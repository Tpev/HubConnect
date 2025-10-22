<?php
// app/Livewire/Recruitment/OpeningShowPublic.php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class OpeningShowPublic extends Component
{
    public Opening $opening;

    /** 'guest' | 'individual' | 'company' */
    public string $viewerType = 'guest';

    /** The current user's application for this opening (if any) */
    public ?Application $myApplication = null;

    /** Convenience flag for the blade */
    public bool $hasApplied = false;

    public function mount(Opening $opening): void
    {
        // Only show published & still visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        $this->opening = $opening;

        $user = Auth::user();

        if ($user) {
            // Determine viewer type
            if (method_exists($user, 'isIndividual') && $user->isIndividual()) {
                $this->viewerType = 'individual';

                // Check if this user already applied to this opening
                $this->myApplication = Application::where('opening_id', $opening->id)
                    ->where('candidate_user_id', $user->id)
                    ->latest('created_at')
                    ->first();

                $this->hasApplied = (bool) $this->myApplication;
            } else {
                $this->viewerType = 'company';
            }
        } else {
            $this->viewerType = 'guest';
        }
    }

    public function render()
    {
        return view('livewire.recruitment.opening-show-public', [
            'opening'      => $this->opening,
            'viewerType'   => $this->viewerType,
            'hasApplied'   => $this->hasApplied,
            'myApplication'=> $this->myApplication,
        ]);
    }
}
