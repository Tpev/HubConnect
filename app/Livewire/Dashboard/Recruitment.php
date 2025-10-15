<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Recruitment extends Component
{
    public array $openings = [];
    public int $applicationsCount = 0;

    public function mount(): void
    {
        $companyId = Auth::user()?->currentTeam?->id;
        if (! $companyId) {
            // no team context → show nothing
            $this->openings = [];
            $this->applicationsCount = 0;
            return;
        }

        // ------- Openings (scoped to this company) -------
        $this->openings = [];
        if (class_exists(\App\Models\Opening::class) && Schema::hasTable('openings')) {
            $openingsQuery = \App\Models\Opening::query();

            // Try common foreign key columns
            $companyCols = ['company_id', 'team_id', 'owner_company_id', 'posted_by_company_id'];
            $matchedCol  = null;
            foreach ($companyCols as $c) {
                if (Schema::hasColumn('openings', $c)) {
                    $matchedCol = $c;
                    break;
                }
            }

            if ($matchedCol) {
                $openingsQuery->where($matchedCol, $companyId);
            } else {
                // Fallback: if there is a relation "company" with a teams FK, you can add a whereHas here.
                // Leaving empty intentionally to avoid leaking data if we can't guarantee scoping.
                $openingsQuery->whereRaw('1=0');
            }

            $this->openings = $openingsQuery
                ->latest('id')
                ->take(5)
                ->get()      // keep flexible: don't hard-select columns
                ->toArray();
        }

        // ------- Applications (scoped to this company) -------
        $this->applicationsCount = 0;

        // Pick model if it exists
        $appModel = null;
        if (class_exists(\App\Models\Application::class)) {
            $appModel = new \App\Models\Application();
        } elseif (class_exists(\App\Models\JobApplication::class)) {
            $appModel = new \App\Models\JobApplication();
        }

        if ($appModel) {
            $appsTable = $appModel->getTable();
            if (Schema::hasTable($appsTable)) {
                $appsQuery = $appModel->newQuery();

                // First, try direct company scoping on the applications table
                $appCompanyCols = ['company_id', 'team_id', 'employer_company_id'];
                $appCompanyCol  = null;
                foreach ($appCompanyCols as $c) {
                    if (Schema::hasColumn($appsTable, $c)) {
                        $appCompanyCol = $c;
                        break;
                    }
                }

                if ($appCompanyCol) {
                    $appsQuery->where($appCompanyCol, $companyId);
                } else {
                    // Otherwise, scope via opening_id → openings table (if both columns exist)
                    if (
                        Schema::hasColumn($appsTable, 'opening_id') &&
                        Schema::hasTable('openings')
                    ) {
                        // Determine which openings column we used above
                        $openingsCompanyCol = null;
                        foreach (['company_id', 'team_id', 'owner_company_id', 'posted_by_company_id'] as $c) {
                            if (Schema::hasColumn('openings', $c)) {
                                $openingsCompanyCol = $c;
                                break;
                            }
                        }

                        if ($openingsCompanyCol) {
                            $appsQuery->whereIn('opening_id', function ($q) use ($openingsCompanyCol, $companyId) {
                                $q->from('openings')->select('id')->where($openingsCompanyCol, $companyId);
                            });
                        } else {
                            // No safe way to scope → show zero instead of leaking data
                            $appsQuery->whereRaw('1=0');
                        }
                    } else {
                        // No safe way to scope → show zero instead of leaking data
                        $appsQuery->whereRaw('1=0');
                    }
                }

                $this->applicationsCount = (int) $appsQuery->count('id');
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.recruitment');
    }
}
