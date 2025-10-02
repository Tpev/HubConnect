<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('kyc.enabled', true)) {
            return $next($request);
        }

        $user = $request->user();
        $team = $user?->currentTeam;

        if (!$team) {
            return $next($request);
        }

        // Allow these while unverified
        if ($request->routeIs([
            'kyc.gate',
            'companies.profile.edit', 'companies.intent.edit', 'companies.show',
            'teams.show','teams.update','profile.show',
        ])) {
            return $next($request);
        }

        if ($team->kyc_status === 'approved') {
            return $next($request);
        }

        // Incomplete basics? push to Company Basics (your Livewire page)
        $needsBasics = blank($team->name) || blank($team->company_type) || blank($team->hq_country);
        if ($needsBasics) {
            return redirect()->route('companies.profile.edit', $team)
                ->with('status', 'complete_basics_first');
        }

        // Otherwise show the Club Gate (status page)
        return redirect()->route('kyc.gate')->with('status', 'verification_required');
    }
}
