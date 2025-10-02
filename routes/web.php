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

// Companies (directory side)
use App\Livewire\Companies\Index as DirectoryCompaniesIndex;
use App\Livewire\Companies\Show  as DirectoryCompanyShow;
use App\Livewire\Companies\ProfileWizard;
use App\Livewire\Companies\IntentEditor;

// Requests
use App\Livewire\Requests\Index as RequestsIndex;

// Admin
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Admin\UserShow;
use App\Livewire\Admin\CompaniesIndex as AdminCompaniesIndex;
use App\Livewire\Admin\CompanyShow   as AdminCompanyShow;
use App\Http\Controllers\DealRoomController;
use App\Livewire\DealRooms\Show as DealRoomShow;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\Admin\KycReviewController;

Route::middleware(['web','auth','verified'])->group(function () {
    // Status page (read-only)
    Route::get('/club', [KycController::class, 'gate'])->name('kyc.gate');

    // Admin KYC review
    Route::get('/admin/kyc', [KycReviewController::class, 'index'])->name('admin.kyc.index');
    Route::put('/admin/kyc/{team}/approve', [KycReviewController::class, 'approve'])->name('admin.kyc.approve');
    Route::put('/admin/kyc/{team}/reject',  [KycReviewController::class, 'reject'])->name('admin.kyc.reject');

    // Gate market-facing features
    Route::middleware(['company.verified'])->group(function () {
        Route::get('/companies', [\App\Http\Controllers\CompanyDirectoryController::class, 'index'])->name('companies.index');
        Route::get('/requests',  [\App\Http\Controllers\RequestsController::class, 'index'])->name('requests.index');
        Route::get('/deal-rooms', [\App\Http\Controllers\DealRoomController::class, 'index'])->name('deal-rooms.index');
        Route::get('/deal-rooms/{room}', \App\Livewire\DealRooms\Show::class)->name('deal-rooms.show');
    });
});

Route::middleware(['web','auth','verified'])->group(function () {
    Route::get('/companies/{team}', [CompanyController::class, 'show'])->name('companies.show');
});

Route::middleware(['web','auth'])->group(function () {
    Route::get('/deal-rooms', [DealRoomController::class, 'index'])->name('deal-rooms.index');
    Route::get('/deal-rooms/{room}', DealRoomShow::class)->name('deal-rooms.show');
});

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/
Route::middleware(['web','auth','verified','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');

        Route::get('/users', UsersIndex::class)->name('users.index');
        Route::get('/users/{user}', UserShow::class)->name('users.show');

        Route::get('/companies', AdminCompaniesIndex::class)->name('companies.index');
        Route::get('/companies/{company}', AdminCompanyShow::class)->name('companies.show');
    });

/*
|--------------------------------------------------------------------------
| Authenticated (non-admin) / Directory
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/companies', DirectoryCompaniesIndex::class)->name('companies.index');
    Route::get('/companies/{company}', DirectoryCompanyShow::class)->name('companies.show');

    Route::get('/companies/{company}/intent', IntentEditor::class)->name('companies.intent.edit');
    Route::get('/companies/{company}/profile', ProfileWizard::class)->name('companies.profile.edit');

    Route::get('/requests', RequestsIndex::class)->name('requests.index');
});

/*
|--------------------------------------------------------------------------
| Employer / Recruitment
|--------------------------------------------------------------------------
*/
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
    Route::get('/employer/openings', \App\Livewire\Recruitment\EmployerOpeningsIndex::class)->name('employer.openings');
    Route::get('/employer/openings/{opening}/applications', \App\Livewire\Recruitment\ApplicantsTable::class)->name('employer.openings.applications');

    // Signed CV download
    Route::get('/employer/applications/{application}/cv', CvDownloadController::class)
        ->middleware('signed')->name('applications.cv');
});

/*
|--------------------------------------------------------------------------
| Roleplay
|--------------------------------------------------------------------------
*/
Route::get('/r/{token}', [RoleplayInviteController::class, 'show'])->name('roleplay.invite.show');
Route::post('/r/{token}/submit', [RoleplaySubmitController::class, 'store'])->name('roleplay.submit');

Route::get('/roleplay', \App\Livewire\RoleplaySimulator::class)
    ->name('roleplay.simulator');

/*
|--------------------------------------------------------------------------
| Marketing Pages
|--------------------------------------------------------------------------
*/
Route::view('/how-it-works', 'how-it-works')->name('how-it-works');
Route::view('/pricing', 'pricing')->name('pricing');

/*
|--------------------------------------------------------------------------
| Manufacturer (authenticated, verified, company type = manufacturer)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'ensure.company.type:manufacturer',
])->prefix('m')->name('m.')->group(function () {
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::get('/devices/create', MDeviceForm::class)->name('devices.create');
    Route::get('/devices/{device}/studio', [DeviceController::class, 'studio'])
        ->whereNumber('device')
        ->name('devices.studio');
    Route::get('/devices/{deviceId}/edit', MDeviceForm::class)
        ->whereNumber('deviceId')
        ->name('devices.edit');
    Route::get('/matches', MatchInbox::class)->name('matches.index');
});

/*
|--------------------------------------------------------------------------
| Public / Distributor devices
|--------------------------------------------------------------------------
*/
Route::get('/devices', PDeviceIndex::class)
    ->middleware('not.manufacturer')
    ->name('devices.index');

Route::get('/devices/{slug}', PDeviceShow::class)
    ->middleware('not.manufacturer')
    ->name('devices.show');

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
| Onboarding
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/onboarding/role',    ChooseRole::class)->name('onboarding.role');
    Route::get('/onboarding/profile', CompanyProfile::class)->name('onboarding.profile');
});

/*
|--------------------------------------------------------------------------
| Landing + Marketing Pages
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
| Blog / Resources
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
