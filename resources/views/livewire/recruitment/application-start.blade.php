<div> {{-- SINGLE ROOT WRAPPER --}}

    {{-- Header --}}
    <section class="grad-hero border-b border-[var(--border)]/80">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="space-y-1">
                <a href="{{ route('openings.show', $opening->slug) }}"
                   class="text-xs text-[var(--brand-700)] hover:underline">← Back to opening</a>
                <div class="text-slate-500 text-sm">{{ ucfirst($opening->company_type) }}</div>
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">Apply — {{ $opening->title }}</h1>
            </div>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

        @if($submitted)
            <x-ts-card class="p-6 ring-brand">
                <div class="text-lg font-semibold text-[var(--brand-700)]">Thanks! Your application has been submitted.</div>
                <div class="text-slate-600 text-sm mt-1">
                    We’ll review your profile and be in touch.
                </div>
            </x-ts-card>
        @else
            {{-- Contact (always) --}}
            <x-ts-card class="p-5 ring-brand space-y-4">
                <h2 class="text-lg font-semibold">Contact</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-ts-input label="Full name" wire:model.live="candidate_name" placeholder="Jane Doe" />
                        @error('candidate_name') <x-ts-error :text="$message" /> @enderror
                    </div>

                    <div>
                        <x-ts-input type="email" label="Email" wire:model.live="email" placeholder="jane.doe@example.com" />
                        @error('email') <x-ts-error :text="$message" /> @enderror
                    </div>

                    <div>
                        <x-ts-input label="Phone" wire:model.live="phone" placeholder="(555) 555-0123" />
                        @error('phone') <x-ts-error :text="$message" /> @enderror
                    </div>

                    <div>
                        <x-ts-input label="City" wire:model.live="location" placeholder="Austin" />
                        @error('location') <x-ts-error :text="$message" /> @enderror
                    </div>

                    @if($this->ask('state'))
                        <div>
                            <x-ts-select.styled
                                label="State / Territory"
                                wire:model.live="state"
                                :options="$territoryOptions"
                                select="label:label|value:value"
                                placeholder="Choose state" />
                            @error('state') <x-ts-error :text="$message" /> @enderror
                        </div>
                    @endif
                </div>
            </x-ts-card>

            {{-- Experience (only if any of these fields are asked) --}}
            @if($this->asksAny(['years_total','years_med_device','travel_percent_max','specialties','overnight_ok','driver_license']))
                <x-ts-card class="p-5 ring-brand space-y-4">
                    <h2 class="text-lg font-semibold">Experience</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($this->ask('years_total'))
                            <div>
                                <x-ts-input type="number" min="0" step="0.1" label="Total B2B sales (years)" wire:model.live="years_total" placeholder="e.g., 6" />
                                @error('years_total') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('years_med_device'))
                            <div>
                                <x-ts-input type="number" min="0" step="0.1" label="Medical device (years)" wire:model.live="years_med_device" placeholder="e.g., 3" />
                                @error('years_med_device') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('travel_percent_max'))
                            <div>
                                <x-ts-input type="number" min="0" max="100" step="1" label="Max travel (%)" wire:model.live="travel_percent_max" placeholder="e.g., 30" />
                                @error('travel_percent_max') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('specialties'))
                            <div class="md:col-span-3">
                                <x-ts-select.styled
                                    label="Specialties (experience)"
                                    wire:model.live="specialties"
                                    :options="$specialtyOptions"
                                    multiple searchable
                                    select="label:label|value:value"
                                    placeholder="Select specialties" />
                            </div>
                        @endif

                        @if($this->ask('overnight_ok'))
                            <div>
                                <x-ts-select.styled
                                    label="Overnights OK"
                                    wire:model.live="overnight_ok"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('overnight_ok') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('driver_license'))
                            <div>
                                <x-ts-select.styled
                                    label="Driver license & car"
                                    wire:model.live="driver_license"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('driver_license') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif
                    </div>
                </x-ts-card>
            @endif

            {{-- Preferences (conditional) --}}
            @if($this->asksAny(['opening_type_accepts','comp_structure_accepts','expected_base','expected_ote','cold_outreach_ok']))
                <x-ts-card class="p-5 ring-brand space-y-4">
                    <h2 class="text-lg font-semibold">Employment & compensation preferences</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->ask('opening_type_accepts'))
                            <div>
                                <x-ts-select.styled
                                    label="Open employment types"
                                    wire:model.live="opening_type_accepts"
                                    :options="$openingTypeOptions"
                                    multiple searchable
                                    select="label:label|value:value"
                                    placeholder="Select one or more" />
                            </div>
                        @endif

                        @if($this->ask('comp_structure_accepts'))
                            <div>
                                <x-ts-select.styled
                                    label="Open comp structures"
                                    wire:model.live="comp_structure_accepts"
                                    :options="$compStructureOptions"
                                    multiple searchable
                                    select="label:label|value:value"
                                    placeholder="Select one or more" />
                            </div>
                        @endif

                        @if($this->ask('expected_base'))
                            <div>
                                <x-ts-input type="number" step="1000" label="Expected base (USD)" wire:model.live="expected_base" placeholder="e.g., 90000" />
                                @error('expected_base') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('expected_ote'))
                            <div>
                                <x-ts-input type="number" step="1000" label="Expected OTE (USD)" wire:model.live="expected_ote" placeholder="e.g., 180000" />
                                @error('expected_ote') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('cold_outreach_ok'))
                            <div>
                                <x-ts-select.styled
                                    label="Comfortable with cold outreach"
                                    wire:model.live="cold_outreach_ok"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('cold_outreach_ok') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif
                    </div>
                </x-ts-card>
            @endif

            {{-- Compliance (conditional) --}}
            @if($this->asksAny(['work_auth','start_date','has_noncompete_conflict','background_check_ok']))
                <x-ts-card class="p-5 ring-brand space-y-4">
                    <h2 class="text-lg font-semibold">Compliance & logistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($this->ask('work_auth'))
                            <div>
                                <x-ts-select.styled
                                    label="U.S. work authorization"
                                    wire:model.live="work_auth"
                                    :options="$workAuthOptions"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('work_auth') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('start_date'))
                            <div>
                                <x-ts-input type="date" label="Earliest start date" wire:model.live="start_date" />
                                @error('start_date') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('has_noncompete_conflict'))
                            <div>
                                <x-ts-select.styled
                                    label="Active non-compete conflict"
                                    wire:model.live="has_noncompete_conflict"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('has_noncompete_conflict') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif

                        @if($this->ask('background_check_ok'))
                            <div>
                                <x-ts-select.styled
                                    label="Background check OK"
                                    wire:model.live="background_check_ok"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    placeholder="Select" />
                                @error('background_check_ok') <x-ts-error :text="$message" /> @enderror
                            </div>
                        @endif
                    </div>
                </x-ts-card>
            @endif

            {{-- Cover letter & CV (always) --}}
            <x-ts-card class="p-5 ring-brand space-y-4">
                <h2 class="text-lg font-semibold">Cover letter & CV</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-ts-textarea
                            label="Cover letter (optional)"
                            rows="6"
                            wire:model.live="cover_letter"
                            placeholder="Tell us briefly why you're a great fit…" />
                        @error('cover_letter') <x-ts-error :text="$message" /> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CV (PDF/DOC, up to 10MB)</label>
                        <input type="file" wire:model="cv" accept=".pdf,.doc,.docx" class="block w-full text-sm">
                        @error('cv') <x-ts-error :text="$message" /> @enderror
                        <div wire:loading wire:target="cv" class="text-xs text-slate-500 mt-1">Uploading…</div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <x-ts-button class="btn-accent" wire:click="submit">
                        Submit application
                    </x-ts-button>
                </div>
            </x-ts-card>
        @endif

    </div>
</div>
