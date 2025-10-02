<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserShow extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user->load(['ownedTeams','teams']);
    }

    public function toggleAdmin(): void
    {
        $this->authorizeAdmin();

        $this->user->is_admin = !$this->user->is_admin;
        $this->user->save();

        session()->flash('success', 'Admin flag updated.');
    }

    protected function authorizeAdmin(): void
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.admin.user-show')
            ->layout('layouts.app');
    }
}
