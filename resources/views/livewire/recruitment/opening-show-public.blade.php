@php
    use Illuminate\Support\Str;
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- ===== Header / Title ===== --}}
    <section class="grad-hero border-b border-[var(--border)]/80">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <a href="{{ route('openings.index') }}" class="text-xs text-[var(--brand-700)] hover:underline">← Back to openings</a>
                    <div class="mt-1 inline-flex items-center gap-2">
                        <span class="chip-brand">{{ ucfirst($opening->company_type) }}</span>
                        @if($opening->visibility_until)
                            @php $days = $opening->visibility_until->diffInDays(now()); @endphp
                            <span class="chip-accent">{{ $days <= 14 ? 'Closing soon' : 'Open' }}</span>
                        @endif
                    </div>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-semibold tracking-tight text-[var(--ink)]">
                        {{ $opening->title }}
                    </h1>
                </div>

                {{-- Apply (compact, desktop only) --}}
                <div class="hidden md:block shrink-0">
                    <a href="{{ route('openings.apply', $opening->slug) }}"
                       class="btn-accent inline-flex items-center gap-2 text-sm"
                       style="padding:.4rem .8rem;">
                        Apply
                        <x-ts-icon name="arrow-right" />
                    </a>
                </div>
            </div>

            {{-- Tags row (specialties / territories) --}}
            @if(($opening->specialty_ids && count($opening->specialty_ids)) || ($opening->territory_ids && count($opening->territory_ids)))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach(($opening->specialty_ids ?? []) as $spec)
                        <span class="badge-brand">{{ $spec }}</span>
                    @endforeach
                    @foreach(($opening->territory_ids ?? []) as $terr)
                        <span class="badge-accent">{{ $terr }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===== Content ===== --}}
    <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

        {{-- Quick facts (compact) --}}
        <x-ts-card class="p-4 ring-brand bg-white/95">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Compensation (emphasized) --}}
                <div class="rounded-lg ring-1 ring-[var(--brand-200)] bg-[var(--brand-50)]/70 p-3">
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-[2px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/>
                        </svg>
                        <div class="min-w-0">
                            <div class="text-[10px] uppercase tracking-wide font-bold text-[var(--brand-800)]">Compensation</div>
                            @if($opening->compensation)
                                <div class="text-sm font-semibold text-[var(--ink)] leading-tight">
                                    {{ $opening->compensation }}
                                </div>
                            @else
                                <div class="text-sm text-slate-600">Not disclosed</div>
                            @endif
                        </div>
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

        {{-- Description --}}
        <x-ts-card class="p-5 ring-brand bg-white/95 space-y-4">
            <div class="prose prose-sm max-w-none">
                {!! nl2br(e($opening->description)) !!}
            </div>

            @if($opening->roleplay_policy !== 'disabled')
                <x-ts-banner class="mt-2">
                    This opening uses a roleplay evaluation
                    @if($opening->roleplay_policy === 'required')
                        <strong>(required)</strong>
                    @else
                        <strong>(optional)</strong>
                    @endif
                    @if($opening->roleplay_pass_threshold)
                        — Target score: <strong>{{ number_format($opening->roleplay_pass_threshold, 2) }}</strong>
                    @endif
                </x-ts-banner>
            @endif
        </x-ts-card>

        {{-- Bottom actions (mobile first) --}}
        <div class="md:hidden">
            <a href="{{ route('openings.apply', $opening->slug) }}"
               class="btn-accent w-full inline-flex items-center justify-center gap-2 text-sm"
               style="padding:.5rem .9rem;">
                Apply
                <x-ts-icon name="arrow-right" />
            </a>
        </div>

        {{-- Secondary actions --}}
        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('openings.index') }}" class="text-sm text-[var(--brand-700)] hover:underline">← Back to openings</a>
            <a href="{{ route('openings.apply', $opening->slug) }}"
               class="hidden md:inline-flex items-center gap-1.5 text-[var(--brand-700)] hover:underline text-sm">
                Apply now
                <x-ts-icon name="arrow-right" />
            </a>
        </div>
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
