<div class="max-w-7xl mx-auto space-y-6">

    {{-- Sticky Toolbar + Stats --}}
    <div class="sticky top-0 z-10 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b">
        <div class="px-4 sm:px-0 max-w-7xl mx-auto">
            <div class="py-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900">Companies Directory</h1>
                    <p class="mt-0.5 text-xs text-slate-500">
                        <span class="me-3">Matching: <span class="font-medium text-slate-700">{{ number_format($stats['matching']) }}</span></span>
                        <span class="me-3">Active intents: <span class="font-medium text-slate-700">{{ number_format($stats['activeIntents']) }}</span></span>
                        <span>Total listed: <span class="font-medium text-slate-700">{{ number_format($stats['totalListed']) }}</span></span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <x-ts-select.styled
                        wire:model="sort"
                        :options="[
                            ['label'=>'Recently updated','value'=>'recent'],
                            ['label'=>'Name (A–Z)','value'=>'name'],
                        ]"
                        class="w-44"
                    />
                    <x-ts-select.styled
                        label="Show"
                        wire:model="perPage"
                        :options="[
                            ['label'=>'12 / page','value'=>12],
                            ['label'=>'24 / page','value'=>24],
                            ['label'=>'48 / page','value'=>48],
                        ]"
                        class="w-36"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <x-ts-card class="ring-brand">
        <x-slot name="header" class="font-semibold text-lg">Find the right partner</x-slot>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <x-ts-input
                label="Keyword"
                wire:model.debounce.400ms="q"
                placeholder="Company name, summary, website"
                class="lg:col-span-2"
            />

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1">
                    @php
                        $roleOptions = [
                            [null,'Any','any'],
                            ['manufacturer','Manufacturer','manufacturer'],
                            ['distributor','Distributor','distributor'],
                            ['both','Both','both'],
                        ];
                    @endphp
                    @foreach($roleOptions as [$val,$label,$key])
                        <button
                            wire:click="$set('role', @js($val))"
                            type="button"
                            class="px-2 py-1.5 rounded-lg text-sm border
                                   {{ ($role===$val || ($val===null && $role===null)) ? 'border-brand bg-brand/10 text-brand-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                            {{ $label }}
                            <span class="ms-1 text-[11px] text-slate-500">({{ $facet[$key] ?? 0 }})</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <x-ts-select.styled
                label="Territory"
                wire:model="territory"
                :options="$countries"
                searchable
                placeholder="Any"
            />

            <x-ts-select.styled
                label="Specialty"
                wire:model="specialty"
                :options="$allSpecialties->map(fn($s)=>['label'=>$s->name,'value'=>$s->id])->toArray()"
                searchable
                placeholder="Any"
            />

            <x-ts-select.styled
                label="Certification"
                wire:model="cert"
                :options="$allCerts->map(fn($c)=>['label'=>$c->name,'value'=>$c->id])->toArray()"
                searchable
                placeholder="Any"
            />
        </div>

        {{-- Filter Chips --}}
        @php
            $activeChips = [];
            if($q)              $activeChips[] = ['label'=>"Keyword: “$q”", 'prop'=>'q', 'val'=>null];
            if($role)           $activeChips[] = ['label'=>"Role: ".ucfirst($role), 'prop'=>'role', 'val'=>null];
            if($territory) {
                $map = collect($countries)->mapWithKeys(fn($c)=>[$c['value'] => $c['label']]);
                $activeChips[] = ['label'=>"Territory: ".$map->get($territory, $territory), 'prop'=>'territory', 'val'=>null];
            }
            if($specialty) {
                $sp = $allSpecialties->firstWhere('id',$specialty)?->name ?? $specialty;
                $activeChips[] = ['label'=>"Specialty: $sp", 'prop'=>'specialty', 'val'=>null];
            }
            if($cert) {
                $ct = $allCerts->firstWhere('id',$cert)?->name ?? $cert;
                $activeChips[] = ['label'=>"Certification: $ct", 'prop'=>'cert', 'val'=>null];
            }
            if($sort !== 'recent') $activeChips[] = ['label'=>'Sorted by Name', 'prop'=>'sort', 'val'=>'recent'];
            if($perPage !== 12)    $activeChips[] = ['label'=>"$perPage / page", 'prop'=>'perPage', 'val'=>12];
        @endphp

        @if(count($activeChips))
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($activeChips as $chip)
                    <button
                        wire:click="$set('{{ $chip['prop'] }}', @js($chip['val']))"
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-200">
                        {{ $chip['label'] }}
                        <x-ts-icon name="x-mark" class="w-3.5 h-3.5"/>
                    </button>
                @endforeach

                <button wire:click="clearFilters" class="ms-1 inline-flex items-center gap-1 rounded-full bg-slate-50 px-2.5 py-1 text-xs text-slate-600 hover:bg-slate-100">
                    Clear all
                    <x-ts-icon name="arrow-path" class="w-3.5 h-3.5"/>
                </button>
            </div>
        @endif
    </x-ts-card>

    {{-- Skeleton --}}
    <div wire:loading.delay>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @for($i=0;$i<6;$i++)
                <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4 animate-pulse">
                    <div class="flex items-start gap-3">
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
        @endphp

        @forelse ($companies as $company)
            @php
                $viewerId   = auth()->user()?->currentTeam?->id;
                $connected  = $viewerId ? \App\Models\CompanyConnection::areConnected($viewerId, $company->id) : false;
                $hasPending = $viewerId ? \App\Models\MatchRequest::where('status','pending')
                                ->whereIn('from_company_id', [$viewerId, $company->id])
                                ->whereIn('to_company_id',   [$viewerId, $company->id])
                                ->exists() : false;
                $intent     = $company->activeIntent();
                $websiteHost= $company->website ? parse_url($company->website, PHP_URL_HOST) : null;
            @endphp

            <x-ts-card class="ring-brand hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start gap-3">
                    @if ($company->team_profile_photo_path)
                        <img src="{{ Storage::url($company->team_profile_photo_path) }}"
                             class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200"
                             alt="{{ $company->name }}">
                    @else
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 grid place-items-center font-semibold text-slate-600 ring-1 ring-slate-200">
                            {{ \Illuminate\Support\Str::of($company->name)->substr(0,1) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <a href="{{ route('companies.show', $company) }}" class="font-semibold hover:underline text-slate-900">
                            {{ $company->name }}
                        </a>
                        <div class="mt-0.5 text-xs text-slate-500">
                            {{ ucfirst($company->company_type ?? '—') }}
                            @if($websiteHost)
                                • <a href="{{ $company->website }}" target="_blank" class="hover:underline">{{ $websiteHost }}</a>
                            @endif
                            @if($company->hq_country_label !== '—')
                                • HQ: {{ $company->hq_country_label }}
                            @endif
                        </div>

                        @if($intent)
                            @php $p = $intent->payload; @endphp
                            <div class="mt-3 text-sm text-slate-700">
                                <span class="font-medium">Actively seeking</span>
                                @if(!empty($p['specialties']))
                                    {{ \App\Models\Specialty::whereIn('id',$p['specialties'])->pluck('name')->join(', ') }}
                                @endif
                                @if(!empty($p['territories']))
                                    @if(!empty($p['specialties'])) in @else in @endif
                                    {{ collect($p['territories'])->map(fn($code) => $countryMap[$code] ?? $code)->join(', ') }}
                                @endif
                                <span class="text-xs text-slate-500"> • updated {{ \Illuminate\Support\Carbon::parse($intent->updated_at)->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>

                    @if($viewerId && $viewerId !== $company->id)
                        @if($connected)
                            <span class="ms-auto chip-brand">Connected</span>
                        @elseif($hasPending)
                            <span class="ms-auto chip-accent">Pending</span>
                        @endif
                    @endif
                </div>

                {{-- Specialties line --}}
                @if ($company->specialties->count())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($company->specialties->take(4) as $s)
                            <span class="badge-brand">{{ $s->name }}</span>
                        @endforeach
                        @if ($company->specialties->count() > 4)
                            <span class="badge-brand">+{{ $company->specialties->count() - 4 }}</span>
                        @endif
                    </div>
                @endif

                {{-- CTA row --}}
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-xs text-slate-500 line-clamp-2 pr-4">
                        {{ \Illuminate\Support\Str::limit($company->summary ?? '—', 160) }}
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('companies.show', $company) }}" class="btn-accent">View</a>

                        @if ($viewerId && $company->id !== $viewerId)
                            <a
                                href="{{ $connected || $hasPending ? '#' : route('companies.show', [$company, 'compose' => 1]) }}"
                                class="btn-brand {{ ($connected || $hasPending) ? 'outline cursor-not-allowed opacity-60 pointer-events-none' : '' }}"
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
                    <div class="mt-3">
                        <x-ts-button class="btn-accent" wire:click="clearFilters">Clear all filters</x-ts-button>
                    </div>
                </div>
            </x-ts-card>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $companies->links() }}
    </div>
</div>
