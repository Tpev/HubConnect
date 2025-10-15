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

class SearchBar extends Component
{
    // URL-synced filters so reloads/bookmarks keep state
    #[Url] public ?string $q = null;
    #[Url] public ?string $role = null;       // manufacturer|distributor|both
    #[Url] public ?string $territory = null;  // ISO code
    #[Url] public ?int    $specialty = null;  // Specialty id
    #[Url] public ?int    $cert = null;       // Certification id

    // Hidden (no UI) but kept to keep Results in sync if it relies on them
    #[Url] public string $sort = 'recent';    // recent|name
    #[Url] public int    $perPage = 12;       // 12|24|48

    // Facet counters for the "Role" segmented pills
    public array $facet = ['any' => 0, 'manufacturer' => 0, 'distributor' => 0, 'both' => 0];

    public function mount(): void
    {
        $this->refreshFacets();
        $this->broadcast();
    }

    public function updated($name, $value): void
    {
        // normalize hidden params (even if they have no UI here)
        if ($name === 'sort' && ! in_array($this->sort, ['recent','name'], true)) {
            $this->sort = 'recent';
        }
        if ($name === 'perPage' && ! in_array((int) $this->perPage, [12,24,48], true)) {
            $this->perPage = 12;
        }

        $this->refreshFacets();
        $this->broadcast();
    }

    public function clearFilters(): void
    {
        $this->q         = null;
        $this->role      = null;
        $this->territory = null;
        $this->specialty = null;
        $this->cert      = null;

        // keep sane defaults for hidden params
        $this->sort    = 'recent';
        $this->perPage = 12;

        $this->refreshFacets();
        $this->broadcast();
    }

    /**
     * Base query that applies all current filters (optionally overriding role).
     */
    protected function baseQuery(?string $overrideRole = null): Builder
    {
        $intentUpdatedAt = CompanyIntent::select('updated_at')
            ->whereColumn('company_intents.company_id', 'teams.id')
            ->where('status', 'active')
            ->latest('id')
            ->limit(1);

        $q = Company::query()
            ->select('teams.*')
            ->selectSub($intentUpdatedAt, 'intent_updated_at')
            ->where('is_listed', true)
            ->where('personal_team', 0);

        // Role filter (can be overridden for facet counting)
        $role = $overrideRole ?? $this->role;
        if ($role) {
            $q->where(function (Builder $qq) use ($role) {
                if ($role === 'both') {
                    $qq->where('company_type', 'both');
                } else {
                    $qq->whereIn('company_type', [$role, 'both']);
                }
            });
        }

        // Keyword
        if ($this->q) {
            $term = '%'.$this->q.'%';
            $q->where(function (Builder $qq) use ($term) {
                $qq->where('name', 'like', $term)
                  ->orWhere('summary', 'like', $term)
                  ->orWhere('website', 'like', $term);
            });
        }

        // Specialty (either declared on company or active intent payload)
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

        // Certification
        if ($this->cert) {
            $id = $this->cert;
            $q->whereHas('certifications', fn (Builder $qq) => $qq->where('certifications.id', $id));
        }

        // Territory (in active intents only)
        if ($this->territory) {
            $code = $this->territory;
            $q->whereHas('intents', function (Builder $qq) use ($code) {
                $qq->where('status', 'active')
                   ->whereJsonContains('payload->territories', $code);
            });
        }

        // A default sort (harmless for counts; also used if someone binds to this query elsewhere)
        if ($this->sort === 'name') {
            $q->orderBy('name');
        } else {
            $q->orderBy(DB::raw('COALESCE(intent_updated_at, updated_at)'), 'desc');
        }

        return $q;
    }

    /**
     * Compute role buckets for the segmented pills.
     * Counts reflect current filters EXCEPT role, so users can see availability.
     */
    protected function refreshFacets(): void
    {
        $qNoRole = $this->baseQuery(overrideRole: null);

        $this->facet = [
            'any'          => (clone $qNoRole)->count('teams.id'),
            'manufacturer' => (clone $qNoRole)->whereIn('company_type', ['manufacturer','both'])->count('teams.id'),
            'distributor'  => (clone $qNoRole)->whereIn('company_type', ['distributor','both'])->count('teams.id'),
            'both'         => (clone $qNoRole)->where('company_type', 'both')->count('teams.id'),
        ];
    }

    /**
     * Push the filters to the Results component.
     */
    protected function broadcast(): void
    {
        $this->dispatch('companies:filters', [
            'q'         => $this->q,
            'role'      => $this->role,
            'territory' => $this->territory,
            'specialty' => $this->specialty,
            'cert'      => $this->cert,
            // hidden but useful for Results to stay consistent
            'sort'      => $this->sort,
            'perPage'   => (int) $this->perPage,
        ]);
    }

    public function render()
    {
        return view('livewire.companies.search-bar', [
            'countries'      => config('countries', []),
            'allSpecialties' => Specialty::orderBy('name')->get(['id','name']),
            'allCerts'       => Certification::orderBy('name')->get(['id','name']),
        ]);
    }
}
