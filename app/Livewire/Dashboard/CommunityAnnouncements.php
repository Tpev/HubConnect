<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CommunityAnnouncements extends Component
{
    /**
     * Hard-coded messages for now.
     * Adjust here anytime you want to broadcast something to all users.
     */
    public array $announcements = [
        [
            'title' => 'Welcome to HubConnect 🎉',
            'date'  => '2025-10-01',
            'body'  => 'Thanks for being part of the early community! We’re polishing the matching flow and company profiles — if you spot anything odd, ping us via the help menu.',
            'level' => 'info', // info|success|warning|urgent (purely visual)
        ],
        [
            'title' => 'Recruitment module: first release',
            'date'  => '2025-10-08',
            'body'  => 'You can now create openings and receive applications. Head to Employer → Openings to start. Feedback welcome!',
            'level' => 'success',
        ],
        [
            'title' => 'Upcoming maintenance window',
            'date'  => '2025-10-20',
            'body'  => 'We’ll perform a short maintenance (under 10 minutes) at 02:00 UTC. Some requests may briefly queue during this time.',
            'level' => 'warning',
        ],
    ];

    public function render()
    {
        return view('livewire.dashboard.community-announcements');
    }
}
