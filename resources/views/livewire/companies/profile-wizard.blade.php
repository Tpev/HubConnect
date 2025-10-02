{{-- resources/views/livewire/companies/profile-basics.blade.php --}}

<div class="max-w-4xl mx-auto space-y-10">
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

            {{-- Company Type --}}
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

            {{-- HQ Country (uses config/countries.php; NO Symfony Intl) --}}
            @php
                // Expecting config/countries.php to return: [['label'=>'France','value'=>'FR'], ...]
                $countries = collect(config('countries', []));
                // If the config were missing for any reason, fall back to a tiny safe list:
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

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <input type="file" wire:model="logo" accept="image/*" class="block w-full" />
                @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <x-ts-button type="submit" class="btn-accent">Save Basics</x-ts-button>
            </div>
        </form>
    </x-ts-card>

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
            <x-ts-button wire:click="saveSpecialties" class="btn-accent">Save Specialties</x-ts-button>
        </div>
    </x-ts-card>

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
            <x-ts-button wire:click="saveCertifications" class="btn-accent">Save Certifications</x-ts-button>
        </div>
    </x-ts-card>
</div>
