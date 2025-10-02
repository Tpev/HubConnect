<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Models\CompanyIntent;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class IntentEditor extends Component
{
    use AuthorizesRequests;

    public Company $company;

    // Form fields
    public array   $territories = [];            // ISO codes from config('countries')
    public array   $specialties = [];            // Specialty IDs
    public ?bool   $pref_exclusivity = null;
    public ?bool   $pref_consignment = null;
    public ?int    $pref_commission_min = null;  // 0..100
    public ?string $capacity_note = null;
    public ?string $urgency = null;
    public ?string $notes = null;

    public function mount(Company $company): void
    {
        $this->authorize('update', $company);
        $this->company = $company;

        // Hydrate from latest active intent (if any)
        if ($intent = $company->activeIntent()) {
            $p = $intent->payload ?? [];
            $this->territories         = array_values($p['territories'] ?? []);
            $this->specialties         = array_values($p['specialties'] ?? []);
            $this->pref_exclusivity    = $p['deal']['exclusivity']    ?? null;
            $this->pref_consignment    = $p['deal']['consignment']    ?? null;
            $this->pref_commission_min = $p['deal']['commission_min'] ?? null;
            $this->capacity_note       = $p['capacity_note'] ?? null;
            $this->urgency             = $p['urgency'] ?? null;
            $this->notes               = $p['notes'] ?? null;
        }
    }

    protected function rules(): array
    {
        // Build allowed ISO codes from config only (no symfony/intl)
        $allowedIso = collect(config('countries', []))->pluck('value')->all();

        return [
            'territories'            => ['array'],
            'territories.*'          => ['string', Rule::in($allowedIso)],
            'specialties'            => ['array'],
            'specialties.*'          => ['integer', Rule::exists('specialties', 'id')],
            'pref_exclusivity'       => ['nullable', 'boolean'],
            'pref_consignment'       => ['nullable', 'boolean'],
            'pref_commission_min'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'capacity_note'          => ['nullable', 'string', 'max:1000'],
            'urgency'                => ['nullable', 'string', 'max:200'],
            'notes'                  => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->company);

        $this->validate();

        // Normalize/clean
        $territories = array_values(array_unique(array_filter($this->territories, fn($v) => filled($v))));
        $specialties = array_values(array_unique(array_filter($this->specialties, fn($v) => filled($v))));

        // Optional: make sure specialties actually exist (extra safety even with validation)
        /** @var Collection<int,\App\Models\Specialty> $existing */
        $existing = Specialty::whereIn('id', $specialties)->pluck('id');
        $specialties = $existing->all();

        // Coerce boolean-ish values (Livewire can pass "true"/"false")
        $excl = is_null($this->pref_exclusivity) ? null : (bool) $this->pref_exclusivity;
        $cons = is_null($this->pref_consignment) ? null : (bool) $this->pref_consignment;

        // Archive previous active intents
        $this->company->intents()
            ->where('status', 'active')
            ->update([
                'status'        => 'archived',
                'effective_to'  => now(),
                'updated_at'    => now(),
            ]);

        // New active payload
        $payload = [
            'territories'  => $territories,
            'specialties'  => $specialties,
            'deal' => [
                'exclusivity'    => $excl,
                'consignment'    => $cons,
                'commission_min' => $this->pref_commission_min,
            ],
            'capacity_note' => $this->capacity_note,
            'urgency'       => $this->urgency,
            'notes'         => $this->notes,
        ];

        CompanyIntent::create([
            'company_id'     => $this->company->id,
            'intent_type'    => 'looking_for',
            'status'         => 'active',
            'payload'        => $payload,
            'effective_from' => now(),
        ]);

        session()->flash('saved', 'Looking For updated.');
        $this->dispatch('intent-saved');
    }

    public function render()
    {
        return view('livewire.companies.intent-editor', [
            'allSpecialties' => Specialty::orderBy('name')->get(['id','name']),
            // Countries from config only
            'countries'      => config('countries', []),
        ])->title('Update “Looking For”');
    }
}
