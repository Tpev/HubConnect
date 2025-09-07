@php
    use Illuminate\Support\Str;
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- ===== Header ===== --}}
    <section class="grad-hero border-b border-[var(--border)]/80">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <a href="{{ route('openings.show', $opening->slug) }}" class="text-xs text-[var(--brand-700)] hover:underline">← Back to opening</a>

            <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2">
                        <span class="chip-brand">{{ ucfirst($opening->company_type) }}</span>
                        @if($opening->visibility_until)
                            @php $days = $opening->visibility_until->diffInDays(now()); @endphp
                            <span class="chip-accent">{{ $days <= 14 ? 'Closing soon' : 'Open' }}</span>
                        @endif
                    </div>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-semibold tracking-tight text-[var(--ink)]">
                        Apply — {{ $opening->title }}
                    </h1>
                </div>

                {{-- Compact link back to job (desktop) --}}
                <div class="hidden md:block shrink-0">
                    <a href="{{ route('openings.index') }}" class="text-sm text-[var(--brand-700)] hover:underline">
                        Browse all jobs
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Quick facts ===== --}}
    <div class="max-w-5xl mx-auto px-4 py-6">
        <x-ts-card class="p-4 ring-brand bg-white/95">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Compensation --}}
                <div class="rounded-lg ring-1 ring-[var(--brand-200)] bg-[var(--brand-50)]/70 p-3">
                    <div class="text-[10px] uppercase tracking-wide font-bold text-[var(--brand-800)]">Compensation</div>
                    <div class="text-sm font-semibold text-[var(--ink)] leading-tight">
                        {{ $opening->compensation ?: 'Not disclosed' }}
                    </div>
                </div>
                {{-- Posted --}}
                <div class="rounded-lg ring-1 ring-[var(--border)] bg-[var(--panel)] p-3">
                    <div class="text-[10px] uppercase tracking-wide font-bold text-slate-600">Posted</div>
                    <div class="text-sm text-[var(--ink)]">
                        {{ $opening->created_at?->toFormattedDateString() }}
                        <span class="text-slate-500">({{ $opening->created_at?->diffForHumans() }})</span>
                    </div>
                </div>
                {{-- Visibility --}}
                <div class="rounded-lg ring-1 ring-[var(--border)] bg-[var(--panel)] p-3">
                    <div class="text-[10px] uppercase tracking-wide font-bold text-slate-600">Visibility</div>
                    <div class="text-sm text-[var(--ink)]">
                        @if($opening->visibility_until)
                            Until {{ $opening->visibility_until->toDateString() }}
                        @else
                            Open until removed
                        @endif
                    </div>
                </div>
            </div>
        </x-ts-card>
    </div>

    {{-- ===== Body ===== --}}
    <div class="max-w-5xl mx-auto px-4 pb-8 space-y-6">

        {{-- Submitted state --}}
        @if($submitted)
            <x-ts-card class="p-6 ring-brand bg-white/95 space-y-2">
                <div class="text-sm font-semibold text-[var(--brand-700)]">
                    Thanks! Your application has been submitted.
                </div>
                <div class="text-slate-600 text-sm">
                    We’ll review your profile and be in touch.
                    @if($opening->roleplay_policy !== 'disabled')
                        This role uses a roleplay evaluation
                        <strong>({{ $opening->roleplay_policy }})</strong>
                        @if($opening->roleplay_pass_threshold)
                            — target score: <strong>{{ number_format($opening->roleplay_pass_threshold, 2) }}</strong>
                        @endif
                        . You may receive an invite link by email.
                    @endif
                </div>
                <div class="pt-2 flex items-center gap-3">
                    <a href="{{ route('openings.show', $opening->slug) }}" class="btn-brand outline text-sm">Back to job</a>
                    <a href="{{ route('openings.index') }}" class="text-sm text-[var(--brand-700)] hover:underline">Browse all jobs</a>
                </div>
            </x-ts-card>

        @else
            {{-- Application form --}}
            <x-ts-card class="p-6 ring-brand bg-white/95 space-y-5">
                @if($opening->roleplay_policy !== 'disabled')
                    <x-ts-banner class="mb-1">
                        This opening uses a roleplay evaluation
                        <strong>({{ $opening->roleplay_policy }})</strong>.
                        @if($opening->roleplay_pass_threshold)
                            Target score: <strong>{{ number_format($opening->roleplay_pass_threshold, 2) }}</strong>.
                        @endif
                    </x-ts-banner>
                @endif

                <form wire:submit.prevent="submit" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ts-input
                                label="Full name"
                                wire:model.live="candidate_name"
                                placeholder="Jane Doe"
                                autocomplete="name"
                                required
                            />
                            @error('candidate_name') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div>
                            <x-ts-input
                                type="email"
                                label="Email"
                                wire:model.live="email"
                                placeholder="jane.doe@gmail.com"
                                autocomplete="email"
                                inputmode="email"
                                required
                            />
                            @error('email') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div>
                            <x-ts-input
                                label="Mobile (US)"
                                wire:model.live="phone"
                                placeholder="(415) 555-0137"
                                autocomplete="tel"
                                inputmode="tel"
                                pattern="^(\+1\s?)?(\(?\d{3}\)?[\s\.-]?)\d{3}[\s\.-]?\d{4}$"
                            />
                            <p class="text-xs text-slate-500 mt-1">Format: (AAA) BBB-CCCC or 555-555-5555</p>
                            @error('phone') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-ts-input
                                label="Location"
                                wire:model.live="location"
                                placeholder="Austin, TX (USA)"
                                autocomplete="address-level2"
                            />
                            <p class="text-xs text-slate-500 mt-1">Example: “Austin, TX” or “Remote, USA”.</p>
                            @error('location') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-ts-textarea
                                label="Cover letter (optional)"
                                rows="6"
                                wire:model.live="cover_letter"
                                placeholder="Briefly highlight your US market experience, specialties, and availability (e.g., ortho/spine coverage across the Southwest; open to W-2 or 1099)."
                            />
                            @error('cover_letter') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Resume (PDF/DOC, up to 10MB)</label>
                            <input type="file" wire:model="cv" accept=".pdf,.doc,.docx" class="block w-full text-sm">
                            @error('cv') <x-ts-error :text="$message" /> @enderror
                            <div wire:loading wire:target="cv" class="text-xs text-slate-500 mt-1">Uploading…</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('openings.show', $opening->slug) }}" class="btn-brand outline text-sm">
                            Cancel
                        </a>
                        <x-ts-button type="submit" class="btn-accent text-sm" wire:loading.attr="disabled">
                            Submit application
                        </x-ts-button>
                    </div>
                </form>
            </x-ts-card>
        @endif
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
