{{-- resources/views/livewire/recruitment/opening-show-public.blade.php --}}

@php
    use Illuminate\Support\Str;

    // Helper formatters that work for string or enum objects.
    $fmtComp = function($value) {
        if (!$value) return null;
        if (is_object($value) && method_exists($value, 'label')) return $value->label();
        return Str::headline((string) $value);
    };

    $fmtType = function($value) {
        if (!$value) return null;
        if (is_object($value) && method_exists($value, 'label')) return $value->label();
        $s = (string) $value;
        return in_array(strtolower($s), ['w2','1099']) ? Str::upper($s) : Str::headline($s);
    };
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- ===== Header / Title ===== --}}
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

                {{-- Apply / Manage (desktop) --}}
                <div class="hidden md:block shrink-0">
                    @auth
                        @if($viewerType === 'individual' && Route::has('openings.apply'))
                            @if(!$hasApplied)
                                <a href="{{ route('openings.apply', $opening->slug) }}"
                                   class="btn-accent inline-flex items-center gap-2 text-sm"
                                   style="padding:.5rem 1rem;">
                                    Apply
                                    <x-ts-icon name="arrow-right" />
                                </a>
                            @else
                                <button type="button" disabled
                                    class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed"
                                    title="You have already applied">
                                    Already applied
                                    <x-ts-icon name="check" />
                                </button>
                            @endif
                        @endif

                        @if($viewerType === 'company' && Route::has('employer.openings'))
                            <a href="{{ route('employer.openings') }}"
                               class="btn-brand outline inline-flex items-center gap-2 text-sm"
                               style="padding:.5rem 1rem;">
                                Manage openings
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Tags row --}}
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

    {{-- ===== Content ===== --}}
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

        {{-- Banner if already applied --}}
        @auth
            @if($viewerType === 'individual' && $hasApplied && $myApplication)
                <div class="p-4 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-900">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                        <div class="min-w-0">
                            <div class="font-semibold">You already applied to this opening.</div>
                            <div class="text-sm">
                                Submitted {{ $myApplication->created_at?->toDayDateTimeString() ?? 'earlier' }}.
                                @if(Route::has('applications.show'))
                                    <a href="{{ route('applications.show', $myApplication->id) }}" class="underline">View your application</a>.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        {{-- Quick facts --}}
        <x-ts-card class="p-6 ring-brand bg-white/95">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Compensation --}}
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

        {{-- Description --}}
        <x-ts-card class="p-6 ring-brand bg-white/95 space-y-4">
            <div class="prose max-w-none">
                {!! nl2br(e($opening->description)) !!}
            </div>
        </x-ts-card>

        {{-- Bottom actions (mobile first) --}}
        <div class="md:hidden">
            @auth
                @if($viewerType === 'individual' && Route::has('openings.apply'))
                    @if(!$hasApplied)
                        <a href="{{ route('openings.apply', $opening->slug) }}"
                           class="btn-accent w-full inline-flex items-center justify-center gap-2 text-sm"
                           style="padding:.6rem 1rem;">
                            Apply
                            <x-ts-icon name="arrow-right" />
                        </a>
                    @else
                        <button type="button" disabled
                                class="w-full inline-flex items-center justify-center gap-2 text-sm px-4 py-2 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed">
                            Already applied
                            <x-ts-icon name="check" />
                        </button>
                    @endif
                @endif
            @endauth
        </div>

        {{-- Secondary actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('openings.index') }}" class="text-sm text-[var(--brand-700)] hover:underline">← Back to openings</a>
            @auth
                @if($viewerType === 'individual' && Route::has('openings.apply') && !$hasApplied)
                    <a href="{{ route('openings.apply', $opening->slug) }}"
                       class="hidden md:inline-flex items-center gap-1.5 text-[var(--brand-700)] hover:underline text-sm">
                        Apply now
                        <x-ts-icon name="arrow-right" />
                    </a>
                @endif
            @endauth
        </div>
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
