<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function render()
    {
        $user = Auth::user();
        $team = $user?->currentTeam;

        // Role-aware hint
        $role = $team?->company_type ?? null; // 'manufacturer' | 'distributor' | 'both' | null

        return view('livewire.dashboard.index', [
            'role' => $role,
            'team' => $team,
        ])->title('Dashboard')->layout('layouts.app');
    }
}
