<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        $user = Auth::user();

        // If this is an Individual account, send them to the Individual Dashboard.
        if ($user && method_exists($user, 'isIndividual') && $user->isIndividual()) {
            // Returning RedirectResponse from mount is supported and avoids the Livewire Redirector type error.
            return redirect()->route('dashboard.individual');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $team = $user?->currentTeam;

        // Role-aware hint for company dashboards
        $role = $team?->company_type ?? null; // 'manufacturer' | 'distributor' | 'both' | null

        return view('livewire.dashboard.index', [
            'role' => $role,
            'team' => $team,
        ])->title('Dashboard')->layout('layouts.app');
    }
}
