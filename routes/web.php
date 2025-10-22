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
use App\Livewire\Manufacturer\MatchInbox;
use App\Http\Controllers\CvDownloadController;
use App\Http\Controllers\RoleplayInviteController;
use App\Http\Controllers\RoleplaySubmitController;


// Companies (directory side)
use App\Http\Controllers\CompanyController;
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
use App\Http\Controllers\KycController;
use App\Http\Controllers\Admin\KycReviewController;
use App\Http\Controllers\DealRoomFileController;
use App\Livewire\ProfileSetup;
use App\Livewire\Kyc\IndividualKycForm;
use App\Http\Controllers\JobApplicationController;
use App\Livewire\Recruitment\OpeningIndexPublic;
use App\Livewire\Recruitment\OpeningShowPublic;
use App\Livewire\Recruitment\ApplicationStart;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Dashboard\IndividualDashboard;
use App\Livewire\Applicant\ProfileEditor;
use App\Livewire\Recruitment\EmployerOpeningsIndex;
use App\Livewire\Recruitment\OpeningCreate;
use App\Livewire\Recruitment\OpeningEdit;




Route::middleware(['auth','verified'])->group(function () {
    Route::get('/applicant/profile', ProfileEditor::class)
        ->name('applicant.profile.edit');
});


Route::middleware(['auth','verified'])->prefix('admin')->name('admin.')->group(function () {
    // Company KYCs (existing)
    Route::get('/kyc', [KycReviewController::class, 'index'])->name('kyc.index');
    Route::put('/kyc/{team}/approve', [KycReviewController::class, 'approve'])->name('kyc.approve');
    Route::put('/kyc/{team}/reject', [KycReviewController::class, 'reject'])->name('kyc.reject');

    // Individual KYCs (NEW)
    Route::get('/kyc/individuals', [KycReviewController::class, 'individuals'])->name('kyc.individuals.index');
    Route::put('/kyc/individuals/{submission}/approve', [KycReviewController::class, 'approveIndividual'])->name('kyc.individuals.approve');
    Route::put('/kyc/individuals/{submission}/reject', [KycReviewController::class, 'rejectIndividual'])->name('kyc.individuals.reject');
});

Route::middleware(['auth','verified'])->group(function () {
    // Your existing dashboard
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    // New Individual dashboard
    Route::get('/dashboard/individual', IndividualDashboard::class)->name('dashboard.individual');
});

Route::get('/openings', OpeningIndexPublic::class)->name('openings.index');
Route::get('/openings/{opening:slug}', OpeningShowPublic::class)->name('openings.show');

# Detailed form (existing Livewire)
Route::get('/openings/{opening:slug}/apply', ApplicationStart::class)->name('openings.apply');

# Quick apply (logged-in individuals)
Route::post('/openings/{opening:slug}/apply/quick', [JobApplicationController::class, 'quickApply'])
    ->middleware(['auth','verified'])
    ->name('openings.apply.quick');


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/kyc/individual', IndividualKycForm::class)->name('kyc.individual');
});

Route::middleware(['auth','verified'])->prefix('admin')->group(function () {
    Route::get('/kyc/individuals', [KycReviewController::class, 'individuals'])->name('admin.kyc.individuals.index');
    Route::post('/kyc/individuals/{submission}/approve', [KycReviewController::class, 'approveIndividual'])->name('admin.kyc.individuals.approve');
    Route::post('/kyc/individuals/{submission}/reject', [KycReviewController::class, 'rejectIndividual'])->name('admin.kyc.individuals.reject');
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/profile/setup', ProfileSetup::class)->name('profile.setup');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/deal-rooms/files/{file}/download', [DealRoomFileController::class, 'download'])
        ->name('deal-rooms.files.download');
});

/*
|--------------------------------------------------------------------------
| Authenticated + Verified (Main User Area)
|--------------------------------------------------------------------------
*/
Route::middleware(['web','auth','verified'])->group(function () {
    // KYC Gate
    Route::get('/club', [KycController::class, 'gate'])->name('kyc.gate');

    // Admin KYC Review
    Route::get('/admin/kyc', [KycReviewController::class, 'index'])->name('admin.kyc.index');
    Route::put('/admin/kyc/{team}/approve', [KycReviewController::class, 'approve'])->name('admin.kyc.approve');
    Route::put('/admin/kyc/{team}/reject',  [KycReviewController::class, 'reject'])->name('admin.kyc.reject');

    /*
    |--------------------------------------------------------------------------
    | Company Directory & Related Pages
    |--------------------------------------------------------------------------
    */
    Route::middleware(['company.verified'])->group(function () {
        // Companies directory — new Livewire split page
        Route::get('/companies', fn () => view('companies.index'))->name('companies.index');

        // Company show
        Route::get('/companies/{team}', [CompanyController::class, 'show'])->name('companies.show');

        // 🔙 Restored routes for profile + intent editor (used in nav)
        Route::get('/companies/{company}/profile', ProfileWizard::class)->name('companies.profile.edit');
        Route::get('/companies/{company}/intent',  IntentEditor::class)->name('companies.intent.edit');

        // Requests + Deal Rooms
        Route::get('/requests', RequestsIndex::class)->name('requests.index');

        Route::get('/deal-rooms', [DealRoomController::class, 'index'])->name('deal-rooms.index');
        Route::get('/deal-rooms/{room}', DealRoomShow::class)->name('deal-rooms.show');
    });
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
Route::get('/roleplay', \App\Livewire\RoleplaySimulator::class)->name('roleplay.simulator');

/*
|--------------------------------------------------------------------------
| Manufacturer Area
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
        ->whereNumber('device')->name('devices.studio');
    Route::get('/devices/{deviceId}/edit', MDeviceForm::class)
        ->whereNumber('deviceId')->name('devices.edit');
    Route::get('/matches', MatchInbox::class)->name('matches.index');
});

/*
|--------------------------------------------------------------------------
| Public / Distributor Devices
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
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/onboarding/role',    ChooseRole::class)->name('onboarding.role');
    Route::get('/onboarding/profile', CompanyProfile::class)->name('onboarding.profile');
});

/*
|--------------------------------------------------------------------------
| Marketing Pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'landing')->name('landing');
Route::view('/manufacturers', 'public.manufacturers')->name('manufacturers');
Route::view('/reps', 'public.reps')->name('reps');
Route::view('/about', 'public.about')->name('about');
Route::view('/security', 'public.security')->name('security');
Route::view('/contact', 'public.contact')->name('contact');
Route::view('/how-it-works', 'how-it-works')->name('how-it-works');
Route::view('/pricing', 'pricing')->name('pricing');
// Legal
Route::view('/terms', 'public.legal.terms')->name('terms');
Route::view('/privacy', 'public.legal.privacy')->name('privacy');

/*
|--------------------------------------------------------------------------
| Blog / Resources
|--------------------------------------------------------------------------
*/

/*
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::view('/resources', 'public.resources.index')->name('resources');
Route::view('/case-studies', 'public.case-studies.index')->name('cases.index');
Route::get('/case-studies/{slug}', fn($slug) => view('public.case-studies.show', compact('slug')))
    ->name('cases.show');
*/
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
    // New Livewire dashboard page
    Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');
});

