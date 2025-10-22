<?php

namespace App\Livewire\Recruitment;

use App\Enums\CompStructure;
use App\Enums\OpeningType;
use App\Models\Opening;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

    // Roleplay
    public string $roleplay_policy = 'disabled';
    public $roleplay_scenario_pack_id = null;
    public $roleplay_pass_threshold   = null;

    // Screening
    public string $screening_policy = 'off';
    /** @var array<int,array> */
    public array $screening_rules = [];   // rows with: id, field, op, value|min/max, severity, message
    public array $ruleIndexByField = [];  // field => index

    // Location omnibox binding
    public array $location_chips = [];

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

    public array $screeningFieldOptions = []; // [{label,value,type}]
    public array $screeningFieldTypeMap = []; // value => type

    // Per-field constraints
    protected array $ruleConstraints = [
        'travel_percent_max' => ['type'=>'percent', 'min'=>0, 'max'=>100],
        'years_total'        => ['type'=>'number',  'min'=>0, 'max'=>60],
        'years_med_device'   => ['type'=>'number',  'min'=>0, 'max'=>60],
        'expected_base'      => ['type'=>'money',   'min'=>0, 'max'=>1_000_000],
        'expected_ote'       => ['type'=>'money',   'min'=>0, 'max'=>2_000_000],
        'start_date'         => ['type'=>'date'],
    ];

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

    /** Cache options, index rules, coerce values. */
    public function hydrate(): void
    {
        if (empty($this->specialtyOptions) || empty($this->territoryOptions) || empty($this->screeningFieldOptions)) {
            $this->loadOptions();
        }
        $this->rebuildRuleIndex();
        $this->coerceAllRuleValues();
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
        $this->scenarioPackOptions = [];

        $this->compStructureOptions   = CompStructure::options();
        $this->openingTypeOptions     = OpeningType::options();
        $this->screeningPolicyOptions = \App\Enums\ScreeningPolicy::options();

        // specialties/territories
        $cfgSpecs = (array) config('recruitment.specialties', []);
        $cfgTerrs = (array) config('recruitment.territories', []);

        $base = Opening::select(['specialty_ids','territory_ids'])->get();
        $dbSpecs = collect($base)->flatMap(fn($r)=>(array)($r->specialty_ids??[]))->filter();
        $dbTerrs = collect($base)->flatMap(fn($r)=>(array)($r->territory_ids??[]))->filter();

        $specs = collect($cfgSpecs)->merge($dbSpecs)->unique()->sort()->values();
        $terrs = collect($cfgTerrs)->merge($dbTerrs)->unique()->sort()->values();
        if ($terrs->isEmpty()) $terrs = collect($this->defaultUsStates());

        $this->specialtyOptions = $specs->map(fn($s)=>['label'=>$s,'value'=>$s])->all();
        $this->territoryOptions = $terrs->map(fn($t)=>['label'=>$t,'value'=>$t])->all();

        // IMPORTANT: make "state" a MULTISELECT so it behaves like opening_type_accepts
        $this->screeningFieldOptions = [
            ['label'=>'Total B2B sales experience (years)',   'value'=>'years_total',        'type'=>'number'],
            ['label'=>'Medical device experience (years)',    'value'=>'years_med_device',  'type'=>'number'],
            ['label'=>'Specialty experience',                 'value'=>'specialties',       'type'=>'multiselect'],
            ['label'=>'Based state/territory',                'value'=>'state',             'type'=>'multiselect'], // <— changed
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

        $this->screeningFieldTypeMap = collect($this->screeningFieldOptions)
            ->mapWithKeys(fn($r)=>[$r['value'] => $r['type']])->all();
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
        $type = $this->screeningFieldTypeMap[$field] ?? 'text';
        return ['type' => $type];
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
            // "state" is multiselect now, so it will use these:
            'multiselect', 'multiselect_enum_ot', 'multiselect_enum_cs' => [
                ['label'=>'Contains any of','value'=>'contains_any'],
                ['label'=>'Contains all of','value'=>'contains_all'],
            ],
            'select' => [
                ['label'=>'Is any of','value'=>'in'],
                ['label'=>'Is exactly','value'=>'eq'],
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
            'state'       => $this->territoryOptions, // used by multiselect now
            'opening_type_accepts'   => OpeningType::options(),
            'comp_structure_accepts' => CompStructure::options(),
            'work_auth'              => (array) config('recruitment.work_auth', []),
            default => [],
        };
    }

    /** field => rule index */
    protected function rebuildRuleIndex(): void
    {
        $map = [];
        foreach ($this->screening_rules as $i => $r) {
            $f = $r['field'] ?? null;
            if ($f && !array_key_exists($f, $map)) $map[$f] = (int) $i;
        }
        $this->ruleIndexByField = $map;
    }

    /** Wire: checkbox toggled */
    public function toggleField(string $field, bool $enabled): void
    {
        if ($enabled) {
            $i = $this->ensureRuleForField($field);
            $this->coerceRuleRowValues($i);
        } else {
            if (array_key_exists($field, $this->ruleIndexByField)) {
                $idx = (int) $this->ruleIndexByField[$field];
                unset($this->screening_rules[$idx]);
                $this->screening_rules = array_values($this->screening_rules);
                $this->rebuildRuleIndex();
            }
        }
    }

    /** Ensure rule row exists for field; return its index. */
    protected function ensureRuleForField(string $field): int
    {
        if (array_key_exists($field, $this->ruleIndexByField)) {
            return (int) $this->ruleIndexByField[$field];
        }

        $type = $this->screeningFieldTypeMap[$field] ?? 'text';
        $row = [
            'id'       => (string) Str::uuid(),
            'field'    => $field,
            'op'       => match ($type) {
                'number','number_money' => '>=',
                // multiselect defaults to contains_any — same as opening_type_accepts
                'multiselect','multiselect_enum_ot','multiselect_enum_cs' => 'contains_any',
                'select','select_workauth' => 'eq',
                'boolean' => 'eq',
                'date' => '>=',
                default => 'eq',
            },
            'severity' => 'fail',
            'message'  => null,
            'value'    => match(true) {
                $type === 'boolean' => true,
                in_array($type, ['multiselect','multiselect_enum_ot','multiselect_enum_cs']) => [],
                default => null,
            },
        ];

        $this->screening_rules[] = $row;
        $this->rebuildRuleIndex();
        return (int) $this->ruleIndexByField[$field];
    }

    public function onOperatorChange(int $i): void
    {
        if (!isset($this->screening_rules[$i])) return;
        $op = $this->screening_rules[$i]['op'] ?? null;

        if ($op === 'between') {
            $this->screening_rules[$i]['min'] = null;
            $this->screening_rules[$i]['max'] = null;
            unset($this->screening_rules[$i]['value']);
        } else {
            $this->screening_rules[$i]['value'] = $this->screening_rules[$i]['value'] ?? null;
            unset($this->screening_rules[$i]['min'], $this->screening_rules[$i]['max']);
        }

        $this->coerceRuleRowValues($i);
    }

    public function validateRuleRow(int $i): array
    {
        return $this->validateRow($i, preview: true);
    }

    protected function coerceAllRuleValues(): void
    {
        foreach (array_keys($this->screening_rules) as $i) {
            $this->coerceRuleRowValues((int) $i);
        }
    }

    protected function coerceRuleRowValues(int $i): void
    {
        $row  = $this->screening_rules[$i] ?? [];
        $type = $this->fieldMeta($row['field'] ?? null)['type'] ?? 'text';

        $coerceBool = function ($v) {
            if (is_bool($v)) return $v;
            if (is_int($v)) return $v === 1;
            if (is_string($v)) {
                $v = strtolower($v);
                return in_array($v, ['1','true','yes','y'], true);
            }
            return (bool) $v;
        };
        $coerceNum = function ($v) {
            if ($v === '' || $v === null) return null;
            return is_numeric($v) ? 0 + $v : null;
        };

        // If type moved to multiselect (state), normalize legacy ops eq/in => contains_any
        if (in_array($type, ['multiselect','multiselect_enum_ot','multiselect_enum_cs'], true)) {
            if (!in_array(($row['op'] ?? null), ['contains_any','contains_all'], true)) {
                $row['op'] = 'contains_any';
            }
        }

        if (($row['op'] ?? null) === 'between') {
            $row['min'] = $coerceNum($row['min'] ?? null);
            $row['max'] = $coerceNum($row['max'] ?? null);
            unset($row['value']);
        } else {
            if ($type === 'boolean') {
                $row['value'] = $coerceBool($row['value'] ?? false);
            } elseif (in_array($type, ['number', 'number_money'])) {
                $row['value'] = $coerceNum($row['value'] ?? null);
            } elseif (in_array($type, ['multiselect', 'multiselect_enum_ot', 'multiselect_enum_cs'])) {
                $v = $row['value'] ?? [];
                $row['value'] = is_array($v) ? array_values($v) : ($v !== null ? [$v] : []);
            } elseif ($type === 'select' || $type === 'select_workauth') {
                $v = $row['value'] ?? (($row['op'] ?? null) === 'in' ? [] : null);
                if (($row['op'] ?? null) === 'in') {
                    $row['value'] = is_array($v) ? array_values($v) : ($v !== null ? [$v] : []);
                } else {
                    $row['value'] = is_array($v) ? (count($v) ? $v[0] : null) : $v;
                }
            } else {
                $row['value'] = $row['value'] ?? null;
            }
            unset($row['min'], $row['max']);
        }

        $row['severity'] = in_array(($row['severity'] ?? 'fail'), ['fail','flag'], true) ? $row['severity'] : 'fail';
        $this->screening_rules[$i] = $row;
    }

    protected function validateRow(int $i, bool $preview = false): array
    {
        $messages = [];
        $r = $this->screening_rules[$i] ?? [];
        $field = $r['field'] ?? null;
        $op    = $r['op'] ?? null;
        if (!$field) $messages['field'] = 'Choose a field.';
        if (!$op)    $messages['op']    = 'Choose an operator.';

        $type = $this->screeningFieldTypeMap[$field] ?? 'text';
        $constraints = $this->ruleConstraints[$field] ?? null;
        $mustUseRange = ($op === 'between');

        if ($mustUseRange) {
            $min = Arr::get($r, 'min');
            $max = Arr::get($r, 'max');
            if ($min === null || $max === null) {
                $messages['minmax'] = 'Both min and max are required.';
            } elseif (!is_numeric($min) || !is_numeric($max)) {
                $messages['minmax'] = 'Min/Max must be numbers.';
            } elseif ($min > $max) {
                $messages['minmax'] = 'Min must be ≤ Max.';
            }
            if ($constraints) {
                if (is_numeric($min) && isset($constraints['min']) && $min < $constraints['min']) $messages['min'] = "Min must be ≥ {$constraints['min']}.";
                if (is_numeric($max) && isset($constraints['max']) && $max > $constraints['max']) $messages['max'] = "Max must be ≤ {$constraints['max']}.";
            }
        } else {
            if ($type === 'boolean') {
                if (!array_key_exists('value', $r)) $messages['value'] = 'Select yes or no.';
            } elseif (in_array($type, ['multiselect','multiselect_enum_ot','multiselect_enum_cs'])) {
                $v = Arr::get($r, 'value', []);
                if (!is_array($v) || empty($v)) $messages['value'] = 'Choose at least one value.';
            } elseif ($type === 'select' || $type === 'select_workauth') {
                $v = Arr::get($r, 'value');
                if ($op === 'in') {
                    if (!is_array($v) || empty($v)) $messages['value'] = 'Choose at least one value.';
                } else {
                    if ($v === null || $v === '') $messages['value'] = 'Choose a value.';
                }
            } elseif ($type === 'date') {
                $v = Arr::get($r, 'value');
                if (!$v || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $v)) $messages['value'] = 'Provide a valid date (YYYY-MM-DD).';
            } elseif ($type === 'number' || $type === 'number_money') {
                $v = Arr::get($r, 'value');
                if ($v === null || $v === '' || !is_numeric($v)) $messages['value'] = 'Provide a valid number.';
            } else {
                $v = Arr::get($r, 'value');
                if ($v === null || $v === '') $messages['value'] = 'Provide a value.';
            }
            if ($constraints) {
                $v = Arr::get($r, 'value');
                if (is_numeric($v)) {
                    if (isset($constraints['min']) && $v < $constraints['min']) $messages['value_min'] = "Value must be ≥ {$constraints['min']}.";
                    if (isset($constraints['max']) && $v > $constraints['max']) $messages['value_max'] = "Value must be ≤ {$constraints['max']}.";
                }
            }
        }

        if ($preview) return $messages;

        foreach ($messages as $k => $msg) {
            $key = match ($k) {
                'field'  => "screening_rules.$i.field",
                'op'     => "screening_rules.$i.op",
                'value', 'value_min', 'value_max' => "screening_rules.$i.value",
                'min', 'max', 'minmax' => "screening_rules.$i.min",
                default  => "screening_rules.$i.message",
            };
            $this->addError($key, $msg);
            if ($k === 'minmax') $this->addError("screening_rules.$i.max", $msg);
        }

        return $messages;
    }

    protected function validateAllRules(): void
    {
        foreach (array_keys($this->screening_rules) as $i) {
            $this->validateRow((int) $i, preview: false);
        }
    }

    protected function normalizedScreeningRules(): array
    {
        return collect($this->screening_rules)->map(function ($r) {
            $row = [
                'id'       => (string) ($r['id'] ?? Str::uuid()),
                'field'    => $r['field']    ?? null,
                'op'       => $r['op']       ?? null,
                'severity' => in_array(($r['severity'] ?? 'fail'), ['fail','flag'], true) ? $r['severity'] : 'fail',
            ];

            if (($r['op'] ?? null) === 'between') {
                $row['min'] = Arr::get($r, 'min');
                $row['max'] = Arr::get($r, 'max');
            } else {
                $row['value'] = Arr::get($r, 'value');
            }

            if (!empty($r['message'])) $row['message'] = (string) $r['message'];

            return $row;
        })
        ->filter(fn($r) => $r['field'] && $r['op'])
        ->values()
        ->all();
    }

    protected function chipsToTerritories(array $chips): array
    {
        return collect($chips)
            ->map(fn($c) => $c['code'] ?? $c['value'] ?? $c['label'] ?? null)
            ->filter()->unique()->values()->all();
    }

    /** Single source of truth for create/update payloads */
    protected function preparedForPersist(): array
    {
        $this->validate();
        $this->coerceAllRuleValues();
        $this->validateAllRules();

        $territories = $this->territory_ids;
        if (!empty($this->location_chips)) {
            $territories = $this->chipsToTerritories($this->location_chips);
        }

        return [
            'title'         => $this->title,
            'slug'          => null,
            'description'   => $this->description,
            'company_type'  => $this->company_type,
            'status'        => $this->status,

            'specialty_ids' => array_values($this->specialty_ids ?? []),
            'territory_ids' => array_values($territories ?? []),

            'compensation'     => $this->compensation,
            'visibility_until' => $this->visibility_until ? Carbon::parse($this->visibility_until)->startOfDay() : null,

            'comp_structure' => $this->comp_structure ?: null,
            'opening_type'   => $this->opening_type   ?: null,

            'roleplay_policy'           => $this->roleplay_policy,
            'roleplay_scenario_pack_id' => $this->roleplay_scenario_pack_id,
            'roleplay_pass_threshold'   => $this->roleplay_pass_threshold,

            'screening_policy' => $this->screening_policy ?: 'off',
            'screening_rules'  => $this->normalizedScreeningRules(),
        ];
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
        $this->rebuildRuleIndex();
        $this->coerceAllRuleValues();
    }
}
