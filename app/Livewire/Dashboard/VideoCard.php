<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class VideoCard extends Component
{
    public string $videoUrl;

    public function mount(): void
    {
        $this->videoUrl = config('services.dashboard_video', env('DASHBOARD_VIDEO_URL', 'https://www.youtube.com/embed/dQw4w9WgXcQ'));
    }

    public function render()
    {
        return view('livewire.dashboard.video-card');
    }
}
