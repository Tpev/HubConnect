<?php

// bootstrap/app.php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // ✅ add/ensure these two aliases exist
            'ensure.company.type' => \App\Http\Middleware\EnsureCompanyType::class,
            'not.manufacturer'    => \App\Http\Middleware\RedirectManufacturersFromDevices::class,
			 'admin' => \App\Http\Middleware\AdminMiddleware::class,
			 'company.verified' => \App\Http\Middleware\EnsureCompanyVerified::class,
        ]);
    })
	    ->withProviders([
        App\Providers\FortifyServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
