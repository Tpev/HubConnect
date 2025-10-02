{{-- Company Show --}}
<div class="max-w-5xl mx-auto space-y-6" wire:init>
    {{-- Skeleton while loading --}}
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

    {{-- Header / Overview --}}
    <x-ts-card class="ring-brand">
        <div class="flex items-start gap-4">
            @if ($company->team_profile_photo_path)
                <img src="{{ Storage::url($company->team_profile_photo_path) }}"
                     class="w-16 h-16 rounded-xl object-cover"
                     alt="{{ $company->name }}">
            @else
                <div class="w-16 h-16 rounded-xl bg-slate-200 grid place-items-center text-xl font-semibold">
                    {{ \Illuminate\Support\Str::of($company->name)->substr(0,1) }}
                </div>
            @endif

            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-semibold truncate">{{ $company->name }}</h1>
                    @if($company->company_type)
                        <span class="badge-brand">{{ ucfirst($company->company_type) }}</span>
                    @endif

                    {{-- Status chips --}}
                    @php
                        $viewerId = auth()->user()?->currentTeam?->id;
                    @endphp
                    @if($viewerId && $viewerId !== $company->id)
                        @if($this->isConnected)
                            <span class="chip-brand">Connected</span>
                        @elseif($this->hasPending)
                            <span class="chip-accent">Request pending</span>
                        @endif
                    @endif
                </div>

                <div class="text-sm text-slate-600">
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="hover:underline">
                            {{ parse_url($company->website, PHP_URL_HOST) ?: $company->website }}
                        </a> •
                    @endif
                    @if($company->hq_country) HQ: {{ $company->hq_country }} @endif
                    @if($company->year_founded) • Founded {{ $company->year_founded }} @endif
                    @if($company->headcount) • {{ $company->headcount }} employees @endif
                </div>

                @if($company->summary)
                    <p class="mt-3 text-slate-700">{{ $company->summary }}</p>
                @endif
            </div>

            <div class="ms-auto">
                @php $viewerTeam = auth()->user()?->currentTeam; @endphp
                @if ($viewerTeam && $company->id !== $viewerTeam->id)
                    <button
                        class="btn-brand {{ ($this->isConnected || $this->hasPending) ? 'outline cursor-not-allowed opacity-60' : '' }}"
                        @disabled($this->isConnected || $this->hasPending)
                        wire:click="$set('showCompose', true)"
                        type="button"
                    >
                        {{ $this->isConnected ? 'Connected' : ($this->hasPending ? 'Request Pending' : 'Request Intro') }}
                    </button>
                @else
                    <a href="{{ route('companies.profile.edit', $company) }}" class="btn-accent outline">Edit Profile</a>
                @endif
            </div>
        </div>
    </x-ts-card>

    {{-- Intent --}}
    @if($intent)
        @php $p = $intent->payload; @endphp
        <x-ts-card class="ring-brand">
            <x-slot name="header" class="font-semibold text-lg">🟢 Currently Looking For</x-slot>
            <div class="space-y-2">
                <div><span class="font-medium">Territories:</span> {{ collect($p['territories'] ?? [])->join(', ') ?: '—' }}</div>
                <div><span class="font-medium">Specialties:</span>
                    {{ \App\Models\Specialty::whereIn('id', $p['specialties'] ?? [])->pluck('name')->join(', ') ?: '—' }}
                </div>
                <div>
                    <span class="font-medium">Deal:</span>
                    Exclusivity: {{ data_get($p,'deal.exclusivity')===true ? 'Yes' : (data_get($p,'deal.exclusivity')===false ? 'No' : 'No pref') }},
                    Consignment: {{ data_get($p,'deal.consignment')===true ? 'Yes' : (data_get($p,'deal.consignment')===false ? 'No' : 'No pref') }},
                    Min Comm: {{ data_get($p,'deal.commission_min') ? data_get($p,'deal.commission_min').'%' : '—' }}
                </div>
                <div class="text-xs text-slate-500">Updated {{ optional($intent->updated_at)->diffForHumans() }}</div>
            </div>
        </x-ts-card>
    @endif

    {{-- Specialties & Certifications --}}
    <div class="grid gap-4 sm:grid-cols-2">
        <x-ts-card class="ring-brand">
            <x-slot name="header" class="font-semibold">Specialties</x-slot>
            <div class="flex flex-wrap gap-2">
                @forelse ($company->specialties as $s)
                    <span class="badge-brand">{{ $s->name }}</span>
                @empty
                    <x-ts-error title="No specialties provided." />
                @endforelse
            </div>
        </x-ts-card>

        <x-ts-card class="ring-brand">
            <x-slot name="header" class="font-semibold">Certifications</x-slot>
            <div class="flex flex-wrap gap-2">
                @forelse ($company->certifications as $c)
                    <span class="badge-accent">{{ $c->name }}</span>
                @empty
                    <x-ts-error title="No certifications listed." />
                @endforelse
            </div>
        </x-ts-card>
    </div>

    {{-- Contacts --}}
    <x-ts-card class="ring-brand">
        <x-slot name="header" class="font-semibold">Contacts</x-slot>
        @if ($this->canSeeContacts)
            <div class="space-y-3">
                @forelse ($company->contacts as $contact)
                    <div>
                        <div class="font-medium">
                            {{ $contact->name }}
                            @if($contact->title)
                                <span class="text-xs text-slate-500">{{ $contact->title }}</span>
                            @endif
                        </div>
                        @if($contact->email)<div class="text-sm">{{ $contact->email }}</div>@endif
                        @if($contact->phone)<div class="text-sm">{{ $contact->phone }}</div>@endif
                    </div>
                @empty
                    <x-ts-error title="No contacts added yet." />
                @endforelse
            </div>
        @else
            <x-ts-error title="Contacts are private." description="Request an intro to unlock contact details." />
        @endif
    </x-ts-card>

    {{-- Assets --}}
    @if($company->assets->count())
        <x-ts-card class="ring-brand">
            <x-slot name="header" class="font-semibold">Assets</x-slot>
            <ul class="list-disc ps-6 space-y-1">
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
            <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl dark:bg-slate-800"
                 x-show="open" x-transition x-on:click.outside="open=false">
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
