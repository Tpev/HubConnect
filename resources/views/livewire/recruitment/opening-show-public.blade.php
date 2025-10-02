@php
    use Illuminate\Support\Str;

    // Helper formatters that work for string or enum objects.
    $fmtComp = function($value) {
        if (!$value) return null;
        if (is_object($value) && method_exists($value, 'label')) return $value->label();
        return Str::headline((string) $value); // "salary_commission" → "Salary Commission"
    };

    $fmtType = function($value) {
        if (!$value) return null;
        if (is_object($value) && method_exists($value, 'label')) return $value->label();
        $s = (string) $value;
        // Normalize to common display (W2/1099 caps, others headline)
        return in_array(strtolower($s), ['w2','1099']) ? Str::upper($s) : Str::headline($s);
    };
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- ===== Header / Title (spacious) ===== --}}
    <section class="grad-hero border-b border-[var(--border)]/80">
        <div class="max-w-7xl mx-auto px-4 py-10 sm:py-12">
            <div class="flex items-start justify-between gap-6">
                <div class="min-w-0">
                    <a href="{{ route('openings.index') }}" class="text-xs text-[var(--brand-700)] hover:underline">← Back to openings</a>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="chip-brand">{{ ucfirst($opening->company_type) }}</span>

                        @if($opening->opening_type)
                            <span class="chip-accent">{{ $fmtType($opening->opening_type) }}</span>
                        @endif

                        @if($opening->visibility_until)
                            @php $days = $opening->visibility_until->diffInDays(now()); @endphp
                            <span class="chip-accent">{{ $days <= 14 ? 'Closing soon' : 'Open' }}</span>
                        @endif
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-[var(--ink)]">
                        {{ $opening->title }}
                    </h1>
                </div>

                {{-- Apply (compact, desktop only) --}}
                <div class="hidden md:block shrink-0">
                    <a href="{{ route('openings.apply', $opening->slug) }}"
                       class="btn-accent inline-flex items-center gap-2 text-sm"
                       style="padding:.5rem 1rem;">
                        Apply
                        <x-ts-icon name="arrow-right" />
                    </a>
                </div>
            </div>

            {{-- Tags row (specialties / territories) --}}
            @if(($opening->specialty_ids && count($opening->specialty_ids)) || ($opening->territory_ids && count($opening->territory_ids)))
                <div class="mt-4 flex flex-wrap gap-1.5">
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

    {{-- ===== Content (spacious) ===== --}}
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

        {{-- Quick facts (roomier paddings) --}}
        <x-ts-card class="p-6 ring-brand bg-white/95">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Compensation (emphasized) --}}
                <div class="rounded-xl ring-1 ring-[var(--brand-200)] bg-[var(--brand-50)]/70 p-4">
                    <div class="text-[11px] uppercase tracking-wide font-bold text-[var(--brand-800)]">Compensation</div>
                    <div class="mt-1">
                        @if($opening->compensation)
                            <div class="text-base font-semibold text-[var(--ink)] leading-tight">
                                {{ $opening->compensation }}
                            </div>
                        @else
                            <div class="text-sm text-slate-600">Not disclosed</div>
                        @endif
                        @if($opening->comp_structure)
                            <div class="pt-2">
                                <span class="chip-brand">{{ $fmtComp($opening->comp_structure) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Employment (Opening type) --}}
                <div class="rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] p-4">
                    <div class="text-[11px] uppercase tracking-wide font-bold text-slate-600">Employment</div>
                    <div class="mt-1 text-sm text-[var(--ink)]">
                        @if($opening->opening_type)
                            <span class="chip-accent">{{ $fmtType($opening->opening_type) }}</span>
                        @else
                            <span class="text-slate-600">Not specified</span>
                        @endif
                    </div>
                </div>

                {{-- Posted --}}
                <div class="rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] p-4">
                    <div class="text-[11px] uppercase tracking-wide font-bold text-slate-600">Posted</div>
                    <div class="mt-1 text-sm text-[var(--ink)]">
                        {{ $opening->created_at?->toFormattedDateString() }}
                        <span class="text-slate-500">({{ $opening->created_at?->diffForHumans() }})</span>
                    </div>
                </div>

                {{-- Visibility --}}
                <div class="rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] p-4">
                    <div class="text-[11px] uppercase tracking-wide font-bold text-slate-600">Visibility</div>
                    <div class="mt-1 text-sm text-[var(--ink)]">
                        @if($opening->visibility_until)
                            Until {{ $opening->visibility_until->toDateString() }}
                        @else
                            Open until removed
                        @endif
                    </div>
                </div>
            </div>
        </x-ts-card>

        {{-- Description (larger type) --}}
        <x-ts-card class="p-6 ring-brand bg-white/95 space-y-4">
            <div class="prose max-w-none">
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
               style="padding:.6rem 1rem;">
                Apply
                <x-ts-icon name="arrow-right" />
            </a>
        </div>

        {{-- Secondary actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('openings.index') }}" class="text-sm text-[var(--brand-700)] hover:underline">← Back to openings</a>
            <a href="{{ route('openings.apply', $opening->slug) }}"
               class="hidden md:inline-flex items-center gap-1.5 text-[var(--brand-700)] hover:underline text-sm">
                Apply now
                <x-ts-icon name="arrow-right" />
            </a>
        </div>
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
