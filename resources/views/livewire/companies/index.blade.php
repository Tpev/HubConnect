<div class="max-w-6xl mx-auto space-y-6">
    {{-- Search --}}
    <x-ts-card class="ring-brand">
        <x-slot name="header" class="font-semibold text-lg">Search Companies</x-slot>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <x-ts-input label="Keyword" wire:model.debounce.400ms="q" placeholder="Name, summary, website" class="lg:col-span-2" />

            <x-ts-select.styled
                label="Role"
                wire:model="role"
                :options="[
                    ['label'=>'Any','value'=>null],
                    ['label'=>'Manufacturer','value'=>'manufacturer'],
                    ['label'=>'Distributor','value'=>'distributor'],
                    ['label'=>'Both','value'=>'both'],
                ]"
                placeholder="Any"
            />

            {{-- Countries come from config only (no symfony/intl) --}}
            <x-ts-select.styled
                label="Territory"
                wire:model="territory"
                :options="$countries"
                searchable
                placeholder="Any"
            />

            <x-ts-select.styled label="Specialty" wire:model="specialty"
                :options="$allSpecialties->map(fn($s)=>['label'=>$s->name,'value'=>$s->id])->toArray()"
                searchable placeholder="Any" />

            <x-ts-select.styled label="Certification" wire:model="cert"
                :options="$allCerts->map(fn($c)=>['label'=>$c->name,'value'=>$c->id])->toArray()"
                searchable placeholder="Any" />

            <x-ts-select.styled label="Sort" wire:model="sort"
                :options="[
                    ['label'=>'Recently updated','value'=>'recent'],
                    ['label'=>'Name (A–Z)','value'=>'name'],
                ]" />
        </div>
    </x-ts-card>

    {{-- Skeleton on filter change --}}
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
            // Build a code => label quick map from config for showing intent territories
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
            @endphp

            <x-ts-card class="ring-brand">
                <div class="flex items-start gap-3">
                    @if ($company->team_profile_photo_path)
                        <img src="{{ Storage::url($company->team_profile_photo_path) }}" class="w-12 h-12 rounded-lg object-cover" alt="{{ $company->name }}">
                    @else
                        <div class="w-12 h-12 rounded-lg bg-slate-200 grid place-items-center font-semibold">
                            {{ \Illuminate\Support\Str::of($company->name)->substr(0,1) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <a href="{{ route('companies.show', $company) }}" class="font-semibold hover:underline">
                            {{ $company->name }}
                        </a>
                        <div class="text-xs text-slate-500">
                            {{ ucfirst($company->company_type ?? '—') }}
                            @if($company->website)
                                • <a href="{{ $company->website }}" target="_blank" class="hover:underline">
                                    {{ parse_url($company->website, PHP_URL_HOST) }}
                                  </a>
                            @endif
                        </div>

                        @if($intent)
                            @php $p = $intent->payload; @endphp
                            <div class="mt-2 text-sm text-slate-700">
                                <span class="font-medium">Seeking:</span>
                                @if(!empty($p['specialties']))
                                    {{ \App\Models\Specialty::whereIn('id',$p['specialties'])->pluck('name')->join(', ') }}
                                @endif
                                @if(!empty($p['territories']))
                                    @if(!empty($p['specialties'])) • @endif
                                    in
                                    {{ collect($p['territories'])->map(fn($code) => $countryMap[$code] ?? $code)->join(', ') }}
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Status chip (top-right) --}}
                    @if($viewerId && $viewerId !== $company->id)
                        @if($connected)
                            <span class="chip-brand ms-auto">Connected</span>
                        @elseif($hasPending)
                            <span class="chip-accent ms-auto">Pending</span>
                        @endif
                    @endif
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($company->specialties->take(3) as $s)
                            <span class="badge-brand">{{ $s->name }}</span>
                        @endforeach
                        @if ($company->specialties->count() > 3)
                            <span class="badge-brand">+{{ $company->specialties->count() - 3 }}</span>
                        @endif
                    </div>

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
            </x-ts-card>
        @empty
            <x-ts-card class="ring-brand">
                <x-ts-error title="No companies found." description="Try changing or clearing some filters." />
            </x-ts-card>
        @endforelse
    </div>

    <div>
        {{ $companies->links() }}
    </div>
</div>
