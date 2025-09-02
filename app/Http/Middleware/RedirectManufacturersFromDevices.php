<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectManufacturersFromDevices
{
    /**
     * If the logged-in user's current team is a manufacturer,
     * redirect them away from public /devices pages.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && optional(auth()->user()->currentTeam)->company_type === 'manufacturer') {
            return redirect()->route('m.devices');
        }

        return $next($request);
    }
}
