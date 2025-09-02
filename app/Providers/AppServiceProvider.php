<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
           View::prependNamespace('tallstackui', resource_path('views/vendor/tallstackui'));
    View::prependNamespace('tallstack-ui', resource_path('views/vendor/tallstack-ui'));
    }
}
