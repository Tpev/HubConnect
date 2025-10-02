<?php

namespace App\Livewire\Recruitment;

use App\Enums\CompStructure;
use App\Enums\OpeningType;
use App\Models\Opening;
use Livewire\Component;

abstract class OpeningFormBase extends Component
{
    public ?Opening $opening = null;

    // Core fields
    public string $title = '';
    public string $description = '';
    public string $company_type = 'manufacturer';
    public string $status = 'draft';
    public array  $specialty_ids = [];
    public array  $territory_ids = [];
    public ?string $compensation = null;
    public ?string $visibility_until = null;

    public ?string $comp_structure = null;
    public ?string $opening_type   = null;

    public string $roleplay_policy = 'disabled';
    public $roleplay_scenario_pack_id = null; // untyped to avoid Livewire typed-prop issues
    public $roleplay_pass_threshold   = null; // untyped (input sends string)

    // Screening
    public string $screening_policy = 'off';
    /** @var array<int, array> */
    public array $screening_rules = [];

    // Options
    public array $companyTypeOptions = [];
    public array $statusOptions      = [];
    public array $specialtyOptions   = [];
    public array $territoryOptions   = [];
    public array $roleplayPolicyOptions = [];
    public array $scenarioPackOptions   = [];

    public array $compStructureOptions = [];
    public array $openingTypeOptions   = [];
    public array $screeningPolicyOptions = [];

    public array $screeningFieldOptions = [];

    protected function rules(): array
    {
        return [
            'title'        => ['required','string','max:180'],
            'description'  => ['required','string','max:20000'],
            'company_type' => ['required','in:manufacturer,distributor'],
            'status'       => ['required','in:draft,published,archived'],

            'specialty_ids'=> ['array'],
            'territory_ids'=> ['array'],

            'compensation'     => ['nullable','string','max:255'],
            'visibility_until' => ['nullable','date'],

            'comp_structure'   => ['nullable','in:salary,commission,salary_commission,equities'],
            'opening_type'     => ['nullable','in:w2,1099,contractor,partner'],

            'roleplay_policy'          => ['required','in:disabled,optional,required'],
            'roleplay_scenario_pack_id'=> ['nullable','integer'],
            'roleplay_pass_threshold'  => ['nullable','numeric','min:0','max:100'],

            'screening_policy' => ['required','in:off,soft,hard'],
            'screening_rules'  => ['array'],
        ];
    }

    /** Rebuild options if they were cleared between updates */
    public function hydrate(): void
    {
        if (empty($this->specialtyOptions) || empty($this->territoryOptions)) {
            $this->loadOptions();
        }
    }

    public function loadOptions(): void
    {
        $this->companyTypeOptions = [
            ['label'=>'Manufacturer','value'=>'manufacturer'],
            ['label'=>'Distributor','value'=>'distributor'],
        ];
        $this->statusOptions = [
            ['label'=>'Draft','value'=>'draft'],
            ['label'=>'Published','value'=>'published'],
            ['label'=>'Archived','value'=>'archived'],
        ];
        $this->roleplayPolicyOptions = [
            ['label'=>'Disabled','value'=>'disabled'],
            ['label'=>'Optional','value'=>'optional'],
            ['label'=>'Required','value'=>'required'],
        ];
        $this->scenarioPackOptions = []; // populate if you have packs

        $this->compStructureOptions   = CompStructure::options();
        $this->openingTypeOptions     = OpeningType::options();
        $this->screeningPolicyOptions = \App\Enums\ScreeningPolicy::options();

        // Build specialties/territories from config + DB with a safe fallback
        $cfgSpecs = (array) config('recruitment.specialties', []);
        $cfgTerrs = (array) config('recruitment.territories', []);

        $base = Opening::select(['specialty_ids','territory_ids'])->get();
        $dbSpecs = collect($base)->flatMap(fn($r)=>(array)($r->specialty_ids??[]))->filter();
        $dbTerrs = collect($base)->flatMap(fn($r)=>(array)($r->territory_ids??[]))->filter();

        $specs = collect($cfgSpecs)->merge($dbSpecs)->unique()->sort()->values();
        $terrs = collect($cfgTerrs)->merge($dbTerrs)->unique()->sort()->values();

        // Fallback to US states if still empty
        if ($terrs->isEmpty()) {
            $terrs = collect($this->defaultUsStates());
        }

        $this->specialtyOptions = $specs->map(fn($s)=>['label'=>$s,'value'=>$s])->all();
        $this->territoryOptions = $terrs->map(fn($t)=>['label'=>$t,'value'=>$t])->all();

        // Screening field palette
        $this->screeningFieldOptions = [
            ['label'=>'Total B2B sales experience (years)',   'value'=>'years_total',        'type'=>'number'],
            ['label'=>'Medical device experience (years)',    'value'=>'years_med_device',  'type'=>'number'],
            ['label'=>'Specialty experience',                 'value'=>'specialties',       'type'=>'multiselect'],
            ['label'=>'Based state/territory',                'value'=>'state',             'type'=>'select'],
            ['label'=>'Willing to travel % (max)',            'value'=>'travel_percent_max','type'=>'number'],
            ['label'=>'Overnights OK',                        'value'=>'overnight_ok',      'type'=>'boolean'],
            ['label'=>'Driver license & car',                 'value'=>'driver_license',    'type'=>'boolean'],
            ['label'=>'Open employment types',                'value'=>'opening_type_accepts',   'type'=>'multiselect_enum_ot'],
            ['label'=>'Open comp structures',                 'value'=>'comp_structure_accepts', 'type'=>'multiselect_enum_cs'],
            ['label'=>'Expected base (USD)',                  'value'=>'expected_base',     'type'=>'number_money'],
            ['label'=>'Expected OTE (USD)',                   'value'=>'expected_ote',      'type'=>'number_money'],
            ['label'=>'Comfortable with cold outreach',       'value'=>'cold_outreach_ok',  'type'=>'boolean'],
            ['label'=>'US Work authorization',                'value'=>'work_auth',         'type'=>'select_workauth'],
            ['label'=>'Earliest start date',                  'value'=>'start_date',        'type'=>'date'],
            ['label'=>'Active non-compete conflict',          'value'=>'has_noncompete_conflict', 'type'=>'boolean'],
            ['label'=>'Background check OK',                  'value'=>'background_check_ok','type'=>'boolean'],
        ];
    }

    protected function defaultUsStates(): array
    {
        return [
            'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia',
            'Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine',
            'Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada',
            'New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma',
            'Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont',
            'Virginia','Washington','West Virginia','Wisconsin','Wyoming','Puerto Rico','Guam','U.S. Virgin Islands',
        ];
    }

    protected function fieldMeta(?string $field): array
    {
        $row = collect($this->screeningFieldOptions)->firstWhere('value', $field) ?? [];
        return ['type' => $row['type'] ?? 'text'];
    }

    public function operatorOptions(?string $field): array
    {
        $type = $this->fieldMeta($field)['type'];
        return match ($type) {
            'number', 'number_money' => [
                ['label'=>'≥ (at least)','value'=>'>='],
                ['label'=>'≤ (at most)','value'=>'<='],
                ['label'=>'=','value'=>'eq'],
                ['label'=>'Between','value'=>'between'],
            ],
            'select' => [
                ['label'=>'Is any of','value'=>'in'],
                ['label'=>'Is exactly','value'=>'eq'],
            ],
            'multiselect', 'multiselect_enum_ot', 'multiselect_enum_cs' => [
                ['label'=>'Contains any of','value'=>'contains_any'],
                ['label'=>'Contains all of','value'=>'contains_all'],
            ],
            'boolean' => [
                ['label'=>'Is','value'=>'eq'],
            ],
            'date' => [
                ['label'=>'On or before','value'=>'<='],
                ['label'=>'On or after','value'=>'>='],
            ],
            default => [
                ['label'=>'=','value'=>'eq'],
            ]
        };
    }

    public function valueOptions(?string $field): array
    {
        return match ($field) {
            'specialties' => $this->specialtyOptions,
            'state'       => $this->territoryOptions, // use already-built options
            'opening_type_accepts'   => OpeningType::options(),
            'comp_structure_accepts' => CompStructure::options(),
            'work_auth'              => config('recruitment.work_auth', []),
            default => [],
        };
    }

    public function addRuleRow(): void
    {
        $this->screening_rules[] = [
            'id'       => uniqid('r_'),
            'field'    => null,
            'op'       => null,
            'value'    => null,
            'severity' => 'fail',
            'message'  => null,
        ];
    }

    public function removeRuleRow(int $index): void
    {
        unset($this->screening_rules[$index]);
        $this->screening_rules = array_values($this->screening_rules);
    }

    public function updatedScreeningRules($value, $key): void
    {
        if (str_ends_with($key, '.field')) {
            $i = (int) explode('.', $key)[0];
            $this->screening_rules[$i]['op'] = null;
            $this->screening_rules[$i]['value'] = null;
            unset($this->screening_rules[$i]['min'], $this->screening_rules[$i]['max']);
        }

        if (str_ends_with($key, '.op')) {
            $i = (int) explode('.', $key)[0];
            if (($this->screening_rules[$i]['op'] ?? null) !== 'between') {
                unset($this->screening_rules[$i]['min'], $this->screening_rules[$i]['max']);
            }
        }
    }

    protected function normalizedScreeningRules(): array
    {
        return collect($this->screening_rules)->map(function ($r) {
            $row = [
                'id'       => (string) ($r['id'] ?? uniqid('r_')),
                'field'    => $r['field']    ?? null,
                'op'       => $r['op']       ?? null,
                'severity' => in_array(($r['severity'] ?? 'fail'), ['fail','flag']) ? $r['severity'] : 'fail',
            ];

            if (($r['op'] ?? null) === 'between') {
                $row['min'] = $r['min'] ?? null;
                $row['max'] = $r['max'] ?? null;
            } else {
                $row['value'] = $r['value'] ?? null;
            }

            if (!empty($r['message'])) {
                $row['message'] = (string) $r['message'];
            }

            return $row;
        })
        ->filter(fn($r) => $r['field'] && $r['op'])
        ->values()
        ->all();
    }

    protected function fillFromModel(Opening $opening): void
    {
        $this->opening       = $opening;

        $this->title         = (string) $opening->title;
        $this->description   = (string) $opening->description;
        $this->company_type  = (string) $opening->company_type;
        $this->status        = (string) $opening->status;

        $this->specialty_ids = array_values((array)($opening->specialty_ids ?? []));
        $this->territory_ids = array_values((array)($opening->territory_ids ?? []));

        $this->compensation     = $opening->compensation;
        $this->visibility_until = optional($opening->visibility_until)->toDateString();

        $this->comp_structure = $opening->comp_structure?->value ?? null;
        $this->opening_type   = $opening->opening_type?->value ?? null;

        $this->roleplay_policy           = (string) $opening->roleplay_policy;
        $this->roleplay_scenario_pack_id = $opening->roleplay_scenario_pack_id;
        $this->roleplay_pass_threshold   = $opening->roleplay_pass_threshold;

        $this->screening_policy = $opening->screening_policy?->value
            ?? (string) $opening->screening_policy
            ?? 'off';

        $this->screening_rules  = array_values((array)($opening->screening_rules ?? []));
    }
}
