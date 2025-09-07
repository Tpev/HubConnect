<?php

use Illuminate\Support\Facades\Route;

// Onboarding
use App\Livewire\Onboarding\ChooseRole;
use App\Livewire\Onboarding\CompanyProfile;

// Public / Distributor device discovery
use App\Livewire\Public\DeviceIndex as PDeviceIndex;
use App\Livewire\Public\DeviceShow as PDeviceShow;

// Manufacturer (controller + livewire forms)
use App\Http\Controllers\DeviceController;
use App\Livewire\Manufacturer\DeviceForm as MDeviceForm;
use App\Livewire\Manufacturer\MatchInbox; // NEW: manufacturer match requests inbox
use App\Http\Controllers\CvDownloadController;
use App\Http\Controllers\RoleplayInviteController;
use App\Http\Controllers\RoleplaySubmitController;
use App\Livewire\Recruitment\OpeningCreate;
use App\Livewire\Recruitment\OpeningEdit;

Route::prefix('employer/openings')->group(function () {
    Route::get('create', OpeningCreate::class)->name('employer.openings.create');
    Route::get('{opening:slug}/edit', OpeningEdit::class)->name('employer.openings.edit');
});



// Public openings
Route::get('/openings', \App\Livewire\Recruitment\OpeningIndexPublic::class)->name('openings.index');
Route::get('/openings/{opening:slug}', \App\Livewire\Recruitment\OpeningShowPublic::class)->name('openings.show');
Route::get('/openings/{opening:slug}/apply', \App\Livewire\Recruitment\ApplicationStart::class)
    ->name('openings.apply');

// Employer area
Route::middleware(['auth'])->group(function () {
//	Route::get('/employer/openings/create', \App\Livewire\Recruitment\OpeningForm::class)->name('employer.openings.create');
    Route::get('/employer/openings', \App\Livewire\Recruitment\EmployerOpeningsIndex::class)->name('employer.openings');

   // Route::get('/employer/openings/{opening}/edit', \App\Livewire\Recruitment\OpeningForm::class)->name('employer.openings.edit');
   Route::get('/employer/openings/{opening}/applications', \App\Livewire\Recruitment\ApplicantsTable::class)->name('employer.openings.applications');

    // Signed CV download
    Route::get('/employer/applications/{application}/cv', CvDownloadController::class)
        ->middleware('signed')->name('applications.cv');
});

// Roleplay token entry
Route::get('/r/{token}', [RoleplayInviteController::class, 'show'])->name('roleplay.invite.show');
Route::post('/r/{token}/submit', [RoleplaySubmitController::class, 'store'])->name('roleplay.submit');


Route::get('/roleplay', \App\Livewire\RoleplaySimulator::class)
    ->name('roleplay.simulator');


Route::view('/how-it-works', 'how-it-works')->name('how-it-works');
Route::view('/pricing', 'pricing')->name('pricing');

/*
|--------------------------------------------------------------------------
| Tools / Utilities
|--------------------------------------------------------------------------
*/
// (none shown here — keep yours if any)

/*
|--------------------------------------------------------------------------
| Manufacturer (authenticated, verified, and proper company type)
| - Devices index is a Blade page via DeviceController@index
| - Create/Edit remain Livewire
| - NEW: Matches inbox
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'ensure.company.type:manufacturer', // enforce manufacturer team
])->prefix('m')->name('m.')->group(function () {
    // INDEX: Blade-hosted page (Livewire mounted inside)
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');

    // CREATE (Livewire)
    Route::get('/devices/create', MDeviceForm::class)->name('devices.create');

    // STUDIO (Blade via controller)
    Route::get('/devices/{device}/studio', [DeviceController::class, 'studio'])
        ->whereNumber('device')
        ->name('devices.studio');

    // EDIT (Livewire) — avoid implicit binding conflicts
    Route::get('/devices/{deviceId}/edit', MDeviceForm::class)
        ->whereNumber('deviceId')
        ->name('devices.edit');

    // NEW — Manufacturer: match requests inbox (accept/reject)
    Route::get('/matches', MatchInbox::class)->name('matches.index');
});

/*
|--------------------------------------------------------------------------
| Public / Distributor devices pages
| - Public catalog & show remain accessible (your "not.manufacturer" keeps MFGs out)
| - NEW: /discover requires distributor login (nice shortcut + can host extra distributor-only UI later)
|--------------------------------------------------------------------------
*/
Route::get('/devices', PDeviceIndex::class)
    ->middleware('not.manufacturer')
    ->name('devices.index');

Route::get('/devices/{slug}', PDeviceShow::class)
    ->middleware('not.manufacturer')
    ->name('devices.show');

// NEW — Distributor-only discover (same component, gated to distributor teams)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'ensure.company.type:distributor',
])->group(function () {
    Route::get('/discover', PDeviceIndex::class)->name('devices.discover');
});

/*
|--------------------------------------------------------------------------
| Onboarding (auth only; no company-type enforcement so users can set it)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/onboarding/role',    ChooseRole::class)->name('onboarding.role');
    Route::get('/onboarding/profile', CompanyProfile::class)->name('onboarding.profile');
});

/*
|--------------------------------------------------------------------------
| Landing + Marketing pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'landing')->name('landing');

Route::view('/manufacturers', 'public.manufacturers')->name('manufacturers');
Route::view('/reps', 'public.reps')->name('reps');



Route::view('/about', 'public.about')->name('about');
Route::view('/security', 'public.security')->name('security');
Route::view('/contact', 'public.contact')->name('contact');

// Legal
Route::view('/terms', 'public.legal.terms')->name('terms');
Route::view('/privacy', 'public.legal.privacy')->name('privacy');

/*
|--------------------------------------------------------------------------
| Blog / Resources (keep your controllers/views)
|--------------------------------------------------------------------------
*/
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

Route::view('/resources', 'public.resources.index')->name('resources');

Route::view('/case-studies', 'public.case-studies.index')->name('cases.index');
Route::get('/case-studies/{slug}', fn($slug) => view('public.case-studies.show', compact('slug')))
    ->name('cases.show'); 

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard (Jetstream default)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
