<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OnboardingCard extends Component
{
    public int $completion = 0;
    public array $steps = [];
    public ?string $kycStatus = null;

    // Exposed so the view can tailor URLs safely
    public ?\App\Models\Company $company = null;

    public function mount(): void
    {
        $user = Auth::user();
        $teamBase = $user?->currentTeam; // May be a plain Jetstream\Team
        $this->company = $teamBase
            ? \App\Models\Company::query()->find($teamBase->id) // <-- re-hydrate as Company model
            : null;

        $c = $this->company;

        // Basic profile
        $hasLogo    = filled($c?->team_profile_photo_path);
        $hasWebsite = filled($c?->website);
        $hasSummary = filled($c?->summary);

        // Relations (now safe because we're on Company model)
        $hasSpecs = $c ? $c->specialties()->exists() : false;
        $hasCerts = $c ? $c->certifications()->exists() : false;
        $hasIntent = false;
        if ($c && method_exists($c, 'activeIntent')) {
            $hasIntent = (bool) $c->activeIntent();
        }

        // KYC (if present on teams table)
        $this->kycStatus = $c?->kyc_status ?? null;

        // Compute completion across ALL tasks (even those hidden later)
        $parts = [
            $hasLogo,
            $hasWebsite,
            $hasSummary,
            $hasSpecs,
            $hasCerts,
            $hasIntent,
        ];
        $max  = count($parts);
        $done = collect($parts)->filter()->count();
        $this->completion = $max ? (int) floor(($done / $max) * 100) : 0;

        // Build steps then HIDE completed ones
        $baseSteps = [
            [
                'key'   => 'logo',
                'label' => 'Add company logo',
                'done'  => $hasLogo,
                'url'   => $this->safeRoute('companies.profile.edit', $c),
            ],
            [
                'key'   => 'website',
                'label' => 'Add website',
                'done'  => $hasWebsite,
                'url'   => $this->safeRoute('companies.profile.edit', $c),
            ],
            [
                'key'   => 'summary',
                'label' => 'Write a short summary',
                'done'  => $hasSummary,
                'url'   => $this->safeRoute('companies.profile.edit', $c),
            ],
            [
                'key'   => 'specialties',
                'label' => 'Add specialties',
                'done'  => $hasSpecs,
                'url'   => $this->safeRoute('companies.profile.edit', $c),
            ],
            [
                'key'   => 'certifications',
                'label' => 'Add certifications',
                'done'  => $hasCerts,
                'url'   => $this->safeRoute('companies.profile.edit', $c),
            ],
            [
                'key'   => 'intent',
                'label' => 'Publish what you’re looking for',
                'done'  => $hasIntent,
                'url'   => $this->safeRoute('companies.intent.edit', $c),
            ],
        ];

        // Only keep incomplete steps
        $this->steps = array_values(array_filter($baseSteps, fn ($s) => ! $s['done']));
    }

    private function safeRoute(string $name, ?\App\Models\Company $company): ?string
    {
        if (! $company) return null;
        return app('router')->has($name) ? route($name, $company) : null;
    }

    public function render()
    {
        return view('livewire.dashboard.onboarding-card');
    }
}
