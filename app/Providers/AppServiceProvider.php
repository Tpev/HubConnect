<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
// ❌ Do NOT import/ use Laravel\Jetstream\Jetstream here

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // TallStack UI view namespaces (keep if you published views)
        View::prependNamespace('tallstackui', resource_path('views/vendor/tallstackui'));
        View::prependNamespace('tallstack-ui', resource_path('views/vendor/tallstack-ui'));

        // ❌ Remove this line (it caused the pivot mismatch):
        // Jetstream::useTeamModel(\App\Models\Company::class);
    }
}
