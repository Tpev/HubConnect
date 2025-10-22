<?php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\IndividualProfile;
use App\Models\KycSubmission;
use App\Models\Opening;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Str;

class ApplicantDrawer extends Component
{
    public int $applicationId;
    public int $openingId;

    public bool $show = true;

    public ?string $status = null;
    public ?float $score = null;

    /** Cached per render */
    protected ?Application $appCache = null;

    /** Allowed statuses for employers */
    public const STATUS_OPTIONS = [
        'new', 'shortlisted', 'under_review', 'interview', 'offer', 'rejected', 'hired', 'withdrawn',
    ];

    public function mount(int $applicationId, int $openingId): void
    {
        $this->applicationId = $applicationId;
        $this->openingId     = $openingId;

        $app = $this->findForTeam($this->applicationId);
        $this->status = $app->status ?: 'new';
        $this->score  = $app->score;
    }

    protected function findForTeam(int $id): Application
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        return Application::query()
            ->where('team_id', $teamId)
            ->where('opening_id', $this->openingId)
            ->findOrFail($id);
    }

    public function close(): void
    {
        $this->show = false;
        $this->dispatch('applicant-drawer:closed');
    }

    public function updateStatus(): void
    {
        $validated = $this->validate([
            'status' => ['required', Rule::in(self::STATUS_OPTIONS)],
            'score'  => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'status.in' => 'Unsupported status value.',
        ]);

        $app = $this->findForTeam($this->applicationId);
        $app->status = $validated['status'];
        $app->score  = $validated['score'];
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Applicant updated.');
    }

    public function sendInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        if (!$app->invite_token) {
            $app->invite_token = Str::random(40);
        }
        $app->invited_at = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite ready.');
    }

    public function regenerateInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        $app->invite_token = Str::random(40);
        $app->invited_at   = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite regenerated.');
    }

    public function removeInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        $app->invite_token = null;
        $app->invited_at   = null;
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite removed.');
    }

    protected function bestCvUrl(?Application $app, ?IndividualProfile $profile): ?string
    {
        // 1) Application-scoped CV
        if ($app && $app->cv_path) {
            // Secured route preferred
            if (Route::has('applications.cv')) {
                return route('applications.cv', ['application' => $app->id]);
            }
            // Fallback: public disk
            if (Storage::disk('public')->exists($app->cv_path)) {
                return Storage::disk('public')->url($app->cv_path);
            }
        }

        // 2) Profile CV
        if ($profile && $profile->cv_path) {
            if (Storage::disk('public')->exists($profile->cv_path)) {
                return Storage::disk('public')->url($profile->cv_path);
            }
        }

        return null;
    }

    protected function applicantProfile(?int $userId): ?IndividualProfile
    {
        if (!$userId) return null;

        if (class_exists(IndividualProfile::class)) {
            return IndividualProfile::where('user_id', $userId)->first();
        }
        return null;
    }

    protected function applicantKycStatus(?int $userId): ?string
    {
        if (!$userId || !class_exists(KycSubmission::class)) return null;
        $k = KycSubmission::where('user_id', $userId)->latest('id')->first();
        return $k?->status; // 'approved'|'pending_review'|'rejected'|'draft'|null
    }

    /**
     * Catalog of all screening fields with labels + types for display.
     */
    protected function fieldCatalog(): array
    {
        return [
            'years_total'               => ['label' => 'Total B2B sales experience (years)',  'type' => 'number'],
            'years_med_device'          => ['label' => 'Medical device experience (years)',   'type' => 'number'],
            'specialties'               => ['label' => 'Specialty experience',                'type' => 'list'],
            'state'                     => ['label' => 'Based state/territory',               'type' => 'text'],
            'travel_percent_max'        => ['label' => 'Willing to travel % (max)',           'type' => 'number'],
            'overnight_ok'              => ['label' => 'Overnights OK',                       'type' => 'boolean'],
            'driver_license'            => ['label' => 'Driver license & car',                'type' => 'boolean'],
            'opening_type_accepts'      => ['label' => 'Open employment types',               'type' => 'list'],
            'comp_structure_accepts'    => ['label' => 'Open comp structures',                'type' => 'list'],
            'expected_base'             => ['label' => 'Expected base (USD)',                 'type' => 'money'],
            'expected_ote'              => ['label' => 'Expected OTE (USD)',                  'type' => 'money'],
            'cold_outreach_ok'          => ['label' => 'Comfortable with cold outreach',      'type' => 'boolean'],
            'work_auth'                 => ['label' => 'US Work authorization',               'type' => 'text'],
            'start_date'                => ['label' => 'Earliest start date',                 'type' => 'date'],
            'has_noncompete_conflict'   => ['label' => 'Active non-compete conflict',         'type' => 'boolean'],
            'background_check_ok'       => ['label' => 'Background check OK',                 'type' => 'boolean'],
        ];
    }

    /**
     * Build a display-ready array for the "Screening answers" card,
     * using fields actually asked in this opening (order preserved).
     *
     * @return array<int,array{key:string,label:string,value:string|null,empty:bool}>
     */
    protected function screeningAnswersForDisplay(Application $app, Opening $opening): array
    {
        $catalog = $this->fieldCatalog();

        // Which fields were asked for this opening?
        $asked = collect((array) ($opening->screening_rules ?? []))
            ->pluck('field')
            ->filter()
            ->values()
            ->all();

        if (empty($asked)) return [];

        $answers = [];

        // We prefer the explicit columns stored on Application (which you already save),
        // and we fallback to the screening_answers JSON snapshot if needed.
        $snapshot = (array) ($app->screening_answers ?? []);

        $get = function (string $key) use ($app, $snapshot) {
            // Map keys to Application properties:
            return match ($key) {
                'years_total'              => $app->years_total,
                'years_med_device'         => $app->years_med_device,
                'specialties'              => $app->candidate_specialties, // array
                'state'                    => $app->state,
                'travel_percent_max'       => $app->travel_percent_max,
                'overnight_ok'             => $app->overnight_ok,
                'driver_license'           => $app->driver_license,
                'opening_type_accepts'     => $app->opening_type_accepts,   // array
                'comp_structure_accepts'   => $app->comp_structure_accepts, // array
                'expected_base'            => $app->expected_base,
                'expected_ote'             => $app->expected_ote,
                'cold_outreach_ok'         => $app->cold_outreach_ok,
                'work_auth'                => $app->work_auth,
                'start_date'               => $app->start_date,
                'has_noncompete_conflict'  => $app->has_noncompete_conflict,
                'background_check_ok'      => $app->background_check_ok,
                default                    => $snapshot[$key] ?? null,
            };
        };

        $fmtMoney = function ($v) {
            if ($v === null || $v === '') return null;
            if (!is_numeric($v)) return (string) $v;
            return '$' . number_format((float) $v, 0);
        };
        $fmtBool = function ($v) {
            if (is_null($v)) return null;
            return $v ? 'Yes' : 'No';
        };
        $fmtDate = function ($v) {
            if (!$v) return null;
            try {
                return \Carbon\Carbon::parse($v)->toDateString();
            } catch (\Throwable $e) {
                return (string) $v;
            }
        };

        foreach ($asked as $field) {
            if (!isset($catalog[$field])) continue;

            $meta  = $catalog[$field];
            $raw   = $get($field);
            $value = null;

            switch ($meta['type']) {
                case 'money':
                    $value = $fmtMoney($raw);
                    break;
                case 'boolean':
                    $value = $fmtBool($raw);
                    break;
                case 'date':
                    $value = $fmtDate($raw);
                    break;
                case 'list':
                    $arr = is_array($raw) ? array_values(array_filter($raw, fn($x) => $x !== '' && $x !== null)) : [];
                    $value = count($arr) ? implode(', ', $arr) : null;
                    break;
                case 'number':
                    $value = ($raw === null || $raw === '') ? null : (string) $raw;
                    break;
                case 'text':
                default:
                    $value = ($raw === null || $raw === '') ? null : (string) $raw;
                    break;
            }

            $answers[] = [
                'key'   => $field,
                'label' => $meta['label'],
                'value' => $value,
                'empty' => $value === null || $value === '',
            ];
        }

        return $answers;
    }

    public function render()
    {
        $app     = $this->findForTeam($this->applicationId);
        $opening = Opening::find($this->openingId);
        $this->appCache = $app;

        $inviteUrl = $app->invite_token
            ? (Route::has('roleplay.invite.show')
                ? route('roleplay.invite.show', ['token' => $app->invite_token])
                : null)
            : null;

        $profile = $this->applicantProfile($app->candidate_user_id);
        $kyc     = $this->applicantKycStatus($app->candidate_user_id);

        $cvUrl = $this->bestCvUrl($app, $profile);

        // Display-safe snapshot for the view
        $snapshot = [
            'name'      => $app->candidate_name ?: ($profile->full_name ?? null),
            'email'     => $app->email,
            'phone'     => $app->phone,
            'location'  => $app->location ?: ($profile->location ?? null),
            'headline'  => $profile?->headline,
            'visibility'=> $profile?->visibility, // private|discoverable
            'years'     => $profile?->years_experience,
            'bio'       => $profile?->bio,
            'skills'    => is_array($profile?->skills) ? array_values(array_filter($profile->skills)) : [],
            'links'     => is_array($profile?->links)  ? array_values($profile->links) : [],
            'kyc'       => $kyc,
        ];

        $answersDisplay = $this->screeningAnswersForDisplay($app, $opening);

        return view('livewire.recruitment.applicant-drawer', [
            'app'            => $app,
            'opening'        => $opening,
            'inviteUrl'      => $inviteUrl,
            'cvUrl'          => $cvUrl,
            'snap'           => $snapshot,
            'statusOptions'  => self::STATUS_OPTIONS,
            'answersDisplay' => $answersDisplay,
        ]);
    }
}
