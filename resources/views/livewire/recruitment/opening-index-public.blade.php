@php
    use Illuminate\Support\Str;
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- ===== Hero ===== --}}
    <section class="grad-hero border-b border-[var(--border)]">
        <div class="max-w-7xl mx-auto px-4 py-10 sm:py-12">
            <div class="text-center space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full chip-brand ring-brand">
                    <span class="text-xs font-semibold tracking-wide">Public board</span>
                    <span class="text-[10px] text-[var(--ink-2)]">No account required</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[var(--ink)]">
                    Open Positions
                </h1>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    Roles from manufacturers and distributors. Filter by specialty, territory, comp, and type.
                </p>
            </div>
        </div>
    </section>

    {{-- ===== Filters / Toolbar ===== --}}
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

        <x-ts-card class="p-5 ring-brand bg-white/90 backdrop-blur">
            <div class="grid grid-cols-1 lg:grid-cols-8 gap-3">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <x-ts-input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search title, description, compensation…"
                        class="w-full"
                        leading-icon="search"
                    />
                </div>

                {{-- Company type segmented control --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Company type</label>
                    <div class="flex rounded-xl ring-1 ring-[var(--border)] overflow-hidden bg-white">
                        <button type="button" wire:click="$set('companyType','all')"
                            class="flex-1 px-3 py-2 text-sm font-semibold transition
                                   {{ $companyType==='all' ? 'bg-[var(--brand-50)] text-[var(--brand-800)] ring-brand' : 'text-slate-700' }}">
                            All
                        </button>
                        <button type="button" wire:click="$set('companyType','manufacturer')"
                            class="flex-1 px-3 py-2 text-sm font-semibold transition
                                   {{ $companyType==='manufacturer' ? 'bg-[var(--brand-50)] text-[var(--brand-800)] ring-brand' : 'text-slate-700' }}">
                            Manufacturer
                        </button>
                        <button type="button" wire:click="$set('companyType','distributor')"
                            class="flex-1 px-3 py-2 text-sm font-semibold transition
                                   {{ $companyType==='distributor' ? 'bg-[var(--brand-50)] text-[var(--brand-800)] ring-brand' : 'text-slate-700' }}">
                            Distributor
                        </button>
                    </div>
                </div>

                {{-- Specialty --}}
                <div class="lg:col-span-2">
                    <x-ts-select.styled
                        label="Specialty"
                        wire:model.live="specialty"
                        :options="$specialtyOptions"
                        select="label:label|value:value"
                        placeholder="Any specialty"
                    />
                </div>

                {{-- Territory --}}
                <div class="lg:col-span-2">
                    <x-ts-select.styled
                        label="Territory"
                        wire:model.live="territory"
                        :options="$territoryOptions"
                        select="label:label|value:value"
                        placeholder="Any territory"
                    />
                </div>

                {{-- Comp structure --}}
                <div class="lg:col-span-2">
                    <x-ts-select.styled
                        label="Comp structure"
                        wire:model.live="compStructure"
                        :options="$compStructureOptions"
                        select="label:label|value:value"
                        placeholder="Any comp structure"
                    />
                </div>

                {{-- Opening type --}}
                <div class="lg:col-span-2">
                    <x-ts-select.styled
                        label="Opening type"
                        wire:model.live="openingType"
                        :options="$openingTypeOptions"
                        select="label:label|value:value"
                        placeholder="Any opening type"
                    />
                </div>
            </div>

            {{-- Secondary toolbar --}}
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-ts-select.styled
                        wire:model.live="sort"
                        label="Sort"
                        :options="$sortOptions"
                        select="label:label|value:value"
                        :clearable="false"
                    />

                    <x-ts-select.styled
                        wire:model.live="perPage"
                        label="Per page"
                        :options="$perPageOptions"
                        select="label:label|value:value"
                        class="w-32"
                        :clearable="false"
                    />
                </div>

                {{-- Active filters --}}
                <div class="flex flex-wrap items-center gap-2">
                    @if($search)      <span class="chip-brand">Search: “{{ $search }}”</span>@endif
                    @if($specialty)   <span class="chip-brand">Specialty: {{ $specialty }}</span>@endif
                    @if($territory)   <span class="chip-accent">Territory: {{ $territory }}</span>@endif
                    @if($compStructure)
                        <span class="chip-brand">
                            Comp: {{ optional(\App\Enums\CompStructure::tryFrom($compStructure))->label() ?? $compStructure }}
                        </span>
                    @endif
                    @if($openingType)
                        <span class="chip-accent">
                            Type: {{ optional(\App\Enums\OpeningType::tryFrom($openingType))->label() ?? $openingType }}
                        </span>
                    @endif

                    @if($search || $specialty || $territory || $companyType!=='all' || $compStructure || $openingType)
                        <button type="button"
                            wire:click="$set('search','');$set('specialty',null);$set('territory',null);$set('companyType','all');$set('compStructure',null);$set('openingType',null)"
                            class="btn-brand outline text-sm">
                            Clear filters
                        </button>
                    @endif

                    <x-ts-button type="button" wire:click="$refresh" class="btn-brand outline">Refresh</x-ts-button>
                </div>
            </div>
        </x-ts-card>

        {{-- ===== Loading skeleton ===== --}}
        <div wire:loading.delay.class.remove="hidden"
             class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @for($i=0; $i<6; $i++)
                <div class="p-5 rounded-2xl ring-1 ring-[var(--border)] bg-[var(--panel)] animate-pulse space-y-3">
                    <div class="h-3 w-24 bg-slate-200 rounded"></div>
                    <div class="h-5 w-3/4 bg-slate-200 rounded"></div>
                    <div class="h-3 w-1/2 bg-slate-200 rounded"></div>
                    <div class="h-3 w-2/3 bg-slate-200 rounded"></div>
                </div>
            @endfor
        </div>

        {{-- ===== Results grid ===== --}}
        <div wire:loading.delay.class="opacity-50"
             class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse ($openings as $o)
                @php
                    // mark if current user (individual) already applied
                    $alreadyApplied = in_array($o->id, $appliedOpeningIds ?? [], true);
                @endphp

                <x-ts-card class="p-5 space-y-4 ring-brand bg-white/90 hover:shadow-md transition">
                    {{-- Top meta --}}
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-semibold text-slate-500">{{ ucfirst($o->company_type) }}</div>
                        <div class="flex items-center gap-2">
                            @if($alreadyApplied)
                                <span class="chip-brand bg-emerald-50 ring-emerald-200 text-emerald-800">Applied</span>
                            @endif
                            @if($o->visibility_until)
                                @php $days = \Carbon\Carbon::parse($o->visibility_until)->diffInDays(now()); @endphp
                                <span class="chip-accent">{{ $days <= 14 ? 'Closing soon' : 'Open' }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Title --}}
                    <a href="{{ route('openings.show', $o->slug) }}" class="block group">
                        <h2 class="text-lg sm:text-xl font-semibold text-[var(--ink)] group-hover:underline">
                            {{ $o->title }}
                        </h2>
                    </a>

                    {{-- Compensation panel --}}
                    <div class="rounded-xl ring-1 ring-[var(--brand-200)] bg-[var(--brand-50)]/70 p-3 space-y-2">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="text-[11px] uppercase tracking-wide font-bold text-[var(--brand-800)]">
                                    Compensation
                                </div>
                                <div class="text-sm sm:text-base font-semibold text-[var(--ink)] leading-tight">
                                    {{ $o->compensation ?: 'Not disclosed' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @if($o->comp_structure)
                                <span class="chip-brand">
                                    {{ $o->comp_structure?->label() ?? Str::headline((string)$o->comp_structure) }}
                                </span>
                            @endif
                            @if($o->opening_type)
                                <span class="chip-accent">
                                    {{ $o->opening_type?->label() ?? Str::upper((string)$o->opening_type) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(($o->specialty_ids ?? []) as $spec)
                            <span class="badge-brand">{{ $spec }}</span>
                        @endforeach
                        @foreach(($o->territory_ids ?? []) as $terr)
                            <span class="badge-accent">{{ $terr }}</span>
                        @endforeach
                    </div>

                    {{-- Excerpt --}}
                    @if($o->description)
                        <p class="text-sm text-slate-600">
                            {{ Str::limit(strip_tags($o->description), 160) }}
                        </p>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Posted {{ $o->created_at?->diffForHumans() }}</span>
                        @if($o->visibility_until)
                            <span>Visible until {{ \Carbon\Carbon::parse($o->visibility_until)->toDateString() }}</span>
                        @endif
                    </div>

                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('openings.show', $o->slug) }}" class="btn-brand inline-flex items-center gap-2">
                            View details
                            <x-ts-icon name="arrow-right" />
                        </a>

                        @auth
                            @if($viewerType === 'individual' && Route::has('openings.apply'))
                                @if(!$alreadyApplied)
                                    <a href="{{ route('openings.apply', $o->slug) }}" class="btn-accent inline-flex items-center gap-2">
                                        Apply
                                    </a>
                                @else
                                    <button type="button" disabled
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed"
                                        title="You have already applied">
                                        Applied
                                        <x-ts-icon name="check" />
                                    </button>
                                @endif
                            @endif
                        @endauth
                    </div>
                </x-ts-card>
            @empty
                <x-ts-card class="p-8 md:col-span-2 xl:col-span-3 text-center ring-brand">
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">No openings match your filters</h3>
                        <p class="text-slate-600 text-sm">Try clearing filters or check back soon.</p>
                        <button type="button" class="btn-accent outline"
                            wire:click="$set('search','');$set('specialty',null);$set('territory',null);$set('companyType','all');$set('compStructure',null);$set('openingType',null)">
                            Clear all filters
                        </button>
                    </div>
                </x-ts-card>
            @endforelse
        </div>

        {{-- ===== Pagination ===== --}}
        <div class="flex items-center justify-center pt-4">
            {{ $openings->onEachSide(1)->links() }}
        </div>
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
