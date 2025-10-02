<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $q = '';

    #[Url(history: true)]
    public string $only = 'all'; // all|admins|verified|unverified

    #[Url]
    public int $perPage = 25;

    public function updatingQ() { $this->resetPage(); }
    public function updatingOnly() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function getRowsQuery()
    {
        $query = User::query()->withCount('teams');

        if ($this->q !== '') {
            $q = trim($this->q);
            $query->where(function($w) use ($q) {
                $w->where('name','like',"%{$q}%")
                  ->orWhere('email','like',"%{$q}%");
            });
        }

        if ($this->only === 'admins') {
            $query->where('is_admin', true);
        } elseif ($this->only === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($this->only === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        return $query->orderByDesc('id');
    }

    public function render()
    {
        $users = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.admin.users-index', compact('users'))
            ->layout('layouts.app');
    }
}
