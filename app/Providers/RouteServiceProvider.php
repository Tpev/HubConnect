<?php

// app/Providers/RouteServiceProvider.php
use Illuminate\Support\Facades\Route;
use App\Models\Company;

public function boot(): void
{
    parent::boot();

    Route::bind('company', function ($value) {
        return Company::where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    });
}
