<?php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')] // public page
class ApplicationStart extends Component
{
    use WithFileUploads;

    public Opening $opening;

    // ---------- Contact / basics ----------
    #[Rule(['required','string','max:120'])]
    public string $candidate_name = '';

    #[Rule(['required','email','max:150'])]
    public string $email = '';

    #[Rule(['nullable','string','max:40'])]
    public ?string $phone = null;

    // City (freeform). State is conditional (only if the rule exists)
    #[Rule(['nullable','string','max:120'])]
    public ?string $location = null;

    // ---------- Screening-aligned fields (all optional) ----------
    #[Rule(['nullable','numeric','min:0','max:60'])]
    public $years_total = null;

    #[Rule(['nullable','numeric','min:0','max:60'])]
    public $years_med_device = null;

    #[Rule(['array'])]
    public array $specialties = [];

    #[Rule(['nullable','string','max:60'])]
    public ?string $state = null;

    #[Rule(['nullable','numeric','min:0','max:100'])]
    public $travel_percent_max = null;

    #[Rule(['nullable','boolean'])]
    public ?bool $overnight_ok = null;

    #[Rule(['nullable','boolean'])]
    public ?bool $driver_license = null;

    #[Rule(['array'])]
    public array $opening_type_accepts = []; // enum values

    #[Rule(['array'])]
    public array $comp_structure_accepts = []; // enum values

    #[Rule(['nullable','numeric','min:0','max:10000000'])]
    public $expected_base = null;

    #[Rule(['nullable','numeric','min:0','max:10000000'])]
    public $expected_ote = null;

    #[Rule(['nullable','boolean'])]
    public ?bool $cold_outreach_ok = null;

    #[Rule(['nullable','string','max:60'])]
    public ?string $work_auth = null;

    #[Rule(['nullable','date'])]
    public ?string $start_date = null;

    #[Rule(['nullable','boolean'])]
    public ?bool $has_noncompete_conflict = null;

    #[Rule(['nullable','boolean'])]
    public ?bool $background_check_ok = null;

    // ---------- Cover letter & CV ----------
    #[Rule(['nullable','string','max:5000'])]
    public ?string $cover_letter = null;

    #[Rule(['nullable','file','mimes:pdf,doc,docx','max:10240'])] // 10MB
    public $cv = null;

    // ---------- UI state ----------
    public bool $submitted = false;

    // ---------- Options for selects ----------
    public array $specialtyOptions = [];
    public array $territoryOptions = [];
    public array $openingTypeOptions = [];
    public array $compStructureOptions = [];
    public array $workAuthOptions = [];

    // Which screening fields are active (based on recruiter rules)
    public array $activeFields = [];

    public function mount(Opening $opening): void
    {
        // gate: only public if published & visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        $this->opening = $opening;

        // Determine active fields from rules
        $rulesRaw = $opening->screening_rules ?? [];
        $rules = is_array($rulesRaw) ? $rulesRaw : collect($rulesRaw)->toArray();
        $this->activeFields = collect($rules)->pluck('field')->filter()->unique()->values()->all();

        // specialties = config + any in DB (map to [{label,value}])
        $specCfg = (array) config('recruitment.specialties', []);
        $specDb  = collect(Opening::pluck('specialty_ids')->all())->flatMap(fn ($arr) => (array) $arr)->filter();
        $specs   = collect($specCfg)->merge($specDb)->unique()->sort()->values()->all();
        $this->specialtyOptions = collect($specs)->map(fn ($s) => ['label' => $s, 'value' => $s])->all();

        // territories: config OR fallback to US states, then map to [{label,value}]
        $territories = (array) config('recruitment.territories', []);
        if (empty($territories)) {
            $territories = $this->defaultUsStates(); // array
        }
        $this->territoryOptions = collect($territories)
            ->map(fn ($t) => ['label' => $t, 'value' => $t])
            ->all();

        // enums/options (arrays)
        $this->openingTypeOptions    = \App\Enums\OpeningType::options();
        $this->compStructureOptions  = \App\Enums\CompStructure::options();
        $this->workAuthOptions       = (array) config('recruitment.work_auth', [
            ['label' => 'U.S. Citizen / Permanent Resident', 'value' => 'citizen_pr'],
            ['label' => 'H-1B',                               'value' => 'h1b'],
            ['label' => 'TN',                                 'value' => 'tn'],
            ['label' => 'OPT/CPT',                            'value' => 'opt_cpt'],
            ['label' => 'Other',                              'value' => 'other'],
        ]);

        // US-centric defaults
        $this->phone    = $this->phone ?? '(555) 555-0123';
        $this->location = $this->location ?? 'Austin';
    }


// App/Livewire/Recruitment/ApplicationStart.php
public function submit(): void
{
    $this->validate();

    // store CV privately
    $cvPath = null;
    if ($this->cv) {
        $dir = 'private/cv';
        $filename = uniqid('cv_') . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $this->cv->getClientOriginalName());
        $cvPath = $this->cv->storeAs($dir, $filename, 'local');
    }

    // Build normalized answers (only keys you actually ask on the form)
    $answers = [
        'years_total'              => $this->toNumber($this->years_total),
        'years_med_device'         => $this->toNumber($this->years_med_device),
        'specialties'              => array_values($this->specialties ?? []),
        'state'                    => $this->state,
        'travel_percent_max'       => $this->toNumber($this->travel_percent_max),
        'overnight_ok'             => $this->toBool($this->overnight_ok),
        'driver_license'           => $this->toBool($this->driver_license),
        'opening_type_accepts'     => array_values($this->opening_type_accepts ?? []),
        'comp_structure_accepts'   => array_values($this->comp_structure_accepts ?? []),
        'expected_base'            => $this->toNumber($this->expected_base),
        'expected_ote'             => $this->toNumber($this->expected_ote),
        'cold_outreach_ok'         => $this->toBool($this->cold_outreach_ok),
        'work_auth'                => $this->work_auth,
        'start_date'               => $this->toDate($this->start_date),
        'has_noncompete_conflict'  => $this->toBool($this->has_noncompete_conflict),
        'background_check_ok'      => $this->toBool($this->background_check_ok),
    ];

    // Create app — persist raw answers both as columns and JSON (no verdict here)
    \App\Models\Application::create([
        'team_id'        => $this->opening->team_id,
        'opening_id'     => $this->opening->id,

        'name'           => $this->candidate_name,     // for older schema
        'candidate_name' => $this->candidate_name,
        'email'          => $this->email,
        'phone'          => $this->phone,
        'location'       => $this->mergeLocation($this->location, $this->state),
        'cover_letter'   => $this->cover_letter,
        'cv_path'        => $cvPath,
        'status'         => 'new',

        // persist each answer column so table-side re-eval has data
        'years_total'              => $answers['years_total'],
        'years_med_device'         => $answers['years_med_device'],
        'candidate_specialties'    => $answers['specialties'],  // array cast on model
        'state'                    => $answers['state'],
        'travel_percent_max'       => $answers['travel_percent_max'],
        'overnight_ok'             => $answers['overnight_ok'],
        'driver_license'           => $answers['driver_license'],
        'opening_type_accepts'     => $answers['opening_type_accepts'],
        'comp_structure_accepts'   => $answers['comp_structure_accepts'],
        'expected_base'            => $answers['expected_base'],
        'expected_ote'             => $answers['expected_ote'],
        'cold_outreach_ok'         => $answers['cold_outreach_ok'],
        'work_auth'                => $answers['work_auth'],
        'start_date'               => $answers['start_date'],
        'has_noncompete_conflict'  => $answers['has_noncompete_conflict'],
        'background_check_ok'      => $answers['background_check_ok'],

        // snapshot of all answers (make sure Application::$casts has 'screening_answers' => 'array')
        'screening_answers'        => $answers,
    ]);

    // tidy up UI
    $this->reset([
        'candidate_name','email','phone','location','cover_letter','cv',
        'years_total','years_med_device','specialties','state','travel_percent_max','overnight_ok','driver_license',
        'opening_type_accepts','comp_structure_accepts','expected_base','expected_ote','cold_outreach_ok',
        'work_auth','start_date','has_noncompete_conflict','background_check_ok',
    ]);
    $this->submitted = true;

    $this->dispatch('toast', type: 'success', message: 'Application submitted. Thank you!');
}




    public function render()
    {
        return view('livewire.recruitment.application-start', [
            'opening' => $this->opening,
        ]);
    }

    // ---------- helpers ----------

    /** Should we ask a particular field? */
    public function ask(string $field): bool
    {
        return in_array($field, $this->activeFields, true);
    }

    /** Should we show a section if any of these fields are asked? */
    public function asksAny(array $fields): bool
    {
        foreach ($fields as $f) {
            if ($this->ask($f)) return true;
        }
        return false;
    }

    protected function mergeLocation(?string $city, ?string $state): ?string
    {
        $city = trim((string) ($city ?? ''));
        $state = trim((string) ($state ?? ''));
        if ($city && $state) return "{$city}, {$state}";
        return $city ?: ($state ?: null);
    }

    protected function toNumber($v): ?float
    {
        if ($v === '' || is_null($v)) return null;
        return (float) $v;
    }

    protected function toBool($v): ?bool
    {
        if (is_null($v) || $v === '') return null;
        return filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $v;
    }

    protected function toDate(?string $v): ?string
    {
        if (!$v) return null;
        try {
            return Carbon::parse($v)->toDateString();
        } catch (\Throwable $e) { return null; }
    }

    /**
     * Evaluate recruiter screening rules against candidate answers.
     * Returns [verdict ('pass'|'soft_block'|'hard_block'), failingRules[], flaggingRules[]].
     */
    protected function evaluateScreening(array $rules, string $policy, array $answers): array
    {
        if ($policy === 'off' || empty($rules)) {
            return ['pass', [], []];
        }

        $failing = [];
        $flagging = [];

        foreach ($rules as $row) {
            $field = $row['field'] ?? null;
            $op    = $row['op'] ?? null;
            $sev   = $row['severity'] ?? 'fail'; // 'fail' | 'flag'
            if (!$field || !$op) continue;

            $candidate = $answers[$field] ?? null;
            $passed = $this->compare($candidate, $op, $row);

            if (!$passed) {
                if ($sev === 'fail') $failing[] = $row;
                else $flagging[] = $row;
            }
        }

        if (!empty($failing) && $policy === 'hard') {
            return ['hard_block', $failing, $flagging];
        }

        if (!empty($failing) && $policy === 'soft') {
            return ['soft_block', $failing, $flagging];
        }

        return ['pass', [], $flagging];
    }

    /**
     * Compare one candidate value vs a rule op.
     * Supports ops: >=, <=, eq, between, in, contains_any, contains_all
     */
    protected function compare($candidate, string $op, array $rule): bool
    {
        switch ($op) {
            case '>=':
                return $this->numeric($candidate) >= $this->numeric($rule['value'] ?? $rule['min'] ?? null);
            case '<=':
                return $this->numeric($candidate) <= $this->numeric($rule['value'] ?? $rule['max'] ?? null);
            case 'eq':
                // dates & strings fall back to string compare
                if ($this->isDateLike($candidate) || $this->isDateLike($rule['value'] ?? null)) {
                    return $this->toDate((string)$candidate) === $this->toDate((string)($rule['value'] ?? null));
                }
                return (string) $candidate === (string) ($rule['value'] ?? '');
            case 'between':
                $min = $this->numeric($rule['min'] ?? null);
                $max = $this->numeric($rule['max'] ?? null);
                $val = $this->numeric($candidate);
                if (!is_null($min) && $val < $min) return false;
                if (!is_null($max) && $val > $max) return false;
                return true;
            case 'in':
                $set = (array) ($rule['value'] ?? []);
                return in_array($candidate, $set, true);
            case 'contains_any':
                $cand = collect((array) $candidate);
                $set  = collect((array) ($rule['value'] ?? []));
                return $cand->intersect($set)->isNotEmpty();
            case 'contains_all':
                $cand = collect((array) $candidate);
                $set  = collect((array) ($rule['value'] ?? []));
                return $set->every(fn ($v) => $cand->contains($v));
            default:
                return true;
        }
    }

    protected function numeric($v): float
    {
        if (is_null($v) || $v === '') return 0.0;
        return (float) $v;
    }

    protected function isDateLike($v): bool
    {
        if (!$v) return false;
        try { Carbon::parse((string) $v); return true; }
        catch (\Throwable $e) { return false; }
    }

    /** Fallback US states (names). */
    protected function defaultUsStates(): array
    {
        return [
            'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware',
            'Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky',
            'Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi',
            'Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico',
            'New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania',
            'Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont',
            'Virginia','Washington','West Virginia','Wisconsin','Wyoming',
        ];
    }

    /** Normalize enum|scalar into a string value (e.g., 'off'|'soft'|'hard'). */
    protected function enumValue(mixed $v, mixed $fallback = null): mixed
    {
        if ($v instanceof \BackedEnum) return $v->value;
        if ($v instanceof \UnitEnum)  return $v->name ?? $fallback;
        return $v ?? $fallback;
    }
}
