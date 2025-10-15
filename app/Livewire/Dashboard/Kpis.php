<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Kpis extends Component
{
    public int $pendingRequests = 0;
    public int $connections = 0;
    public int $dealRooms = 0;
    public int $intentsActive = 0;

    public function mount(): void
    {
        $team = Auth::user()?->currentTeam;

        if ($team) {
            if (class_exists(\App\Models\MatchRequest::class)) {
                $this->pendingRequests = \App\Models\MatchRequest::query()
                    ->where('to_company_id', $team->id)
                    ->where('status', 'pending')
                    ->count('id');
            }

            if (class_exists(\App\Models\CompanyConnection::class)) {
                $this->connections = \App\Models\CompanyConnection::query()
                    ->where(function($q) use ($team) {
                        $q->where('company_a_id', $team->id)
                          ->orWhere('company_b_id', $team->id);
                    })->count('id');
            }

            if (class_exists(\App\Models\DealRoom::class)) {
                $this->dealRooms = \App\Models\DealRoom::query()
                    ->where(function($q) use ($team) {
                        $q->where('company_small_id', $team->id)
                          ->orWhere('company_large_id', $team->id);
                    })->count('id');
            }

            if (class_exists(\App\Models\CompanyIntent::class)) {
                $this->intentsActive = \App\Models\CompanyIntent::query()
                    ->where('company_id', $team->id)
                    ->where('status', 'active')
                    ->count('id');
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.kpis');
    }
}
