<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyType
{
    /**
     * Enforce company type when provided (e.g. 'manufacturer' or 'distributor').
     * Still preserves your onboarding redirect if company_type is missing.
     */
    public function handle(Request $request, Closure $next, ?string $requiredType = null)
    {
        $user = $request->user();
        $team = $user?->currentTeam;

        // Allow onboarding routes to pass through un-gated
        if ($request->is('onboarding/*')) {
            return $next($request);
        }

        // If logged in but company_type not set -> send to role onboarding
        if ($user && $team && empty($team->company_type)) {
            return redirect()->route('onboarding.role');
        }

        // If a specific type is required for the route, enforce it
        if ($requiredType && (($team->company_type ?? null) !== $requiredType)) {
            abort(403, 'Unauthorized for this area.');
        }

        return $next($request);
    }
}
