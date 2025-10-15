<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Models\CompanyIntent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Results extends Component
{
    use WithPagination;

    // Copy of filters (kept in sync via event), also URL-synced for shareability
    #[Url] public ?string $q = null;
    #[Url] public ?string $role = null;       // manufacturer|distributor|both
    #[Url] public ?string $territory = null;  // ISO code
    #[Url] public ?int    $specialty = null;  // Specialty id
    #[Url] public ?int    $cert = null;       // Certification id
    #[Url] public string  $sort = 'recent';   // recent|name
    #[Url] public int     $perPage = 12;      // 12|24|48

    public function updating($name, $value) { $this->resetPage(); }

    #[On('companies:filters')]
    public function syncFilters(array $filters): void
    {
        // update local props from SearchBar
        $this->q         = $filters['q'] ?? null;
        $this->role      = $filters['role'] ?? null;
        $this->territory = $filters['territory'] ?? null;
        $this->specialty = $filters['specialty'] ?? null;
        $this->cert      = $filters['cert'] ?? null;
        $this->sort      = in_array($filters['sort'] ?? 'recent', ['recent','name'], true) ? $filters['sort'] : 'recent';
        $this->perPage   = in_array((int)($filters['perPage'] ?? 12), [12,24,48], true) ? (int)$filters['perPage'] : 12;

        $this->resetPage();
    }

    protected function baseQuery(): Builder
    {
        $intentUpdatedAt = CompanyIntent::select('updated_at')
            ->whereColumn('company_intents.company_id', 'teams.id')
            ->where('status', 'active')
            ->latest('id')
            ->limit(1);

        $q = Company::query()
            ->with(['specialties:id,name', 'certifications:id,name'])
            ->select('teams.*')
            ->selectSub($intentUpdatedAt, 'intent_updated_at')
            ->where('is_listed', true)
            ->where('personal_team', 0);

        if ($this->role) {
            $role = $this->role;
            $q->where(function (Builder $qq) use ($role) {
                if ($role === 'both') {
                    $qq->where('company_type', 'both');
                } else {
                    $qq->whereIn('company_type', [$role, 'both']);
                }
            });
        }

        if ($this->q) {
            $term = '%'.$this->q.'%';
            $q->where(function (Builder $qq) use ($term) {
                $qq->where('name', 'like', $term)
                   ->orWhere('summary', 'like', $term)
                   ->orWhere('website', 'like', $term);
            });
        }

        if ($this->specialty) {
            $id = $this->specialty;
            $q->where(function (Builder $w) use ($id) {
                $w->whereHas('specialties', fn (Builder $qq) => $qq->where('specialties.id', $id))
                  ->orWhereHas('intents', function (Builder $qq) use ($id) {
                      $qq->where('status', 'active')
                         ->whereJsonContains('payload->specialties', $id);
                  });
            });
        }

        if ($this->cert) {
            $id = $this->cert;
            $q->whereHas('certifications', fn (Builder $qq) => $qq->where('certifications.id', $id));
        }

        if ($this->territory) {
            $code = $this->territory;
            $q->whereHas('intents', function (Builder $qq) use ($code) {
                $qq->where('status','active')
                   ->whereJsonContains('payload->territories', $code);
            });
        }

        if ($this->sort === 'name') {
            $q->orderBy('name');
        } else {
            $q->orderBy(DB::raw('COALESCE(intent_updated_at, updated_at)'), 'desc');
        }

        return $q;
    }

    public function render()
    {
        $companies = $this->baseQuery()
            ->paginate($this->perPage)
            ->withQueryString();

        return view('livewire.companies.results', [
            'companies' => $companies,
            'countries' => config('countries', []),
        ]);
    }
}
