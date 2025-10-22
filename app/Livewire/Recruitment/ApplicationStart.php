<?php
// app/Livewire/Recruitment/ApplicationStart.php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ApplicationStart extends Component
{
    use WithFileUploads;

    public Opening $opening;

    // ---------- Always-asked basics ----------
    #[Rule(['required','string','max:120'])]
    public string $candidate_name = '';

    #[Rule(['required','email','max:150'])]
    public string $email = '';

    #[Rule(['nullable','string','max:40'])]
    public ?string $phone = null;

    #[Rule(['nullable','string','max:120'])]
    public ?string $location = null; // city

    #[Rule(['nullable','string','max:60'])]
    public ?string $state = null;    // US state when asked

    // ---------- Screening-aligned (only shown if asked) ----------
    #[Rule(['nullable','numeric','min:0','max:60'])]
    public $years_total = null;

    #[Rule(['nullable','numeric','min:0','max:60'])]
    public $years_med_device = null;

    #[Rule(['array'])]
    public array $specialties = [];

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

    #[Rule(['nullable','file','mimes:pdf,doc,docx','max:10240'])]
    public $cv = null; // 10MB

    // ---------- Options & feature flags ----------
    public array $specialtyOptions = [];
    public array $territoryOptions = [];
    public array $openingTypeOptions = [];
    public array $compStructureOptions = [];
    public array $workAuthOptions = [];

    /** Names of fields requested by employer in this opening (from screening_rules). */
    public array $activeFields = [];

    public function mount(Opening $opening): void
    {
        // Allow only published & visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        $this->opening = $opening;

        // ----- Determine which fields to show from opening->screening_rules
        $rulesRaw = $opening->screening_rules ?? [];
        $rulesArr = is_array($rulesRaw) ? $rulesRaw : collect($rulesRaw)->toArray();
        $this->activeFields = collect($rulesArr)->pluck('field')->filter()->unique()->values()->all();

        // ----- Options
        // specialties from config + anything used in openings
        $specCfg = (array) config('recruitment.specialties', [
            'Cardiology','Dental','Dermatology','Diabetes','ENT','Imaging','Neurology',
            'Oncology','Ophthalmology','Orthopedics','Radiology','Respiratory',
            'Spine','Surgical Instruments','Urology','Wound Care',
        ]);
        $specDb  = collect(\App\Models\Opening::pluck('specialty_ids')->all())
            ->flatMap(fn ($arr) => (array) $arr)->filter();
        $specs   = collect($specCfg)->merge($specDb)->unique()->sort()->values()->all();
        $this->specialtyOptions = collect($specs)->map(fn ($s) => ['label' => $s, 'value' => $s])->all();

        // US states (used if state asked)
        $this->territoryOptions = array_map(
            fn($t) => ['label' => $t, 'value' => $t],
            $this->defaultUsStates()
        );

        // enums for opening type & comp structure (if asked)
        $this->openingTypeOptions   = \App\Enums\OpeningType::options();   // [['label','value'],...]
        $this->compStructureOptions = \App\Enums\CompStructure::options(); // [['label','value'],...]

        $this->workAuthOptions = (array) config('recruitment.work_auth', [
            ['label' => 'U.S. Citizen / Permanent Resident', 'value' => 'citizen_pr'],
            ['label' => 'H-1B',                               'value' => 'h1b'],
            ['label' => 'TN',                                 'value' => 'tn'],
            ['label' => 'OPT/CPT',                            'value' => 'opt_cpt'],
            ['label' => 'Other',                              'value' => 'other'],
        ]);

        // ----- Prefill from user & their latest application (to avoid retyping)
        $user = Auth::user();
        if ($user) {
            $this->candidate_name = $this->candidate_name ?: ($user->name ?? '');
            $this->email          = $this->email ?: ($user->email ?? '');

            $last = Application::where('candidate_user_id', $user->id)
                ->latest('created_at')
                ->first();

            if ($last) {
                $this->phone    = $this->phone    ?: $last->phone;
                if (!$this->location && $last->location) {
                    [$city, $st] = $this->splitCityState((string)$last->location);
                    $this->location = $city ?: $this->location;
                    $this->state    = $this->state ?: ($last->state ?? $st);
                } else {
                    $this->state    = $this->state ?: $last->state;
                }
                $this->cover_letter = $this->cover_letter ?: $last->cover_letter;

                // Also prefill optional answers if they’re being asked again
                if ($this->ask('years_total'))             $this->years_total = $last->years_total;
                if ($this->ask('years_med_device'))        $this->years_med_device = $last->years_med_device;
                if ($this->ask('specialties'))             $this->specialties = (array) $last->candidate_specialties;
                if ($this->ask('travel_percent_max'))      $this->travel_percent_max = $last->travel_percent_max;
                if ($this->ask('overnight_ok'))            $this->overnight_ok = $last->overnight_ok;
                if ($this->ask('driver_license'))          $this->driver_license = $last->driver_license;
                if ($this->ask('opening_type_accepts'))    $this->opening_type_accepts = (array) $last->opening_type_accepts;
                if ($this->ask('comp_structure_accepts'))  $this->comp_structure_accepts = (array) $last->comp_structure_accepts;
                if ($this->ask('expected_base'))           $this->expected_base = $last->expected_base;
                if ($this->ask('expected_ote'))            $this->expected_ote = $last->expected_ote;
                if ($this->ask('cold_outreach_ok'))        $this->cold_outreach_ok = $last->cold_outreach_ok;
                if ($this->ask('work_auth'))               $this->work_auth = $last->work_auth;
                if ($this->ask('start_date'))              $this->start_date = optional($last->start_date)->toDateString();
                if ($this->ask('has_noncompete_conflict')) $this->has_noncompete_conflict = $last->has_noncompete_conflict;
                if ($this->ask('background_check_ok'))     $this->background_check_ok = $last->background_check_ok;
            }
        }
    }

    /** Submit the application — create/update per (candidate_user_id, opening_id). */
    public function submit()
    {
        $this->validate();

        $user = Auth::user();

        // Optional CV upload
        $cvPath = null;
        if ($this->cv) {
            $dir = 'private/cv';
            $filename = uniqid('cv_') . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $this->cv->getClientOriginalName());
            $cvPath = $this->cv->storeAs($dir, $filename, 'local');
        }

        // Only include answers for fields that are actually asked
        $answers = [];
        $put = function(string $key, $value) use (&$answers) {
            $answers[$key] = $value;
        };

        if ($this->ask('years_total'))             $put('years_total', $this->toNumber($this->years_total));
        if ($this->ask('years_med_device'))        $put('years_med_device', $this->toNumber($this->years_med_device));
        if ($this->ask('specialties'))             $put('specialties', array_values($this->specialties ?? []));
        if ($this->ask('state'))                   $put('state', $this->state);
        if ($this->ask('travel_percent_max'))      $put('travel_percent_max', $this->toNumber($this->travel_percent_max));
        if ($this->ask('overnight_ok'))            $put('overnight_ok', $this->toBool($this->overnight_ok));
        if ($this->ask('driver_license'))          $put('driver_license', $this->toBool($this->driver_license));
        if ($this->ask('opening_type_accepts'))    $put('opening_type_accepts', array_values($this->opening_type_accepts ?? []));
        if ($this->ask('comp_structure_accepts'))  $put('comp_structure_accepts', array_values($this->comp_structure_accepts ?? []));
        if ($this->ask('expected_base'))           $put('expected_base', $this->toNumber($this->expected_base));
        if ($this->ask('expected_ote'))            $put('expected_ote', $this->toNumber($this->expected_ote));
        if ($this->ask('cold_outreach_ok'))        $put('cold_outreach_ok', $this->toBool($this->cold_outreach_ok));
        if ($this->ask('work_auth'))               $put('work_auth', $this->work_auth);
        if ($this->ask('start_date'))              $put('start_date', $this->toDate($this->start_date));
        if ($this->ask('has_noncompete_conflict')) $put('has_noncompete_conflict', $this->toBool($this->has_noncompete_conflict));
        if ($this->ask('background_check_ok'))     $put('background_check_ok', $this->toBool($this->background_check_ok));

        // Create or update unique (candidate_user_id, opening_id)
        $application = Application::firstOrNew([
            'opening_id'        => $this->opening->id,
            'candidate_user_id' => $user?->id, // null for guests if allowed
        ]);

        // Basics
        $application->team_id        = $this->opening->team_id;
        $application->candidate_name = $this->candidate_name;
        $application->name           = $this->candidate_name; // legacy column
        $application->email          = $this->email;
        $application->phone          = $this->phone;
        $application->location       = $this->mergeLocation($this->location, $this->state);
        $application->state          = $this->state;
        $application->cover_letter   = $this->cover_letter;
        if ($cvPath) {
            $application->cv_path = $cvPath;
        }
        if (!$application->exists) {
            $application->status = 'new';
        }

        // Persist (only fields that exist in $answers)
        $application->years_total              = $answers['years_total']             ?? null;
        $application->years_med_device         = $answers['years_med_device']        ?? null;
        $application->candidate_specialties    = $answers['specialties']             ?? [];
        $application->travel_percent_max       = $answers['travel_percent_max']      ?? null;
        $application->overnight_ok             = $answers['overnight_ok']            ?? null;
        $application->driver_license           = $answers['driver_license']          ?? null;
        $application->opening_type_accepts     = $answers['opening_type_accepts']    ?? [];
        $application->comp_structure_accepts   = $answers['comp_structure_accepts']  ?? [];
        $application->expected_base            = $answers['expected_base']           ?? null;
        $application->expected_ote             = $answers['expected_ote']            ?? null;
        $application->cold_outreach_ok         = $answers['cold_outreach_ok']        ?? null;
        $application->work_auth                = $answers['work_auth']               ?? null;
        $application->start_date               = $answers['start_date']              ?? null;
        $application->has_noncompete_conflict  = $answers['has_noncompete_conflict'] ?? null;
        $application->background_check_ok      = $answers['background_check_ok']     ?? null;

        // Full snapshot for admin review
        $application->screening_answers = $answers;

        $application->save();

        session()->flash('success', 'Application submitted. Thank you!');
        return redirect()->route('openings.show', $this->opening->slug);
    }

    public function render()
    {
        return view('livewire.recruitment.application-start', [
            'opening' => $this->opening,
        ]);
    }

    // ---------- conditional helpers ----------
    /** Should we ask this field? */
    public function ask(string $field): bool
    {
        return in_array($field, $this->activeFields, true);
    }

    /** Show a section if at least one of these fields is asked. */
    public function asksAny(array $fields): bool
    {
        foreach ($fields as $f) {
            if ($this->ask($f)) return true;
        }
        return false;
    }

    // ---------- misc helpers ----------
    protected function splitCityState(string $location): array
    {
        if (str_contains($location, ',')) {
            [$city, $state] = array_map('trim', explode(',', $location, 2));
            return [$city, $state];
        }
        return [$location, null];
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
}
