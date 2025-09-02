<?php

// app/Livewire/Public/DeviceIndex.php

namespace App\Livewire\Public;

use App\Models\Device;
use App\Models\Specialty;
use App\Models\Territory;
use Livewire\Component;
use Livewire\WithPagination;

class DeviceIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public array $specialtyIds = [];
    public array $territoryIds = [];
    public string $sort = 'newest'; // newest|name|commission

    protected $queryString = ['search','specialtyIds','territoryIds','sort'];

    // Reset page when any bound prop updates live (useful for selects/sort)
    public function updating($name, $value) { $this->resetPage(); }

    // Called by the Search button (when using wire:model.defer on the input)
    public function applyFilters(): void
    {
        $this->resetPage();
    }

    // Clear all filters back to defaults
    public function resetFilters(): void
    {
        $this->search = '';
        $this->specialtyIds = [];
        $this->territoryIds = [];
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $q = Device::query()
            ->with(['company:id,name','category:id,name','specialties:id,name','territories:id,name'])
            ->where('is_published', true)
            ->whereIn('visibility', ['public','verified_only']);

        if ($this->search !== '') {
            $s = "%{$this->search}%";
            $q->where(function($w) use ($s) {
                $w->where('name','like',$s)
                  ->orWhere('slug','like',$s)
                  ->orWhere('description','like',$s)
                  ->orWhere('indications','like',$s)
                  ->orWhere('fda_pathway','like',$s);
            });
        }

        if (!empty($this->specialtyIds)) {
            $ids = $this->specialtyIds;
            $q->whereHas('specialties', fn($w) => $w->whereIn('specialties.id', $ids));
        }

        if (!empty($this->territoryIds)) {
            $ids = $this->territoryIds;
            $q->whereHas('territories', fn($w) => $w->whereIn('territories.id', $ids));
        }

        switch ($this->sort) {
            case 'name':
                $q->orderBy('name');
                break;
            case 'commission':
                $q->orderByDesc('commission_percent');
                break;
            default:
                $q->latest();
                break;
        }

        return view('livewire.public.device-index', [
            'rows' => $q->paginate(12),
            'allSpecialties' => Specialty::query()->orderBy('name')->get(['id','name']),
            'allTerritories' => Territory::query()->orderBy('name')->get(['id','name']),
        ])->layout('layouts.app');
    }
}

