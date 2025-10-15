{{-- Company Show (refined) --}}
<div class="max-w-5xl mx-auto space-y-6" wire:init>
    {{-- Loading skeleton --}}
    <div wire:loading.delay>
        <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-5 animate-pulse">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-xl bg-slate-200"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-6 bg-slate-200 rounded w-1/3"></div>
                    <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                </div>
                <div class="w-32 h-10 bg-slate-200 rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('msg'))
        <x-ts-alert color="green" class="ring-brand">{{ session('msg') }}</x-ts-alert>
    @endif

    @php
        $websiteHost = $company->website ? (parse_url($company->website, PHP_URL_HOST) ?: $company->website) : null;
        $hq          = method_exists($company, 'getHqCountryLabelAttribute') ? $company->hq_country_label : ($company->hq_country ?: null);
        $intent      = isset($intent) ? $intent : $company->activeIntent();
    @endphp

    {{-- Header / Overview (no green bg, just the accent line) --}}
    <x-ts-card class="relative ring-brand overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>

        <div class="p-5 flex items-start gap-4">
            {{-- Logo / Initial --}}
            @if ($company->team_profile_photo_path)
                <img
                    src="{{ Storage::url($company->team_profile_photo_path) }}"
                    class="w-16 h-16 rounded-xl object-cover ring-1 ring-slate-200"
                    alt="{{ $company->name }}"
                >
            @else
                <div class="w-16 h-16 rounded-xl bg-slate-100 grid place-items-center text-xl font-semibold ring-1 ring-slate-200 text-slate-600">
                    {{ \Illuminate\Support\Str::of($company->name)->substr(0,1) }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-semibold truncate text-slate-900">{{ $company->name }}</h1>
                    @if($company->company_type)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-800 ring-1 ring-emerald-200">
                            {{ ucfirst($company->company_type) }}
                        </span>
                    @endif

                    {{-- Connection state --}}
                    @php $viewerId = auth()->user()?->currentTeam?->id; @endphp
                    @if($viewerId && $viewerId !== $company->id)
                        @if($this->isConnected)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-200">Connected</span>
                        @elseif($this->hasPending)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-200">Request pending</span>
                        @endif
                    @endif
                </div>

                {{-- Meta row --}}
                <div class="mt-1 text-xs md:text-sm text-slate-600 flex flex-wrap items-center gap-x-3 gap-y-1">
                    @if($websiteHost)
                        <a href="{{ $company->website }}" target="_blank" class="hover:underline">{{ $websiteHost }}</a>
                    @endif
                    @if($hq)
                        <span>HQ: {{ $hq }}</span>
                    @endif
                    @if($company->year_founded)
                        <span>Founded {{ $company->year_founded }}</span>
                    @endif
                    @if($company->headcount)
                        <span>{{ number_format($company->headcount) }} employees</span>
                    @endif
                </div>

                {{-- Specialties ribbon --}}
                @if ($company->specialties->count())
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @foreach ($company->specialties as $s)
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium
                                         ring-1 ring-emerald-200 bg-emerald-50 text-emerald-800">
                                <x-ts-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" />
                                {{ $s->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Summary --}}
                @if($company->summary)
                    <p class="mt-3 text-slate-700 leading-relaxed">{{ $company->summary }}</p>
                @endif
            </div>

            {{-- CTA --}}
            <div class="ms-auto shrink-0">
                @php $viewerTeam = auth()->user()?->currentTeam; @endphp
                @if ($viewerTeam && $company->id !== $viewerTeam->id)
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-2 text-sm font-semibold shadow-sm transition
                               {{ ($this->isConnected || $this->hasPending) ? 'opacity-70 cursor-not-allowed' : '' }}"
                        @disabled($this->isConnected || $this->hasPending)
                        wire:click="$set('showCompose', true)"
                    >
                        {{ $this->isConnected ? 'Connected' : ($this->hasPending ? 'Request Pending' : 'Request Intro') }}
                    </button>
                @else
                    <a href="{{ route('companies.profile.edit', $company) }}"
                       class="inline-flex items-center rounded-md bg-white text-slate-700 hover:bg-slate-50 px-3 py-2 text-sm font-semibold ring-1 ring-slate-200 transition">
                        Edit Profile
                    </a>
                @endif
            </div>
        </div>
    </x-ts-card>

    {{-- What they are looking for (Intent) --}}
    @if($intent)
        @php
            $p = $intent->payload ?? [];
            $countries  = config('countries', []);
            $countryMap = collect($countries)->mapWithKeys(fn($c) => [$c['value'] => $c['label']]);

            $territories = collect($p['territories'] ?? [])
                ->map(fn($code) => $countryMap[$code] ?? $code)
                ->values();

            $specialtyNames = \App\Models\Specialty::whereIn('id', $p['specialties'] ?? [])->pluck('name');

            $exclusivity = data_get($p, 'deal.exclusivity');
            $consignment = data_get($p, 'deal.consignment');
            $commission  = data_get($p, 'deal.commission_min');

            $urgency  = trim((string)($p['urgency'] ?? ''));
            $capacity = trim((string)($p['capacity_note'] ?? '')); // show full text (no cropping)
        @endphp

        <x-ts-card class="relative ring-brand overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
            <div class="p-5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-base md:text-lg font-semibold text-slate-900">What they are looking for</h2>
                    <span class="text-[11px] text-slate-500">Updated {{ optional($intent->updated_at)->diffForHumans() }}</span>
                </div>

                {{-- Urgency prominent (if set) --}}
                @if($urgency !== '')
                    <div class="mt-1 text-sm font-medium text-emerald-800">
                        {{ $urgency }}
                    </div>
                @endif

                {{-- Full capacity/notes text (no label, no crop) --}}
                @if($capacity !== '')
                    <div class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                        {{ $capacity }}
                    </div>
                @endif

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    {{-- Territories --}}
                    <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
                        <div class="text-xs font-medium text-slate-600 mb-1">Territories</div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($territories as $t)
                                <span class="inline-flex rounded-full bg-white px-2 py-0.5 text-xs ring-1 ring-slate-200">{{ $t }}</span>
                            @empty
                                <span class="text-xs text-slate-400">—</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Specialties --}}
                    <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
                        <div class="text-xs font-medium text-slate-600 mb-1">Specialties</div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($specialtyNames as $name)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs ring-1 ring-emerald-200 text-emerald-800">
                                    <x-ts-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" /> {{ $name }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">—</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Deal preferences --}}
                    <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
                        <div class="text-xs font-medium text-slate-600 mb-1">Deal preferences</div>
                        <ul class="text-xs text-slate-700 space-y-1.5">
                            <li>
                                <span class="font-medium">Exclusivity:</span>
                                {{ $exclusivity === true ? 'Yes' : ($exclusivity === false ? 'No' : 'No preference') }}
                            </li>
                            <li>
                                <span class="font-medium">Consignment:</span>
                                {{ $consignment === true ? 'Yes' : ($consignment === false ? 'No' : 'No preference') }}
                            </li>
                            <li>
                                <span class="font-medium">Min Commission:</span>
                                {{ $commission ? $commission.'%' : '—' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </x-ts-card>
    @endif

    {{-- Quick facts (includes Certifications now) --}}
    <x-ts-card class="relative ring-brand overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
        <x-slot name="header" class="font-semibold">Quick facts</x-slot>
        @php
            $certNames = $company->certifications->pluck('name');
        @endphp
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div>
                <dt class="text-slate-500">Website</dt>
                <dd class="text-slate-800">
                    @if($websiteHost)
                        <a class="hover:underline" href="{{ $company->website }}" target="_blank">{{ $websiteHost }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-slate-500">Headquarters</dt>
                <dd class="text-slate-800">{{ $hq ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Founded</dt>
                <dd class="text-slate-800">{{ $company->year_founded ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Employees</dt>
                <dd class="text-slate-800">{{ $company->headcount ? number_format($company->headcount) : '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-slate-500">Certifications</dt>
                <dd class="text-slate-800">
                    @if($certNames->isNotEmpty())
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @foreach($certNames as $n)
                                <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-xs ring-1 ring-slate-200">
                                    <x-ts-icon name="check-badge" class="w-4 h-4 text-emerald-600"/>
                                    {{ $n }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        —
                    @endif
                </dd>
            </div>
        </dl>
    </x-ts-card>

    {{-- Assets --}}
    @if($company->assets->count())
        <x-ts-card class="relative ring-brand overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
            <x-slot name="header" class="font-semibold">Assets</x-slot>
            <ul class="list-disc ps-6 space-y-1 text-slate-800">
                @foreach ($company->assets as $a)
                    <li>
                        <a href="{{ $a->url }}" target="_blank" class="hover:underline">
                            {{ $a->title ?? ucfirst($a->type) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-ts-card>
    @endif

    {{-- Compose Modal (entangled) --}}
    <div
        x-data="{ open: @entangle('showCompose').live }"
        x-cloak
        x-show="open"
        x-on:keydown.escape.window="open=false"
        class="relative z-50"
        role="dialog"
        aria-modal="true"
        aria-labelledby="compose-title"
    >
        <div class="fixed inset-0 bg-black/40" x-show="open" x-transition.opacity></div>
        <div class="fixed inset-0 flex items-start justify-center p-4 sm:p-8">
            <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl" x-show="open" x-transition x-on:click.outside="open=false">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h3 id="compose-title" class="text-base font-semibold">Request Intro</h3>
                    <button type="button" class="p-1 text-slate-400 hover:text-slate-600" x-on:click="open=false">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"/>
                        </svg>
                    </button>
                </div>
                <div class="px-4 py-4 space-y-3">
                    <div class="text-sm">
                        <div><span class="font-medium">To:</span> {{ $company->name }}</div>
                        <div><span class="font-medium">From:</span> {{ $this->viewerCompany?->name }}</div>
                    </div>

                    <x-ts-textarea
                        label="Message (optional)"
                        wire:model.defer="note"
                        placeholder="Tell them why you’d like to connect..."
                        rows="4" />

                    <p class="text-xs text-slate-500">
                        We’ll notify {{ $company->name }}. If they accept, you’ll both unlock contact details.
                    </p>
                </div>
                <div class="flex justify-end gap-2 border-t px-4 py-3">
                    <button class="btn-accent outline" x-on:click="open=false" type="button">Cancel</button>
                    <button class="btn-brand" wire:click="sendRequest" type="button">Send Request</button>
                </div>
            </div>
        </div>
    </div>
</div>
