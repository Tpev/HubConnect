<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Company;
use Livewire\Component;

class Dashboard extends Component
{
    public int $usersCount = 0;
    public int $companiesCount = 0;
    public int $adminsCount = 0;

    public function mount(): void
    {
        $this->usersCount     = User::count();
        $this->adminsCount    = User::where('is_admin', true)->count();
        $this->companiesCount = Company::count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.app');
    }
}
