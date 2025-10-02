<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
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

        // Respect intended URL if there was one
        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
