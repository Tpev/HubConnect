<?php

namespace App\Http\Controllers;

use App\Models\Application;   // your model name
use App\Models\Opening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function quickApply(Request $request, Opening $opening)
    {
        $user = $request->user();
        abort_unless($user, 403);
        abort_unless($user->isIndividual(), 403, 'Only individual accounts can apply here.');

        // Opening must be published & visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        // Has this user already applied?
        $existing = Application::where('opening_id', $opening->id)
            ->where('candidate_user_id', $user->id)
            ->first();
        if ($existing) {
            return redirect()
                ->route('openings.show', $opening->slug)
                ->with('status', 'already-applied');
        }

        // Pull a bit of context from IndividualProfile if present
        $profile = $user->individualProfile ?? null;

        Application::create([
            'team_id'            => $opening->team_id,
            'opening_id'         => $opening->id,
            'candidate_user_id'  => $user->id,
            'candidate_name'     => $user->name,
            'email'              => $user->email,
            'phone'              => $profile?->phone,
            'location'           => $profile?->location,
            'status'             => 'new',
            'screening_answers'  => [],
        ]);

        // (Optional) notify the opening owner/recruiters here…

        return redirect()
            ->route('openings.show', $opening->slug)
            ->with('status', 'applied');
    }
}
