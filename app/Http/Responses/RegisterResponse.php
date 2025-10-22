<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // INDIVIDUAL: no team; send to profile setup
        if ($user && $user->account_type === 'individual') {
            return redirect()->intended('/profile/setup');
        }

        // COMPANY: keep your existing logic
        $team = $user?->currentTeam;

        if ($team) {
            $needsBasics = blank($team->name) || blank($team->company_type) || blank($team->hq_country);
            if ($needsBasics) {
                return redirect()->route('companies.profile.edit', $team);
            }
            if ($team->kyc_status !== 'approved') {
                return redirect()->route('kyc.gate');
            }
        }

        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
