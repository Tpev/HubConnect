<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Recruitment extends Component
{
    public array $openings = [];
    public int $applicationsCount = 0;

    public function mount(): void
    {
        // Openings (no hard-coded columns; just fetch whatever exists)
        if (class_exists(\App\Models\Opening::class)) {
            $this->openings = \App\Models\Opening::query()
                ->latest('id')
                ->take(5)
                ->get()               // ← no select([...]) to avoid missing columns
                ->toArray();
        }

        // Applications count (support either model name)
        if (class_exists(\App\Models\Application::class)) {
            $this->applicationsCount = \App\Models\Application::query()->count('id');
        } elseif (class_exists(\App\Models\JobApplication::class)) {
            $this->applicationsCount = \App\Models\JobApplication::query()->count('id');
        } else {
            $this->applicationsCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.recruitment');
    }
}
