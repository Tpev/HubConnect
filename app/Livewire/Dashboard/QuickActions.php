<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class QuickActions extends Component
{
    public function render()
    {
        $team = Auth::user()?->currentTeam;

        return view('livewire.dashboard.quick-actions', [
            'team' => $team,
        ]);
    }
}
