<?php

namespace App\Livewire\Dashboard;

use App\Models\KycSubmission;
use App\Models\Opening;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class IndividualDashboard extends Component
{
    public int $completion = 0;
    /** @var array<string> */
    public array $missingFields = [];

    public string $kycStatus = 'not_started'; // draft|pending_review|approved|rejected|not_started
    public string $kycHint = '';
    public ?KycSubmission $kyc = null;

    /** @var array<int, array<string, mixed>> */
    public array $applications = [];

    /** @var array<int, array{title:string,href?:string,icon?:string,hint?:string}> */
    public array $nextSteps = [];

    public int $newRolesCount = 0;

    public bool $needsApplicantProfile = false;

    public function mount(): void
    {
        $u = Auth::user();
        abort_unless($u && method_exists($u, 'isIndividual') && $u->isIndividual(), 403);

        // Build a tolerant snapshot of the applicant profile
        $snap = $this->snapshotProfile($u);

        // Core fields that gate the "create your profile" banner
        $this->needsApplicantProfile = !(filled($snap['headline']) && filled($snap['location']));

        // ---- COMPLETION (phone + city removed from checklist)
        $checks = [
            'headline'   => filled($snap['headline']),
            'location'   => filled($snap['location']),
            'bio'        => filled($snap['bio']),
            'experience' => $snap['experience'] !== null && $snap['experience'] !== '',
            'skills'     => count($snap['skills']) > 0,
        ];

        $weights = [
            'headline'   => 3,
            'skills'     => 3,
            'location'   => 2,
            'bio'        => 2,
            'experience' => 1,
        ];

        $score = 0; $max = 0; $missing = [];
        foreach ($checks as $field => $ok) {
            $w = $weights[$field] ?? 1;
            $max += $w;
            if ($ok) {
                $score += $w;
            } else {
                $missing[] = Str::headline($field);
            }
        }
        $this->completion    = (int) round($max > 0 ? ($score / $max) * 100 : 0);
        $this->missingFields = $missing;

        // ---- KYC
        $this->kyc = KycSubmission::where('user_id', $u->id)->latest('id')->first();
        $this->kycStatus = $this->kyc?->status ?? 'not_started';
        $this->kycHint = match ($this->kycStatus) {
            'approved'        => 'You’re verified. Applications get priority review.',
            'pending_review'  => 'We’re reviewing your submission. You’ll be notified by email.',
            'rejected'        => 'Your submission needs updates. Review comments and resubmit.',
            'draft'           => 'Finish your draft and submit for review.',
            default           => 'Get verified to speed up applications.',
        };

        // ---- Applications
        $this->applications = $this->loadUserApplications($u->id, $u->email);

        // ---- New roles (for next steps)
        $this->newRolesCount = Opening::query()
            ->where('status','published')
            ->where('created_at','>=', now()->subDays(7))
            ->count();

        // ---- Next steps
        $this->nextSteps = $this->buildNextSteps();
    }

    /**
     * Snapshot applicant profile from relation or direct model lookup.
     *
     * Returns:
     *  - headline, location, bio: string
     *  - experience: int|string|null
     *  - skills: array<string>
     *  - phone, city: string|null (kept for other UI needs; not part of completion)
     */
    protected function snapshotProfile($u): array
    {
        $p = null;

        if (method_exists($u, 'individualProfile')) {
            $p = $u->individualProfile;
        }
        if (!$p && class_exists(\App\Models\IndividualProfile::class)) {
            $p = \App\Models\IndividualProfile::where('user_id', $u->id)->first();
        }

        $headline = trim((string)($p->headline ?? ''));
        $location = trim((string)($p->location ?? ($u->city ?? '')));
        $bio      = trim((string)($p->bio ?? ''));

        $experience = $p->years_experience ?? $p->experience ?? null;
        if (is_string($experience)) $experience = trim($experience);

        // Skills normalization: array / collection / JSON / CSV
        $skills = [];
        $raw = $p->skills ?? null;

        if ($raw instanceof \Illuminate\Support\Collection) {
            $skills = $raw->map(function ($item) {
                if (is_array($item)) return $item['name'] ?? $item['title'] ?? reset($item) ?? null;
                if (is_object($item)) return $item->name ?? $item->title ?? (string) $item;
                return (string) $item;
            })->filter()->values()->all();
        } elseif (is_array($raw)) {
            $skills = array_values(array_filter(array_map(fn($v) => is_string($v) ? trim($v) : ($v ?? ''), $raw)));
        } elseif (is_string($raw) && str_starts_with(trim($raw), '[')) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $skills = array_values(array_filter(array_map(fn($v) => is_string($v) ? trim($v) : ($v ?? ''), $decoded)));
            }
        } elseif (is_string($raw) && $raw !== '') {
            $skills = array_values(array_filter(array_map('trim', explode(',', $raw))));
        }

        $phone = $p->phone ?? $u->phone ?? null; // not used for completion
        $city  = $p->city  ?? $u->city  ?? null; // not used for completion

        return compact('headline','location','bio','experience','skills','phone','city');
    }

    /**
     * Load applications from either App\Models\Application or App\Models\JobApplication.
     */
    protected function loadUserApplications(int $userId, ?string $email): array
    {
        $class = null;
        if (class_exists(\App\Models\Application::class)) {
            $class = \App\Models\Application::class;
        } elseif (class_exists(\App\Models\JobApplication::class)) {
            $class = \App\Models\JobApplication::class;
        }

        if (!$class) return [];

        $query = $class::query();
        $modelInstance = new $class;
        if (method_exists($modelInstance, 'opening')) {
            $query->with('opening');
        }

        $query->where(function ($q) use ($userId, $email) {
            $q->where('candidate_user_id', $userId);
            if ($email) $q->orWhere('email', $email);
        });

        $apps = $query->latest('id')->limit(12)->get();

        return $apps->map(function ($a) {
            $opening      = $a->opening ?? null;
            $openingTitle = $opening?->title ?? ($a->opening_title ?? null);
            $openingSlug  = $opening?->slug ?? ($a->opening_slug ?? null);
            $companyType  = $opening?->company_type ?? ($a->company_type ?? null);
            $compensation = $opening?->compensation ?? ($a->compensation ?? null);

            $openingUrl = null;
            if ($openingSlug && Route::has('openings.show')) {
                $openingUrl = route('openings.show', $openingSlug);
            }

            $applicationUrl = null;
            if (Route::has('applications.show')) {
                $applicationUrl = route('applications.show', $a->id);
            }

            $status = $a->screen_status ?? $a->status ?? 'pending';

            return [
                'id'                 => $a->id,
                'status'             => Str::of($status)->lower()->toString(),
                'applied_at'         => optional($a->created_at)?->toDateTimeString(),
                'applied_for_humans' => optional($a->created_at)?->diffForHumans(),
                'opening_title'      => $openingTitle,
                'opening_slug'       => $openingSlug,
                'company_type'       => $companyType,
                'compensation'       => $compensation,
                'opening_url'        => $openingUrl,
                'application_url'    => $applicationUrl,
            ];
        })->all();
    }

    protected function buildNextSteps(): array
    {
        $steps = [];

        $profileRoute = Route::has('applicant.profile.edit')
            ? route('applicant.profile.edit')
            : (Route::has('profile.show') ? route('profile.show') : null);

        if ($this->completion < 100 && !empty($this->missingFields) && $profileRoute) {
            $steps[] = [
                'title' => 'Complete your profile',
                'href'  => $profileRoute,
                'icon'  => 'user',
                'hint'  => 'Add: ' . implode(', ', array_slice($this->missingFields, 0, 3)) . (count($this->missingFields) > 3 ? '…' : ''),
            ];
        }

        if (in_array($this->kycStatus, ['not_started','draft','rejected']) && Route::has('kyc.individual')) {
            $steps[] = [
                'title' => $this->kycStatus === 'rejected' ? 'Fix & resubmit verification' : 'Start verification',
                'href'  => route('kyc.individual'),
                'icon'  => 'shield',
                'hint'  => $this->kycHint,
            ];
        }

        if ($this->newRolesCount > 0 && Route::has('openings.index')) {
            $steps[] = [
                'title' => 'Check new openings this week',
                'href'  => route('openings.index'),
                'icon'  => 'briefcase',
                'hint'  => "{$this->newRolesCount} new roles published",
            ];
        }

        if (empty($steps) && Route::has('openings.index')) {
            $steps[] = [
                'title' => 'Browse roles now',
                'href'  => route('openings.index'),
                'icon'  => 'search',
                'hint'  => 'See what’s available today',
            ];
        }

        return $steps;
    }

    public function render()
    {
        return view('livewire.dashboard.individual-dashboard', [
            'completion'           => $this->completion,
            'missingFields'        => $this->missingFields,
            'kyc'                  => $this->kyc,
            'kycStatus'            => $this->kycStatus,
            'kycHint'              => $this->kycHint,
            'applications'         => $this->applications,
            'nextSteps'            => $this->nextSteps,
            'newRolesCount'        => $this->newRolesCount,
            'needsApplicantProfile'=> $this->needsApplicantProfile,
        ])->title('My Dashboard')->layout('layouts.app');
    }
}
