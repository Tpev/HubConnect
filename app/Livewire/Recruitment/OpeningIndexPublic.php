<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.guest')] // public/guest layout
class OpeningIndexPublic extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $companyType = 'all'; // all|manufacturer|distributor

    #[Url]
    public ?string $specialty = null;   // label string from specialty_ids

    #[Url]
    public ?string $territory = null;   // label string from territory_ids

    // NOTE: nullable to avoid Livewire unsetting on clear; we coerce to default below
    #[Url]
    public ?string $sort = 'newest';     // newest|title|closing

    // NOTE: nullable for same reason; coerce to default
    #[Url]
    public ?int $perPage = 12;

    public array $specialtyOptions = [];
    public array $territoryOptions = [];

    // TallStack styled select options
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

    public function mount(): void
    {
        // Build facet options from currently visible (published) openings
        $base = Opening::query()
            ->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('visibility_until')
                  ->orWhere('visibility_until', '>=', now());
            })
            ->get(['specialty_ids', 'territory_ids']);

        $specs = collect($base)
            ->flatMap(fn ($row) => (array) ($row->specialty_ids ?? []))
            ->filter()->unique()->sort()->values();

        $terrs = collect($base)
            ->flatMap(fn ($row) => (array) ($row->territory_ids ?? []))
            ->filter()->unique()->sort()->values();

        $this->specialtyOptions = $specs->map(fn ($s) => ['label' => $s, 'value' => $s])->all();
        $this->territoryOptions = $terrs->map(fn ($t) => ['label' => $t, 'value' => $t])->all();

        // Ensure defaults if query-string came empty
        $this->sort    = $this->sort    ?: 'newest';
        $this->perPage = $this->perPage ?: 12;
    }

    // Reset page on filter/sort changes
    public function updatingSearch(): void     { $this->resetPage(); }
    public function updatingCompanyType(): void{ $this->resetPage(); }
    public function updatingSpecialty(): void  { $this->resetPage(); }
    public function updatingTerritory(): void  { $this->resetPage(); }
    public function updatingSort(): void       { $this->resetPage(); }
    public function updatingPerPage(): void    { $this->resetPage(); }

    // Coerce null/empty from clear buttons back to defaults
    public function updatedSort($value): void
    {
        $this->sort = $value ?: 'newest';
    }
    public function updatedPerPage($value): void
    {
        $this->perPage = (int) ($value ?: 12);
    }

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

        $sort = $this->sort ?: 'newest';

        $q->when($sort === 'newest',  fn ($qq) => $qq->orderByDesc('created_at'))
          ->when($sort === 'title',   fn ($qq) => $qq->orderBy('title'))
          ->when($sort === 'closing', fn ($qq) =>
                $qq->orderByRaw('visibility_until is null desc')->orderBy('visibility_until'));

        return $q->select([
            'id','slug','title','description','company_type',
            'specialty_ids','territory_ids','compensation',
            'visibility_until','created_at',
        ]);
    }

    public function render()
    {
        $openings = $this->query()->paginate($this->perPage ?: 12);

        return view('livewire.recruitment.opening-index-public', [
            'openings'         => $openings,
            'specialtyOptions' => $this->specialtyOptions,
            'territoryOptions' => $this->territoryOptions,
            'sortOptions'      => $this->sortOptions,
            'perPageOptions'   => $this->perPageOptions,
        ]);
    }
}
