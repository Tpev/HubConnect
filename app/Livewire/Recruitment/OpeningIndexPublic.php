<?php

namespace App\Livewire\Recruitment;

use App\Enums\CompStructure;
use App\Enums\OpeningType;
use App\Models\Application;
use App\Models\Opening;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')] // use app layout
class OpeningIndexPublic extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $companyType = 'all'; // all|manufacturer|distributor

    #[Url]
    public ?string $specialty = null;   // label from specialty_ids
    #[Url]
    public ?string $territory = null;   // label from territory_ids

    // Nullable to avoid Livewire clearable -> null crash
    #[Url]
    public ?string $sort = 'newest';     // newest|title|closing
    #[Url]
    public ?int $perPage = 12;

    // Filters
    #[Url]
    public ?string $compStructure = null; // salary|commission|salary_commission|equities
    #[Url]
    public ?string $openingType   = null; // w2|1099|contractor|partner

    public array $specialtyOptions = [];
    public array $territoryOptions = [];

    public array $sortOptions = [
        ['label' => 'Newest',       'value' => 'newest'],
        ['label' => 'Title (A→Z)',  'value' => 'title'],
        ['label' => 'Closing soon', 'value' => 'closing'],
    ];

    public array $perPageOptions = [
        ['label' => '12', 'value' => 12],
        ['label' => '24', 'value' => 24],
        ['label' => '48', 'value' => 48],
    ];

    // fixed options from enums
    public array $compStructureOptions = [];
    public array $openingTypeOptions   = [];

    // viewer context
    public string $viewerType = 'guest'; // guest|individual|company

    /**
     * IDs of openings the current user (individual) already applied to.
     * Used only for UI (to hide/disable Apply).
     *
     * @var array<int,int>
     */
    public array $appliedOpeningIds = [];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'isIndividual') && $user->isIndividual()) {
            $this->viewerType = 'individual';

            // Preload the set of openings this user applied to
            $this->appliedOpeningIds = Application::query()
                ->where('candidate_user_id', $user->id)
                ->pluck('opening_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        } elseif ($user) {
            $this->viewerType = 'company';
        }

        // Build facet options from currently visible (published) openings
        $base = Opening::query()
            ->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('visibility_until')
                  ->orWhere('visibility_until', '>=', now());
            })
            ->get(['specialty_ids', 'territory_ids']);

        $specs = collect($base)->flatMap(fn ($row) => (array) ($row->specialty_ids ?? []))
            ->filter()->unique()->sort()->values();

        $terrs = collect($base)->flatMap(fn ($row) => (array) ($row->territory_ids ?? []))
            ->filter()->unique()->sort()->values();

        $this->specialtyOptions = $specs->map(fn ($s) => ['label' => $s, 'value' => $s])->all();
        $this->territoryOptions = $terrs->map(fn ($t) => ['label' => $t, 'value' => $t])->all();

        // Enum options
        $this->compStructureOptions = CompStructure::options();
        $this->openingTypeOptions   = OpeningType::options();

        // Coerce defaults if query-string cleared them
        $this->sort    = $this->sort    ?: 'newest';
        $this->perPage = $this->perPage ?: 12;
    }

    // Reset page on filter changes
    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingCompanyType(): void  { $this->resetPage(); }
    public function updatingSpecialty(): void    { $this->resetPage(); }
    public function updatingTerritory(): void    { $this->resetPage(); }
    public function updatingSort(): void         { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }
    public function updatingCompStructure(): void{ $this->resetPage(); }
    public function updatingOpeningType(): void  { $this->resetPage(); }

    // Coerce clears to sane defaults
    public function updatedSort($v): void    { $this->sort    = $v ?: 'newest'; }
    public function updatedPerPage($v): void { $this->perPage = (int) ($v ?: 12); }

    protected function baseScope(): Builder
    {
        return Opening::query()
            ->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('visibility_until')
                  ->orWhere('visibility_until', '>=', now());
            });
    }

    protected function query(): Builder
    {
        $q = $this->baseScope();

        if ($this->search !== '') {
            $like = '%' . trim($this->search) . '%';
            $q->where(function (Builder $w) use ($like) {
                $w->where('title', 'like', $like)
                  ->orWhere('description', 'like', $like)
                  ->orWhere('compensation', 'like', $like);
            });
        }

        if ($this->companyType !== 'all') {
            $q->where('company_type', $this->companyType);
        }

        if ($this->specialty) {
            $q->whereJsonContains('specialty_ids', $this->specialty);
        }

        if ($this->territory) {
            $q->whereJsonContains('territory_ids', $this->territory);
        }

        if ($this->compStructure) {
            $q->where('comp_structure', $this->compStructure);
        }

        if ($this->openingType) {
            $q->where('opening_type', $this->openingType);
        }

        $sort = $this->sort ?: 'newest';

        $q->when($sort === 'newest',  fn($qq) => $qq->orderByDesc('created_at'))
          ->when($sort === 'title',   fn($qq) => $qq->orderBy('title'))
          ->when($sort === 'closing', fn($qq) => $qq
                ->orderByRaw('visibility_until is null desc')
                ->orderBy('visibility_until'));

        return $q->select([
            'id','slug','title','description','company_type',
            'specialty_ids','territory_ids','compensation',
            'comp_structure','opening_type',
            'visibility_until','created_at',
        ]);
    }

    public function render()
    {
        $openings = $this->query()->paginate($this->perPage ?: 12);

        return view('livewire.recruitment.opening-index-public', [
            'openings'             => $openings,
            'specialtyOptions'     => $this->specialtyOptions,
            'territoryOptions'     => $this->territoryOptions,
            'sortOptions'          => $this->sortOptions,
            'perPageOptions'       => $this->perPageOptions,
            'compStructureOptions' => $this->compStructureOptions,
            'openingTypeOptions'   => $this->openingTypeOptions,
            'viewerType'           => $this->viewerType,
            'appliedOpeningIds'    => $this->appliedOpeningIds,
        ]);
    }
}
