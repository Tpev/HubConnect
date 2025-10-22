{{-- resources/views/livewire/recruitment/application-start.blade.php --}}

@php
    $states = $territoryOptions ?? [];
@endphp

<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="space-y-1">
        <a href="{{ route('openings.show', $opening->slug) }}" class="text-sm text-[var(--brand-700)] hover:underline">← Back to opening</a>
        <h1 class="text-2xl font-semibold text-slate-900">Apply to {{ $opening->title }}</h1>
        <p class="text-slate-600">Only fields requested by the employer are shown.</p>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-lg bg-rose-50 text-rose-800 ring-1 ring-rose-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    <form wire:submit.prevent="submit" class="bg-white rounded-2xl shadow p-6 space-y-8">
        {{-- Basic contact (always) --}}
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Contact</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Full name <span class="text-rose-600">*</span></label>
                    <input type="text" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="candidate_name">
                    @error('candidate_name') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Email <span class="text-rose-600">*</span></label>
                    <input type="email" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="email">
                    @error('email') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Phone</label>
                    <input type="text" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="phone" placeholder="(555) 555-0123">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">City</label>
                        <input type="text" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="location" placeholder="Austin">
                    </div>
                    @if($this->ask('state'))
                        <div>
                            <label class="text-sm font-medium">State</label>
                            <select class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="state">
                                <option value="">—</option>
                                @foreach($states as $opt)
                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Experience / availability --}}
        @if($this->asksAny(['years_total','years_med_device','travel_percent_max','overnight_ok','driver_license']))
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Experience & Availability</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($this->ask('years_total'))
                    <div>
                        <label class="text-sm font-medium">Total experience (years)</label>
                        <input type="number" min="0" step="1" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="years_total">
                    </div>
                @endif
                @if($this->ask('years_med_device'))
                    <div>
                        <label class="text-sm font-medium">Med device (years)</label>
                        <input type="number" min="0" step="1" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="years_med_device">
                    </div>
                @endif
                @if($this->ask('travel_percent_max'))
                    <div>
                        <label class="text-sm font-medium">Travel max (%)</label>
                        <input type="number" min="0" max="100" step="1" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="travel_percent_max">
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($this->ask('overnight_ok'))
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" class="rounded border-slate-300" wire:model.defer="overnight_ok">
                    <span class="text-sm">Overnight travel OK</span>
                </label>
                @endif

                @if($this->ask('driver_license'))
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" class="rounded border-slate-300" wire:model.defer="driver_license">
                    <span class="text-sm">Driver license</span>
                </label>
                @endif
            </div>
        </section>
        @endif

        {{-- Specialties --}}
        @if($this->ask('specialties'))
        <section class="space-y-3">
            <h2 class="text-lg font-semibold">Specialties</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($specialtyOptions as $opt)
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-slate-300"
                               value="{{ $opt['value'] }}"
                               wire:model.defer="specialties">
                        <span class="text-sm">{{ $opt['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Employment preferences --}}
        @if($this->asksAny(['opening_type_accepts','comp_structure_accepts','expected_base','expected_ote','work_auth','start_date','cold_outreach_ok','has_noncompete_conflict','background_check_ok']))
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Employment Preferences</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($this->ask('opening_type_accepts'))
                    <div>
                        <label class="text-sm font-medium">Accepted employment types</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach($openingTypeOptions as $opt)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" value="{{ $opt['value'] }}" class="rounded border-slate-300"
                                           wire:model.defer="opening_type_accepts">
                                    <span class="text-sm">{{ $opt['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($this->ask('comp_structure_accepts'))
                    <div>
                        <label class="text-sm font-medium">Accepted compensation structures</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach($compStructureOptions as $opt)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" value="{{ $opt['value'] }}" class="rounded border-slate-300"
                                           wire:model.defer="comp_structure_accepts">
                                    <span class="text-sm">{{ $opt['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($this->ask('expected_base'))
                    <div>
                        <label class="text-sm font-medium">Expected base ($)</label>
                        <input type="number" min="0" step="1000" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="expected_base">
                    </div>
                @endif
                @if($this->ask('expected_ote'))
                    <div>
                        <label class="text-sm font-medium">Expected OTE ($)</label>
                        <input type="number" min="0" step="1000" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="expected_ote">
                    </div>
                @endif
                @if($this->ask('work_auth'))
                    <div>
                        <label class="text-sm font-medium">Work authorization</label>
                        <select class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="work_auth">
                            <option value="">—</option>
                            @foreach($workAuthOptions as $opt)
                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($this->ask('start_date'))
                    <div>
                        <label class="text-sm font-medium">Earliest start date</label>
                        <input type="date" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="start_date">
                    </div>
                @endif

                @if($this->ask('cold_outreach_ok'))
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-slate-300" wire:model.defer="cold_outreach_ok">
                        <span class="text-sm">Open to cold outreach</span>
                    </label>
                @endif

                @if($this->ask('has_noncompete_conflict'))
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-slate-300" wire:model.defer="has_noncompete_conflict">
                        <span class="text-sm">Non-compete conflict</span>
                    </label>
                @endif

                @if($this->ask('background_check_ok'))
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="rounded border-slate-300" wire:model.defer="background_check_ok">
                        <span class="text-sm">Background check OK</span>
                    </label>
                @endif
            </div>
        </section>
        @endif

        {{-- Cover letter & CV (always visible; neither required) --}}
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Cover Letter & Resume</h2>
            <div>
                <label class="text-sm font-medium">Cover letter</label>
                <textarea rows="5" class="mt-1 w-full rounded-md border-slate-300" wire:model.defer="cover_letter" placeholder="Optional."></textarea>
            </div>
            <div>
                <label class="text-sm font-medium">Resume (PDF/DOC/DOCX, max 10MB)</label>
                <input type="file" class="mt-1 block w-full text-sm" wire:model="cv" accept=".pdf,.doc,.docx">
                @error('cv') <p class="text-sm text-rose-700 mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="cv" class="text-xs text-slate-500 mt-1">Uploading…</div>
            </div>
        </section>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('openings.show', $opening->slug) }}" class="px-4 py-2 rounded-lg ring-1 ring-slate-200 text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                Submit application
            </button>
        </div>
    </form>
</div>
