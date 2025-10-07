<?php

namespace App\Livewire\Companies;

use App\Models\Certification;
use App\Models\Company;
use App\Models\CompanyIntent;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public ?string $role = null;       // manufacturer|distributor|both
    #[Url] public ?string $territory = null;  // ISO code
    #[Url] public ?int    $specialty = null;  // Specialty id
    #[Url] public ?int    $cert = null;       // Certification id
    #[Url] public string  $sort = 'recent';   // recent|name

    public function updating($name, $value) { $this->resetPage(); }

    public function render()
    {
        // Always use config for countries (no symfony/intl)
        $countries = config('countries', []);

        // subquery for latest active intent updated_at for ordering
        $intentUpdatedAt = CompanyIntent::select('updated_at')
            ->whereColumn('company_intents.company_id', 'teams.id')
            ->where('status', 'active')
            ->latest('id')
            ->limit(1);

        $companies = Company::query()
            ->with(['specialties:id,name', 'certifications:id,name'])
            ->select('teams.*')
            ->selectSub($intentUpdatedAt, 'intent_updated_at')
			->where('is_listed', true)

            // Role filter
            ->when($this->role, function (Builder $q) {
                $q->where(function (Builder $qq) {
                    if ($this->role === 'both') {
                        $qq->where('company_type', 'both');
                    } else {
                        $qq->whereIn('company_type', [$this->role, 'both']);
                    }
                });
            })

            // Keyword
            ->when($this->q, function (Builder $q) {
                $term = '%'.$this->q.'%';
                $q->where(function (Builder $qq) use ($term) {
                    $qq->where('name', 'like', $term)
                       ->orWhere('summary', 'like', $term)
                       ->orWhere('website', 'like', $term);
                });
            })

            // Specialty (group the OR conditions!)
            ->when($this->specialty, function (Builder $q) {
                $id = $this->specialty;
                $q->where(function (Builder $w) use ($id) {
                    $w->whereHas('specialties', fn (Builder $qq) => $qq->where('specialties.id', $id))
                      ->orWhereHas('intents', function (Builder $qq) use ($id) {
                          $qq->where('status', 'active')
                             ->whereJsonContains('payload->specialties', $id);
                      });
                });
            })

            // Certification
            ->when($this->cert, function (Builder $q) {
                $id = $this->cert;
                $q->whereHas('certifications', fn (Builder $qq) => $qq->where('certifications.id', $id));
            })

            // Territory — only in active intents
            ->when($this->territory, function (Builder $q) {
                $code = $this->territory;
                $q->whereHas('intents', function (Builder $qq) use ($code) {
                    $qq->where('status','active')
                       ->whereJsonContains('payload->territories', $code);
                });
            })

            // Hide personal teams if any slipped through
            ->where('personal_team', 0)

            // Sorting
            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'recent', fn ($q) =>
                $q->orderBy(DB::raw('COALESCE(intent_updated_at, updated_at)'), 'desc')
            )

            ->paginate(12)
            ->withQueryString();

        return view('livewire.companies.index', [
            'companies'      => $companies,
            'allSpecialties' => Specialty::orderBy('name')->get(['id','name']),
            'allCerts'       => Certification::orderBy('name')->get(['id','name']),
            'countries'      => $countries, // config-based
        ])->title('Companies')->layout('layouts.app');
    }
}
