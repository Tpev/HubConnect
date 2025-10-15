<div class="space-y-4">
    {{-- Loading skeleton --}}
    <div wire:loading.delay>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @for($i=0;$i<6;$i++)
                <div class="relative rounded-2xl ring-1 ring-slate-200 bg-white p-4 animate-pulse">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
                    <div class="flex items-start gap-3 mt-2">
                        <div class="w-12 h-12 bg-slate-200 rounded-lg"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                        </div>
                        <div class="w-24 h-8 bg-slate-200 rounded"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Results --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" wire:loading.remove>
        @php
            $countryMap = collect($countries ?? [])->mapWithKeys(fn($c) => [$c['value'] => $c['label']]);
            $viewerId   = auth()->user()?->currentTeam?->id;
        @endphp

        @forelse ($companies as $company)
            @php
                $connected   = $viewerId ? \App\Models\CompanyConnection::areConnected($viewerId, $company->id) : false;
                $hasPending  = $viewerId ? \App\Models\MatchRequest::where('status','pending')
                                    ->whereIn('from_company_id', [$viewerId, $company->id])
                                    ->whereIn('to_company_id',   [$viewerId, $company->id])
                                    ->exists() : false;
                $intent      = $company->activeIntent();
                $websiteHost = $company->website ? parse_url($company->website, PHP_URL_HOST) : null;

                // Specialty color cycle (keeps the row visually scannable)
                $chipPalettes = [
                    ['bg' => 'bg-emerald-50', 'ring' => 'ring-emerald-200', 'text' => 'text-emerald-900', 'icon' => 'text-emerald-700'],
                    ['bg' => 'bg-sky-50',     'ring' => 'ring-sky-200',     'text' => 'text-sky-900',     'icon' => 'text-sky-700'],
                    ['bg' => 'bg-amber-50',   'ring' => 'ring-amber-200',   'text' => 'text-amber-900',   'icon' => 'text-amber-700'],
                    ['bg' => 'bg-violet-50',  'ring' => 'ring-violet-200',  'text' => 'text-violet-900',  'icon' => 'text-violet-700'],
                    ['bg' => 'bg-rose-50',    'ring' => 'ring-rose-200',    'text' => 'text-rose-900',    'icon' => 'text-rose-700'],
                    ['bg' => 'bg-teal-50',    'ring' => 'ring-teal-200',    'text' => 'text-teal-900',    'icon' => 'text-teal-700'],
                ];
            @endphp

            <x-ts-card class="relative overflow-hidden hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 group">
                {{-- Slim emerald top accent w/ subtle glow on hover --}}
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400 transition-opacity duration-200"></div>
                <div class="absolute inset-x-0 top-0 h-1 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-emerald-500 via-emerald-400 to-emerald-300 blur-[2px]"></div>

                <div class="p-4 pt-5">
                    {{-- Header: logo + name + relationship --}}
                    <div class="flex items-start gap-3">
                        @if ($company->team_profile_photo_path)
                            <img src="{{ Storage::url($company->team_profile_photo_path) }}"
                                 class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200"
                                 alt="{{ $company->name }}">
                        @else
                            <div class="w-12 h-12 rounded-lg grid place-items-center font-semibold text-slate-600 ring-1 ring-slate-200 bg-slate-50">
                                {{ \Illuminate\Support\Str::of($company->name)->substr(0,1) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('companies.show', $company) }}"
                               class="block text-slate-900 font-semibold tracking-tight hover:underline decoration-emerald-500/60 underline-offset-2">
                                {{ $company->name }}
                            </a>

                            <div class="mt-1 text-xs text-slate-500 flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="inline-flex items-center gap-1">
                                    <x-ts-icon name="building-office-2" class="w-3.5 h-3.5"/>
                                    {{ ucfirst($company->company_type ?? '—') }}
                                </span>

                                @if($websiteHost)
                                    <span class="text-slate-300">•</span>
                                    <a href="{{ $company->website }}" target="_blank"
                                       class="inline-flex items-center gap-1 hover:underline decoration-slate-300 underline-offset-2">
                                        <x-ts-icon name="globe-alt" class="w-3.5 h-3.5"/>
                                        {{ $websiteHost }}
                                    </a>
                                @endif

                                @if($company->hq_country_label !== '—')
                                    <span class="text-slate-300">•</span>
                                    <span class="inline-flex items-center gap-1">
                                        <x-ts-icon name="map-pin" class="w-3.5 h-3.5"/>
                                        {{ $company->hq_country_label }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($viewerId && $viewerId !== $company->id)
                            @if($connected)
                                <span class="ms-auto chip-brand">Connected</span>
                            @elseif($hasPending)
                                <span class="ms-auto chip-accent">Pending</span>
                            @endif
                        @endif
                    </div>

                    {{-- Quick facts --}}
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] text-slate-600">
                        @if($company->year_founded)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 ring-1 ring-slate-200">
                                <x-ts-icon name="calendar" class="w-3 h-3"/> Founded {{ $company->year_founded }}
                            </span>
                        @endif
                        @if($company->headcount)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 ring-1 ring-slate-200">
                                <x-ts-icon name="users" class="w-3 h-3"/> {{ $company->headcount }} ppl
                            </span>
                        @endif
                        @if($company->stage)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 ring-1 ring-slate-200">
                                <x-ts-icon name="sparkles" class="w-3 h-3"/> {{ ucfirst($company->stage) }}
                            </span>
                        @endif
                        @if($company->certifications->count())
                            @php $firstCerts = $company->certifications->take(2)->pluck('name'); @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 ring-1 ring-slate-200">
                                <x-ts-icon name="shield-check" class="w-3 h-3"/>
                                {{ $firstCerts->join(', ') }}@if($company->certifications->count() > 2) +{{ $company->certifications->count() - 2 }}@endif
                            </span>
                        @endif
                    </div>

                    {{-- 🔹 Specialty ribbon (moved BEFORE description) --}}
                    @if ($company->specialties->count())
                        <div class="mt-3 -mx-1 flex flex-wrap items-center">
                            @foreach ($company->specialties->take(6)->values() as $idx => $s)
                                @php $pal = $chipPalettes[$idx % count($chipPalettes)]; @endphp
                                <span
                                    class="mx-1 my-1 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium
                                           {{ $pal['bg'] }} {{ $pal['ring'] }} {{ $pal['text'] }} ring-1
                                           transition-transform duration-150 hover:scale-[1.03] will-change-transform">
                                    <x-ts-icon name="tag" class="w-3.5 h-3.5 {{ $pal['icon'] }}"/>
                                    {{ $s->name }}
                                </span>
                            @endforeach

                            @if ($company->specialties->count() > 6)
                                <span class="mx-1 my-1 inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-3 py-1 text-xs font-medium ring-1 ring-slate-200 text-slate-700">
                                    +{{ $company->specialties->count() - 6 }}
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Summary --}}
                    <p class="mt-2 text-[13px] leading-5 text-slate-700">
                        {{ \Illuminate\Support\Str::limit($company->summary ?? '—', 200) }}
                    </p>

                    {{-- Actively looking for --}}
                    @if($intent)
                        @php $p = $intent->payload; @endphp
                        <div class="mt-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 p-3 transition duration-200 group-hover:ring-emerald-300">
                            <div class="text-[13px] text-emerald-900">
                                <span class="font-medium inline-flex items-center gap-1.5">
                                    <x-ts-icon name="bolt" class="w-4 h-4 text-emerald-700"/>
                                    Actively looking for
                                </span>
                                <span class="text-xs text-emerald-700/70">
                                    • updated {{ \Illuminate\Support\Carbon::parse($intent->updated_at)->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Title-ish urgency/timeline --}}
                            @if(!empty($p['urgency']))
                                <div class="mt-2 text-[13px] font-semibold text-emerald-900 leading-5">
                                    {{ $p['urgency'] }}
                                </div>
                            @endif

                            {{-- Items under urgency --}}
                            <div class="mt-2 flex flex-wrap gap-2">
                                {{-- Specialties --}}
                                @if(!empty($p['specialties']))
                                    @php $seekSpecs = \App\Models\Specialty::whereIn('id', $p['specialties'])->pluck('name'); @endphp
                                    @foreach($seekSpecs as $sName)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-emerald-200 text-emerald-800 transition-transform duration-150 hover:scale-[1.03]">
                                            <x-ts-icon name="tag" class="w-3.5 h-3.5 text-emerald-700"/>
                                            {{ $sName }}
                                        </span>
                                    @endforeach
                                @endif

                                {{-- Territories --}}
                                @if(!empty($p['territories']))
                                    @foreach(collect($p['territories']) as $code)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-emerald-200 text-emerald-800 transition-transform duration-150 hover:scale-[1.03]">
                                            <x-ts-icon name="map" class="w-3.5 h-3.5 text-emerald-700"/>
                                            {{ $countryMap[$code] ?? $code }}
                                        </span>
                                    @endforeach
                                @endif

                                {{-- Optional prefs --}}
                                @if(data_get($p,'pref_exclusivity') !== null)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-emerald-200 text-emerald-800">
                                        <x-ts-icon name="lock-closed" class="w-3.5 h-3.5 text-emerald-700"/>
                                        Exclusivity: {{ $p['pref_exclusivity'] ? 'Yes' : 'No' }}
                                    </span>
                                @endif
                                @if(data_get($p,'pref_consignment') !== null)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-emerald-200 text-emerald-800">
                                        <x-ts-icon name="banknotes" class="w-3.5 h-3.5 text-emerald-700"/>
                                        Consignment: {{ $p['pref_consignment'] ? 'Yes' : 'No' }}
                                    </span>
                                @endif
                                @if(!empty($p['pref_commission_min']))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-emerald-200 text-emerald-800">
                                        <x-ts-icon name="chart-bar" class="w-3.5 h-3.5 text-emerald-700"/>
                                        Min commission: {{ (int)$p['pref_commission_min'] }}%
                                    </span>
                                @endif
                            </div>

                            {{-- Capacity notes (no label) --}}
                            @if(!empty($p['capacity_note']))
                                <p class="mt-3 text-[13px] leading-5 text-emerald-900">
                                    {{ \Illuminate\Support\Str::limit($p['capacity_note'], 220) }}
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Footer CTAs --}}
                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('companies.show', $company) }}" class="btn-accent transition-transform duration-150 hover:-translate-y-0.5">View</a>
                        @if ($viewerId && $company->id !== $viewerId)
                            <a
                                href="{{ $connected || $hasPending ? '#' : route('companies.show', [$company, 'compose' => 1]) }}"
                                class="btn-brand {{ ($connected || $hasPending) ? 'outline cursor-not-allowed opacity-60 pointer-events-none' : '' }}
                                       transition-transform duration-150 hover:-translate-y-0.5"
                                @if($connected || $hasPending) aria-disabled="true" tabindex="-1" @endif
                            >
                                {{ $connected ? 'Connected' : ($hasPending ? 'Request Pending' : 'Request Intro') }}
                            </a>
                        @endif
                    </div>
                </div>
            </x-ts-card>
        @empty
            <x-ts-card class="ring-brand">
                <div class="py-6 text-center">
                    <x-ts-icon name="magnifying-glass" class="w-8 h-8 mx-auto text-slate-400"/>
                    <p class="mt-2 font-medium text-slate-800">No companies match your filters.</p>
                    <p class="text-sm text-slate-500">Try removing some filters or widening your search.</p>
                </div>
            </x-ts-card>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $companies->links() }}
    </div>
</div>
