<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use App\Models\RoleplayScenarioPack;
use App\Models\Specialty;
use App\Models\Territory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.app')]
abstract class OpeningFormBase extends Component
{
    public ?Opening $opening = null;

    #[Rule(['required','string','max:150'])] public string $title = '';
    #[Rule(['nullable','string'])] public string $description = '';
    #[Rule(['required','in:manufacturer,distributor'])] public string $company_type = 'manufacturer';
    #[Rule(['array'])] public array $specialty_ids = [];
    #[Rule(['array'])] public array $territory_ids = [];
    #[Rule(['nullable','string','max:255'])] public ?string $compensation = null;
    #[Rule(['nullable','date'])] public ?string $visibility_until = null;
    #[Rule(['required','in:draft,published,archived'])] public string $status = 'draft';

    #[Rule(['required','in:disabled,optional,required'])] public string $roleplay_policy = 'optional';
    #[Rule(['nullable','integer','exists:roleplay_scenario_packs,id'])] public ?int $roleplay_scenario_pack_id = null;
    #[Rule(['nullable','numeric','min:0','max:99.99'])] public ?float $roleplay_pass_threshold = null;

    public array $companyTypeOptions = [
        ['label' => 'Manufacturer', 'value' => 'manufacturer'],
        ['label' => 'Distributor',  'value' => 'distributor'],
    ];
    public array $statusOptions = [
        ['label' => 'Draft', 'value' => 'draft'],
        ['label' => 'Published', 'value' => 'published'],
        ['label' => 'Archived', 'value' => 'archived'],
    ];
    public array $roleplayPolicyOptions = [
        ['label' => 'Disabled', 'value' => 'disabled'],
        ['label' => 'Optional', 'value' => 'optional'],
        ['label' => 'Required', 'value' => 'required'],
    ];

    public array $specialtyOptions = [];
    public array $territoryOptions = [];
    public array $scenarioPackOptions = [];

    protected function loadOptions(): void
    {
        $this->specialtyOptions = Specialty::query()
            ->orderBy('name')->get(['name'])
            ->map(fn ($s) => ['label' => $s->name, 'value' => $s->name])
            ->values()->all();

        $this->territoryOptions = Territory::query()
            ->orderBy('name')->get(['name'])
            ->map(fn ($t) => ['label' => $t->name, 'value' => $t->name])
            ->values()->all();

        $this->scenarioPackOptions = RoleplayScenarioPack::query()
            ->where('is_active', true)
            ->orderBy('name')->get(['id','name'])
            ->map(fn ($p) => ['label' => $p->name, 'value' => $p->id])
            ->values()->all();
    }

    protected function fillFromModel(Opening $opening): void
    {
        $this->opening                   = $opening;
        $this->title                     = $opening->title ?? '';
        $this->description               = (string) ($opening->description ?? '');
        $this->company_type              = $opening->company_type ?? 'manufacturer';
        $this->specialty_ids             = (array) ($opening->specialty_ids ?? []);
        $this->territory_ids             = (array) ($opening->territory_ids ?? []);
        $this->compensation              = $opening->compensation;
        $this->visibility_until          = optional($opening->visibility_until)->format('Y-m-d');
        $this->status                    = $opening->status ?? 'draft';
        $this->roleplay_policy           = $opening->roleplay_policy ?? 'optional';
        $this->roleplay_scenario_pack_id = $opening->roleplay_scenario_pack_id;
        $this->roleplay_pass_threshold   = $opening->roleplay_pass_threshold;
    }

    public function render()
    {
        return view('livewire.recruitment.opening-form', [
            'scenarioPackOptions'   => $this->scenarioPackOptions,
            'specialtyOptions'      => $this->specialtyOptions,
            'territoryOptions'      => $this->territoryOptions,
            'companyTypeOptions'    => $this->companyTypeOptions,
            'statusOptions'         => $this->statusOptions,
            'roleplayPolicyOptions' => $this->roleplayPolicyOptions,
        ]);
    }
}
