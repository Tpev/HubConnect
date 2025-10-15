<div
    x-data="{
        toastOpen:false,
        toastMsg:'',
        progress:0,
        showProgress:false
    }"
    x-on:toast.window="toastMsg = $event.detail.message; toastOpen = true; setTimeout(()=>toastOpen=false, 2200)"
    x-on:livewire-upload-start="showProgress=true; progress=0"
    x-on:livewire-upload-progress="progress=$event.detail.progress"
    x-on:livewire-upload-finish="setTimeout(()=>{ showProgress=false; progress=0 }, 400)"
    class="max-w-4xl mx-auto space-y-10"
>
    @php $team = auth()->user()?->currentTeam; @endphp
    @if($team && $team->kyc_status !== 'approved')
        <div class="p-4 rounded-lg bg-amber-50 text-amber-800">
            <div class="font-semibold">Verification</div>
            <div class="text-sm">
                We verify every new company to keep the network trusted. Most reviews complete within
                <strong>one business day</strong>. Save your basics below to submit for review.
                @if($team->kyc_status === 'rejected' && $team->kyc_notes)
                    <div class="mt-2 text-rose-700">
                        <span class="font-semibold">Review notes: </span>{{ $team->kyc_notes }}
                    </div>
                @endif
            </div>
            <div class="mt-2 text-xs text-amber-700">
                Status: <span class="px-2 py-0.5 rounded-full bg-amber-100">{{ $team->kycStatusLabel() }}</span>
                @if($team->kyc_status === 'pending_review' && $team->kyc_submitted_at)
                    <span class="ml-2">• Submitted {{ $team->kyc_submitted_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    @endif

    {{-- BASICS --}}
    <x-ts-card>
        <x-slot name="header" class="flex items-center justify-between">
            <div class="font-semibold text-lg">Company Basics</div>
            @if (session('saved'))
                <span class="text-emerald-600 text-sm">{{ session('saved') }}</span>
            @endif
        </x-slot>

        <form wire:submit.prevent="saveBasic" class="grid gap-5 sm:grid-cols-2">
            <x-ts-input label="Company Name" wire:model.defer="name" required />

            <x-ts-input label="Website" wire:model.defer="website" placeholder="https://example.com" />

            <x-ts-select.styled
                label="Company Type"
                wire:model="company_type"
                :options="[
                    ['label' => 'Manufacturer', 'value' => 'manufacturer'],
                    ['label' => 'Distributor',  'value' => 'distributor'],
                    ['label' => 'Both',         'value' => 'both'],
                ]"
                placeholder="Select type"
                required
            />

            @php
                $countries = collect(config('countries', []));
                if ($countries->isEmpty()) {
                    $countries = collect([
                        ['label' => 'United States', 'value' => 'US'],
                        ['label' => 'United Kingdom','value' => 'GB'],
                        ['label' => 'France',        'value' => 'FR'],
                        ['label' => 'Germany',       'value' => 'DE'],
                    ]);
                }
            @endphp
            <x-ts-select.styled
                label="HQ Country"
                wire:model="hq_country"
                :options="$countries->map(fn($c) => ['label' => $c['label'], 'value' => $c['value']])->values()->all()"
                searchable
                placeholder="Select country"
            />

            <x-ts-input
                label="Year Founded"
                type="number"
                wire:model.defer="year_founded"
                min="1800"
                max="{{ now()->year }}"
            />

            <x-ts-input
                label="Headcount"
                type="number"
                wire:model.defer="headcount"
                min="1"
            />

            <x-ts-select.styled
                label="Stage"
                wire:model="stage"
                :options="[
                    ['label'=>'Startup','value'=>'startup'],
                    ['label'=>'Growth','value'=>'growth'],
                    ['label'=>'Established','value'=>'established'],
                    ['label'=>'Global','value'=>'global'],
                ]"
                placeholder="Select stage"
            />

            <div class="sm:col-span-2">
                <x-ts-textarea
                    label="Summary"
                    wire:model.defer="summary"
                    rows="5"
                    placeholder="Short company overview..."
                />
            </div>

            {{-- Logo uploader --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <div class="flex items-center gap-4">
                    {{-- Current logo --}}
                    @php $current = auth()->user()?->currentTeam?->team_profile_photo_path; @endphp
                    <div class="h-16 w-16 rounded-lg ring-1 ring-slate-200 overflow-hidden bg-slate-100">
                        @if ($logo)
                            {{-- Preview new upload --}}
                            <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 object-cover" alt="Preview">
                        @elseif (!empty($current))
                            <img src="{{ Storage::disk('public')->url($current) }}" class="h-16 w-16 object-cover" alt="Logo">
                        @else
                            <div class="h-full w-full grid place-items-center text-xs text-slate-500">No logo</div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <input type="file" wire:model="logo" accept="image/*" class="block w-full text-sm" />
                        @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                        {{-- Progress bar --}}
                        <div x-show="showProgress" class="mt-2 w-full h-1.5 rounded bg-slate-100 overflow-hidden">
                            <div class="h-1.5 bg-emerald-600 transition-all" :style="`width:${progress}%;`"></div>
                        </div>

                        <p class="mt-1 text-xs text-slate-500">JPG/PNG/WebP up to 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="sm:col-span-2">
                <x-ts-button type="submit" class="btn-accent" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Basics</span>
                    <span wire:loading>Saving…</span>
                </x-ts-button>
            </div>
        </form>
    </x-ts-card>

    {{-- SPECIALTIES --}}
    <x-ts-card>
        <x-slot name="header" class="font-semibold text-lg">Specialties</x-slot>

        <div class="space-y-4">
            <x-ts-select.styled
                label="Select Specialties"
                wire:model="selectedSpecialties"
                :options="$allSpecialties->map(fn($s)=>['label'=>$s->name,'value'=>$s->id])->values()->all()"
                multiple
                searchable
                placeholder="Choose specialties"
            />
            <x-ts-button wire:click="saveSpecialties" class="btn-accent" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Specialties</span>
                <span wire:loading>Saving…</span>
            </x-ts-button>
        </div>
    </x-ts-card>

    {{-- CERTIFICATIONS --}}
    <x-ts-card>
        <x-slot name="header" class="font-semibold text-lg">Certifications</x-slot>

        <div class="space-y-4">
            <x-ts-select.styled
                label="Select Certifications"
                wire:model="selectedCerts"
                :options="$allCerts->map(fn($c)=>['label'=>$c->name,'value'=>$c->id])->values()->all()"
                multiple
                searchable
                placeholder="Choose certifications"
            />
            <x-ts-button wire:click="saveCertifications" class="btn-accent" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Certifications</span>
                <span wire:loading>Saving…</span>
            </x-ts-button>
        </div>
    </x-ts-card>

    {{-- Tiny toast --}}
    <div
        x-cloak
        x-show="toastOpen"
        x-transition
        class="fixed top-4 right-4 z-50 rounded-lg bg-emerald-600 text-white px-3.5 py-2 text-sm shadow-lg"
        role="status"
        aria-live="polite"
    >
        <span x-text="toastMsg"></span>
    </div>
</div>
