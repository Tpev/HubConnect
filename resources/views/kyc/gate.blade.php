<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900">Welcome to the Club</h2>
            @php
                $kyc         = $team->kyc_status ?? 'new';
                $isPending   = in_array($kyc, ['new','pending_review']);
                $isApproved  = $kyc === 'approved';
                $isRejected  = $kyc === 'rejected';
                $needsBasics = blank($team->name) || blank($team->company_type) || blank($team->hq_country);
            @endphp

            {{-- Compact status badge (uses Blade Heroicons) --}}
            @if($isApproved)
                <x-ts-badge color="success" size="sm">
                    <span class="inline-flex items-center gap-1">
                        
                        Verified
                    </span>
                </x-ts-badge>
            @elseif($isRejected)
                <x-ts-badge color="danger" size="sm">
                    <span class="inline-flex items-center gap-1">
                        
                        Not approved
                    </span>
                </x-ts-badge>
            @else
                <x-ts-badge color="warning" size="sm">
                    <span class="inline-flex items-center gap-1">
                        
                        Pending review
                    </span>
                </x-ts-badge>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto space-y-8 sm:px-6 lg:px-8">

            {{-- Hero / Intro --}}
            <x-ts-card>
                <x-slot name="header">
                    <div class="flex items-center gap-3">
                        <div class="shrink-0 rounded-xl p-2 bg-[var(--brand-50)]">
                            {{-- keep this inline SVG (not heroicons) --}}
                            <svg class="size-5 text-[var(--brand-700)]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3l8 4v5c0 5-3.5 9-8 9s-8-4-8-9V7l8-4z" stroke="currentColor" stroke-width="1.5" />
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-semibold">Why we verify every company</div>
                            <div class="text-sm text-gray-500">A curated, trusted network—built for serious B2B relationships.</div>
                        </div>
                    </div>
                </x-slot>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-3">
                        <p class="text-gray-700">
                            We’re a <strong>curated club</strong> of manufacturers and distributors. To keep quality high,
                            we manually verify each new company that joins. This protects everyone’s time and builds a
                            consistent level of trust across the platform.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 size-2 rounded-full bg-emerald-500"></span>
                                <span><strong>Fewer dead ends:</strong> you talk to real, relevant companies.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 size-2 rounded-full bg-emerald-500"></span>
                                <span><strong>Higher signal:</strong> requests and Deal Rooms mean business—not spam.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 size-2 rounded-full bg-emerald-500"></span>
                                <span><strong>Safer sharing:</strong> confidence to exchange pricing, docs, and timelines.</span>
                            </li>
                        </ul>
                    </div>

                    {{-- STATUS PANEL: loud and unmistakable --}}
                    <div class="space-y-4">
                        <x-ts-card>
                            <x-slot name="header" class="flex items-center justify-between">
                                <div class="text-sm font-semibold">Verification status</div>
                                @if($isApproved)
                                    <x-ts-badge color="success" size="sm">
                                        <span class="inline-flex items-center gap-1">
                                            
                                            Verified
                                        </span>
                                    </x-ts-badge>
                                @elseif($isRejected)
                                    <x-ts-badge color="danger" size="sm">
                                        <span class="inline-flex items-center gap-1">
                                            
                                            Not approved
                                        </span>
                                    </x-ts-badge>
                                @else
                                    <x-ts-badge color="warning" size="sm">
                                        <span class="inline-flex items-center gap-1">
                                            
                                            Pending review
                                        </span>
                                    </x-ts-badge>
                                @endif
                            </x-slot>

                            @if($isPending)
                                <div class="space-y-3">
                                    <div class="text-sm text-gray-700 inline-flex items-start gap-2">
                                        
                                        <span>
                                            Your company is <strong>under manual review</strong>. We process new applications daily and
                                            most reviews complete within <strong>one business day</strong>.
                                        </span>
                                    </div>

                                    {{-- Progress bar to imply "in progress" visually --}}
                                    <div class="h-1.5 w-full rounded bg-amber-100 overflow-hidden">
                                        <div class="h-1.5 w-2/3 bg-amber-400 animate-pulse"></div>
                                    </div>

                                    @if($team->kyc_submitted_at)
                                        <div class="text-xs text-amber-700">
                                            Submitted {{ $team->kyc_submitted_at->diffForHumans() }}.
                                        </div>
                                    @endif

                                    {{-- Helpful next step in-line --}}
                                    @if($needsBasics)
                                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-amber-800 text-sm inline-flex items-start gap-2">
                                            
                                            <div>
                                                Add your <strong>Company name</strong>, <strong>Type</strong>, and <strong>HQ Country</strong>,
                                                then click <em>Save</em> to submit.
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-gray-700 text-sm inline-flex items-start gap-2">
                                            
                                            <div>You’ll get an email as soon as your account is approved.</div>
                                        </div>
                                    @endif
                                </div>
                            @elseif($isApproved)
                                <div class="space-y-3">
                                    <div class="text-sm text-emerald-700 inline-flex items-start gap-2">
                                       
                                        <span>
                                            You’re verified — <strong>welcome!</strong> Explore partners, send connection requests, and open Deal Rooms.
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-emerald-700">
                                       
                                        Verified {{ optional($team->kyc_verified_at)->diffForHumans() ?? 'just now' }}.
                                    </div>
                                </div>
                            @elseif($isRejected)
                                <div class="space-y-3">
                                    <div class="text-sm text-rose-700 inline-flex items-start gap-2">
                                       
                                        <span>
                                            We couldn’t approve the company yet. Please review the notes below and update your profile — then save to resubmit.
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </x-ts-card>

                        @if($isRejected && $team->kyc_notes)
                            <x-ts-card>
                                <x-slot name="header" class="text-sm font-semibold text-rose-700">Reviewer notes</x-slot>
                                <div class="text-sm text-rose-700 whitespace-pre-wrap">{{ $team->kyc_notes }}</div>
                            </x-ts-card>
                        @endif
                    </div>
                </div>
            </x-ts-card>

            {{-- Next steps --}}
            <x-ts-card>
                <x-slot name="header">
                    <div class="text-lg font-semibold">What happens next</div>
                </x-slot>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="space-y-2">
                        <div class="text-sm font-semibold">1) Complete basics</div>
                        <p class="text-sm text-gray-600">
                            We need <strong>Company name</strong>, <strong>Type</strong>, and <strong>HQ Country</strong>. Save to submit.
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('companies.profile.edit', $team) }}">
                                <x-ts-button class="btn-accent w-full md:w-auto">
                                    Edit Company Profile
                                </x-ts-button>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-semibold">2) Manual verification</div>
                        <p class="text-sm text-gray-600">
                            Our team reviews new applications daily. You’ll get an email once approved.
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('kyc.gate') }}">
                                <x-ts-button class="btn-subtle w-full md:w-auto">
                                    Check Status
                                </x-ts-button>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-semibold">3) Unlock the club</div>
                        <p class="text-sm text-gray-600">
                            After approval you can access <strong>Explore</strong>, <strong>Connections</strong>, and <strong>Deal Rooms</strong>.
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600">
                            <span class="px-2 py-0.5 rounded-full bg-gray-100">Explore</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100">Connections</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100">Deal Rooms</span>
                        </div>
                    </div>
                </div>
            </x-ts-card>

            {{-- Helpful extras while waiting --}}
            <x-ts-card>
                <x-slot name="header">
                    <div class="text-lg font-semibold">Make the most of your waiting time</div>
                </x-slot>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm font-semibold mb-1">Boost your profile quality</div>
                            <p class="text-sm text-gray-600">
                                A complete profile ranks higher in search and increases response rates.
                                Add a logo, a clear summary, relevant specialties, and certifications.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('companies.profile.edit', $team) }}"><x-ts-button class="btn-outline">Edit Basics</x-ts-button></a>
                            @if(Route::has('companies.intent.edit'))
                                <a href="{{ route('companies.intent.edit', $team) }}"><x-ts-button class="btn-outline">Partner Preferences</x-ts-button></a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <div class="text-sm font-semibold mb-1">Invite teammates</div>
                            <p class="text-sm text-gray-600">
                                Bring colleagues who’ll help manage conversations and deals.
                                Collaboration speeds up cycles and reduces single-point bottlenecks.
                            </p>
                        </div>
                        @if(Route::has('teams.show'))
                            <a href="{{ route('teams.show', $team->id) }}">
                                <x-ts-button class="btn-outline">Team Settings</x-ts-button>
                            </a>
                        @endif
                    </div>
                </div>
            </x-ts-card>

            {{-- Footer/help --}}
            <div class="flex items-start gap-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-gray-700 text-sm inline-flex items-start gap-2 w-full">
                   
                    <div>
                        Questions about verification or timelines?
                        <a href="mailto:support@yourapp.com" class="underline">Contact support</a>.
                        We aim to process every new application within one business day.
                    </div>
                </div>
            </div>

            {{-- Contextual actions based on status --}}
            <div class="flex flex-wrap gap-3">
                @if($needsBasics)
                    <a href="{{ route('companies.profile.edit', $team) }}"><x-ts-button class="btn-accent">Complete Company Basics</x-ts-button></a>
                @elseif($isPending)
                    <a href="{{ route('companies.profile.edit', $team) }}"><x-ts-button class="btn-outline">Update Profile</x-ts-button></a>
                @elseif($isApproved)
                    <a href="{{ route('companies.index') }}"><x-ts-button class="btn-accent">Start Exploring</x-ts-button></a>
                @elseif($isRejected)
                    <a href="{{ route('companies.profile.edit', $team) }}"><x-ts-button class="btn-accent">Fix &amp; Resubmit</x-ts-button></a>
                @endif
                <a href="mailto:support@yourapp.com"><x-ts-button class="btn-subtle">Contact Support</x-ts-button></a>
            </div>

        </div>
    </div>
</x-app-layout>
