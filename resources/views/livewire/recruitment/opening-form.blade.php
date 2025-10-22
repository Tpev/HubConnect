<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ $opening?->id ? 'Edit Opening' : 'New Opening' }}
            </h1>
            <p class="text-sm text-slate-500">
                Define the details of your opening. You can publish now or save as draft.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($opening?->id)
                <x-ts-button type="button" class="btn-brand" wire:click.prevent="save('stay')">Save</x-ts-button>
            @endif
            <x-ts-button type="button" class="btn-accent" wire:click.prevent="save('index')">Save & return</x-ts-button>
        </div>
    </div>

    {{-- ===== Basic Info ===== --}}
    <x-ts-card class="p-5 space-y-5 ring-brand">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-ts-input
                    label="Title"
                    placeholder="e.g., Territory Rep — Spine Ortho (Houston, TX)"
                    wire:model.defer="title" />
                @error('title') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Company Type"
                    wire:model.live="company_type"
                    :options="$companyTypeOptions"
                    select="label:label|value:value"
                    :clearable="false" />
                @error('company_type') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Status"
                    wire:model.live="status"
                    :options="$statusOptions"
                    select="label:label|value:value"
                    :clearable="false" />
                @error('status') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Specialties"
                    wire:model.defer="specialty_ids"
                    :options="$specialtyOptions"
                    :multiple="true"
                    :searchable="true"
                    select="label:label|value:value"
                    placeholder="Select specialties" />
                @error('specialty_ids') <x-ts-error :text="$message" /> @enderror
            </div>

            {{-- Location Omnibox --}}
            <div class="md:col-span-2">
                <livewire:geo.location-omnibox
                    wire:model="location_chips"
                    :value="$location_chips ?? []"
                    :biasCountryIso2="auth()->user()?->currentTeam?->hq_country ?? config('geo.bias_country')"
                />
                <p class="mt-1 text-xs text-slate-500">
                    Type any country, state/province, or city (e.g., <em>France</em>, <em>US-CA</em>, <em>Paris</em>). Select multiple if needed.
                </p>
                @error('location_chips') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-input
                    label="Compensation"
                    placeholder="e.g., Base $80k–$100k + 20% commission + equity"
                    wire:model.defer="compensation" />
                @error('compensation') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-input type="date" label="Visible until" wire:model.defer="visibility_until" />
                @error('visibility_until') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Comp structure"
                    wire:model.defer="comp_structure"
                    :options="$compStructureOptions"
                    select="label:label|value:value"
                    placeholder="Optional" />
                @error('comp_structure') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Opening type"
                    wire:model.defer="opening_type"
                    :options="$openingTypeOptions"
                    select="label:label|value:value"
                    placeholder="Optional" />
                @error('opening_type') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-2">
                <x-ts-textarea
                    label="Description"
                    wire:model.defer="description"
                    rows="8"
                    placeholder="Describe the role, responsibilities, requirements, territory coverage, OTE, benefits…" />
                @error('description') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>
    </x-ts-card>

    {{-- ===== Roleplay (placeholder) ===== --}}
    <x-ts-card class="p-0 ring-brand overflow-hidden">
        <div class="relative">
            <fieldset disabled aria-disabled="true" class="pointer-events-none select-none">
                <div class="p-5 space-y-5 filter blur-[1.5px] opacity-70">
                    <h2 class="text-lg font-semibold">Roleplay Evaluation</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-ts-select.styled
                                label="Policy"
                                wire:model.defer="roleplay_policy"
                                :options="$roleplayPolicyOptions"
                                select="label:label|value:value"
                                :clearable="false" />
                            @error('roleplay_policy') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div>
                            <x-ts-select.styled
                                label="Scenario Pack"
                                wire:model.defer="roleplay_scenario_pack_id"
                                :options="$scenarioPackOptions"
                                select="label:label|value:value"
                                placeholder="Select pack (optional)" />
                            @error('roleplay_scenario_pack_id') <x-ts-error :text="$message" /> @enderror
                        </div>

                        <div>
                            <x-ts-input
                                type="number" step="0.01"
                                label="Pass threshold"
                                placeholder="e.g., 75.00"
                                wire:model.defer="roleplay_pass_threshold" />
                            @error('roleplay_pass_threshold') <x-ts-error :text="$message" /> @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-ts-banner>
                            If policy is <strong>Required</strong>, candidates must complete a roleplay and achieve the pass threshold before advancing to interview.
                        </x-ts-banner>
                    </div>
                </div>
            </fieldset>

            <div class="absolute inset-0 z-10 flex items-center justify-center">
                <div class="backdrop-blur-md bg-white/70 dark:bg-slate-900/40 rounded-2xl px-6 py-8 text-center shadow-lg ring-1 ring-[var(--border)]">
                    <div class="mx-auto mb-3 inline-flex h-10 w-10 items-center justify-center rounded-full ring-1 ring-[var(--border)]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="text-lg font-semibold">Roleplay Evaluation</div>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                        This feature is <span class="font-medium">coming soon</span>.
                    </p>
                </div>
            </div>
        </div>
    </x-ts-card>

    {{-- ===== Deal-breaker criteria (table mode) ===== --}}
    <x-ts-card class="p-5 space-y-4 ring-brand">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Deal-breaker criteria</h2>
            <div class="w-64">
                <x-ts-select.styled
                    label="Screening mode"
                    wire:model.live="screening_policy"
                    :options="$screeningPolicyOptions"
                    select="label:label|value:value"
                    :clearable="false" />
                @error('screening_policy') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-600">
                    <tr class="border-b">
                        <th class="py-2 px-2 w-10">Ask</th>
                        <th class="py-2 px-2">Question</th>
                        <th class="py-2 px-2 w-48">Operator</th>
                        <th class="py-2 px-2 w-[28rem]">Value / Range</th>
                        <th class="py-2 px-2 w-40">Severity</th>
                        <th class="py-2 px-2">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($screeningFieldOptions as $opt)
                        @php
                            $field   = $opt['value'];
                            $label   = $opt['label'];
                            $type    = $opt['type'];
                            $i       = $ruleIndexByField[$field] ?? null; // index in screening_rules if enabled
                            $ops     = $this->operatorOptions($field);
                            $vOpts   = $this->valueOptions($field);
                            $isOn    = $i !== null;
                            $row     = $isOn ? ($screening_rules[$i] ?? []) : [];
                            $opValue = $isOn ? ($row['op'] ?? null) : null;
                            $isBetween = $isOn && ($opValue === 'between') && in_array($type, ['number','number_money'], true);
                        @endphp

                        <tr class="align-top">
                            {{-- Ask checkbox --}}
                            <td class="py-3 px-2">
                                <input
                                    type="checkbox"
                                    @checked($isOn)
                                    wire:change="toggleField('{{ $field }}', $event.target.checked)"
                                    class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand">
                            </td>

                            {{-- Question label --}}
                            <td class="py-3 px-2">
                                <div class="font-medium">{{ $label }}</div>
                                <div class="text-xs text-slate-500">{{ $field }}</div>
                            </td>

                            {{-- Operator --}}
                            <td class="py-3 px-2">
                                @if($isOn)
                                    <x-ts-select.styled
                                        wire:model="screening_rules.{{ $i }}.op"
                                        wire:change="onOperatorChange({{ $i }})"
                                        :options="$ops"
                                        select="label:label|value:value"
                                        placeholder="Op" />
                                    @error("screening_rules.$i.op") <x-ts-error :text="$message" /> @enderror
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Value / Range --}}
                            <td class="py-3 px-2">
                                @if(!$isOn)
                                    <span class="text-slate-400">—</span>
                                @else
                                    @if($isBetween)
                                        <div class="grid grid-cols-2 gap-2">
                                            <x-ts-input type="number" label="Min" wire:model.defer="screening_rules.{{ $i }}.min" />
                                            <x-ts-input type="number" label="Max" wire:model.defer="screening_rules.{{ $i }}.max" />
                                            @error("screening_rules.$i.min") <x-ts-error :text="$message" /> @enderror
                                            @error("screening_rules.$i.max") <x-ts-error :text="$message" /> @enderror
                                        </div>
                                    @elseif($type === 'date')
                                        <x-ts-input type="date" wire:model.defer="screening_rules.{{ $i }}.value" />
                                        @error("screening_rules.$i.value") <x-ts-error :text="$message" /> @enderror
                                    @elseif($type === 'boolean')
                                        <x-ts-select.styled
                                            :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                            select="label:label|value:value"
                                            wire:model="screening_rules.{{ $i }}.value"
                                            :clearable="false" />
                                        @error("screening_rules.$i.value") <x-ts-error :text="$message" /> @enderror
                                    @elseif(in_array($type, ['multiselect','multiselect_enum_ot','multiselect_enum_cs'], true))
                                        <x-ts-select.styled
                                            :multiple="true"
                                            :searchable="true"
                                            :options="$vOpts"
                                            select="label:label|value:value"
                                            wire:model.defer="screening_rules.{{ $i }}.value"
                                            placeholder="Choose one or more" />
                                        @error("screening_rules.$i.value") <x-ts-error :text="$message" /> @enderror
                                    @elseif($type === 'number' || $type === 'number_money')
                                        <x-ts-input type="number" wire:model.defer="screening_rules.{{ $i }}.value" />
                                        @error("screening_rules.$i.value") <x-ts-error :text="$message" /> @enderror
                                    @else
                                        <x-ts-input wire:model.defer="screening_rules.{{ $i }}.value" />
                                        @error("screening_rules.$i.value") <x-ts-error :text="$message" /> @enderror
                                    @endif
                                @endif
                            </td>

                            {{-- Severity --}}
                            <td class="py-3 px-2">
                                @if($isOn)
                                    <x-ts-select.styled
                                        :options="[['label'=>'Fail (deal-breaker)','value'=>'fail'],['label'=>'Flag (soft)','value'=>'flag']]"
                                        select="label:label|value:value"
                                        wire:model.defer="screening_rules.{{ $i }}.severity"
                                        :clearable="false" />
                                    @error("screening_rules.$i.severity") <x-ts-error :text="$message" /> @enderror
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Note --}}
                            <td class="py-3 px-2">
                                @if($isOn)
                                    <x-ts-input placeholder="Internal note…" wire:model.defer="screening_rules.{{ $i }}.message" />
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <x-ts-button type="button" class="btn-accent outline" wire:click="$refresh">
                Refresh table
            </x-ts-button>
            <x-ts-button type="button" class="btn-brand" wire:click.prevent="save('stay')">Save</x-ts-button>
            <x-ts-button type="button" class="btn-accent" wire:click.prevent="save('index')">Save & return</x-ts-button>
        </div>
    </x-ts-card>

    <div class="flex items-center justify-end gap-2">
        <x-ts-button type="button" class="btn-brand" wire:click.prevent="save('stay')">Save</x-ts-button>
        <x-ts-button type="button" class="btn-accent" wire:click.prevent="save('index')">Save & return</x-ts-button>
    </div>
</div>
