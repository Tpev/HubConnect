<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CompaniesIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $q = '';

    #[Url]
    public int $perPage = 25;

    public function updatingQ() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    protected function query()
    {
        $base = Company::query()
            ->select('teams.*') // Company extends Team (table = teams)
            ->selectSub(
                DB::table('team_user')
                    ->selectRaw('count(*)')
                    ->whereColumn('team_user.team_id', 'teams.id'),
                'members_count'
            );

        if ($this->q !== '') {
            $term = trim($this->q);
            $base->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                  ->orWhere('slug', 'like', "%{$term}%")
                  ->orWhere('company_type', 'like', "%{$term}%")
                  ->orWhere('hq_country', 'like', "%{$term}%");
            });
        }

        return $base->orderBy('name');
    }

    public function render()
    {
        $companies = $this->query()->paginate($this->perPage);

        return view('livewire.admin.companies-index', compact('companies'))
            ->layout('layouts.app');
    }
}
