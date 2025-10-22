<?php

namespace App\Http\Controllers;

use App\Models\IndividualProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->isIndividual(), 403);

        $profile = IndividualProfile::firstOrCreate(['user_id' => $user->id]);

        if ($profile->is_complete) {
            // If already complete, send to individual KYC (next step in your flow),
            // or to jobs board if you prefer:
            return redirect()->intended('/kyc/individual');
        }

        // Let Livewire component render the wizard (route uses component directly;
        // this method is alternative if you prefer a controller-based route)
        return view('livewire.profile-setup');
    }
}
