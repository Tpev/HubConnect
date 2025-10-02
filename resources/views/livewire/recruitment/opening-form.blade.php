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
                <x-ts-button class="btn-brand" wire:click="save('stay')">Save</x-ts-button>
            @endif
            <x-ts-button class="btn-accent" wire:click="save('index')">Save & return</x-ts-button>
        </div>
    </div>

    {{-- ===== Basic Info ===== --}}
    <x-ts-card class="p-5 space-y-5 ring-brand">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-ts-input
                    label="Title"
                    placeholder="e.g., Territory Rep — Spine Ortho (Houston, TX)"
                    wire:model.live="title" />
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
                    wire:model.live="specialty_ids"
                    :options="$specialtyOptions"
                    :multiple="true" searchable
                    select="label:label|value:value"
                    placeholder="Select specialties" />
                @error('specialty_ids') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Territories"
                    wire:model.live="territory_ids"
                    :options="$territoryOptions"
                    :multiple="true" searchable
                    select="label:label|value:value"
                    placeholder="Select territories" />
                @error('territory_ids') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-input
                    label="Compensation"
                    placeholder="e.g., Base $80k–$100k + 20% commission + equity"
                    wire:model.live="compensation" />
                @error('compensation') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-input type="date" label="Visible until" wire:model.live="visibility_until" />
                @error('visibility_until') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Comp structure"
                    wire:model.live="comp_structure"
                    :options="$compStructureOptions"
                    select="label:label|value:value"
                    placeholder="Optional" />
                @error('comp_structure') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Opening type"
                    wire:model.live="opening_type"
                    :options="$openingTypeOptions"
                    select="label:label|value:value"
                    placeholder="Optional" />
                @error('opening_type') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-2">
                <x-ts-textarea
                    label="Description"
                    wire:model.live="description"
                    rows="8"
                    placeholder="Describe the role, responsibilities, requirements, territory coverage, OTE, benefits…" />
                @error('description') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>
    </x-ts-card>

    {{-- ===== Roleplay ===== --}}
    <x-ts-card class="p-5 space-y-5 ring-brand">
        <h2 class="text-lg font-semibold">Roleplay Evaluation</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-ts-select.styled
                    label="Policy"
                    wire:model.live="roleplay_policy"
                    :options="$roleplayPolicyOptions"
                    select="label:label|value:value"
                    :clearable="false" />
                @error('roleplay_policy') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled
                    label="Scenario Pack"
                    wire:model.live="roleplay_scenario_pack_id"
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
                    wire:model.live="roleplay_pass_threshold" />
                @error('roleplay_pass_threshold') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>

        <div class="pt-2">
            <x-ts-banner>
                If policy is <strong>Required</strong>, candidates must complete a roleplay and achieve the pass threshold before advancing to interview.
            </x-ts-banner>
        </div>
    </x-ts-card>

    {{-- ===== Deal-breaker criteria ===== --}}
    <x-ts-card class="p-5 space-y-5 ring-brand">
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

        <div class="space-y-3">
            @forelse($screening_rules as $i => $row)
                @php
                    $field = $row['field'] ?? null;
                    $ops   = $this->operatorOptions($field);
                    $vOpts = $this->valueOptions($field);
                    $type  = $this->fieldMeta($field)['type'];
                    $isBetween = (($row['op'] ?? null) === 'between');
                    $rowKey = $row['id'] ?? ('row-'.$i);
                @endphp

                <div class="p-3 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)]" wire:key="rule-{{ $rowKey }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-3">
                            <x-ts-select.styled
                                label="Field"
                                wire:model.live="screening_rules.{{ $i }}.field"
                                :options="$screeningFieldOptions"
                                select="label:label|value:value"
                                placeholder="Choose a field" />
                        </div>

                        <div class="md:col-span-2">
                            <x-ts-select.styled
                                label="Operator"
                                wire:model.live="screening_rules.{{ $i }}.op"
                                :options="$ops"
                                select="label:label|value:value"
                                placeholder="Op" />
                        </div>

                        <div class="md:col-span-4">
                            @if($type === 'number' || $type === 'number_money')
                                @if($isBetween)
                                    <div class="grid grid-cols-2 gap-2">
                                        <x-ts-input type="number" label="Min" wire:model.live="screening_rules.{{ $i }}.min" />
                                        <x-ts-input type="number" label="Max" wire:model.live="screening_rules.{{ $i }}.max" />
                                    </div>
                                @else
                                    <x-ts-input type="number" label="Value" wire:model.live="screening_rules.{{ $i }}.value" />
                                @endif
                            @elseif($type === 'date')
                                <x-ts-input type="date" label="Date" wire:model.live="screening_rules.{{ $i }}.value" />
                            @elseif($type === 'boolean')
                                <x-ts-select.styled
                                    label="Value"
                                    wire:model.live="screening_rules.{{ $i }}.value"
                                    :options="[['label'=>'Yes','value'=>true],['label'=>'No','value'=>false]]"
                                    select="label:label|value:value"
                                    :clearable="false" />
                            @elseif(in_array($type, ['multiselect','multiselect_enum_ot','multiselect_enum_cs']))
                                <x-ts-select.styled
                                    label="Values"
                                    :multiple="true" searchable
                                    wire:model.live="screening_rules.{{ $i }}.value"
                                    :options="$vOpts"
                                    select="label:label|value:value"
                                    placeholder="Choose one or more" />
                            @elseif($type === 'select' || $type === 'select_workauth')
                                <x-ts-select.styled
                                    label="Value"
                                    :multiple="(($row['op'] ?? null) === 'in')"
                                    searchable
                                    wire:model.live="screening_rules.{{ $i }}.value"
                                    :options="$vOpts"
                                    select="label:label|value:value"
                                    placeholder="Choose" />
                            @else
                                <x-ts-input label="Value" wire:model.live="screening_rules.{{ $i }}.value" />
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <x-ts-select.styled
                                label="Severity"
                                wire:model.live="screening_rules.{{ $i }}.severity"
                                :options="[['label'=>'Fail (deal-breaker)','value'=>'fail'],['label'=>'Flag (soft)','value'=>'flag']]"
                                select="label:label|value:value"
                                :clearable="false" />
                        </div>

                        <div class="md:col-span-1 flex justify-end md:justify-center">
                            <x-ts-button size="sm" class="btn-accent outline" wire:click="removeRuleRow({{ $i }})">
                                Remove
                            </x-ts-button>
                        </div>

                        <div class="md:col-span-12">
                            <x-ts-input
                                label="Optional note (shown internally)"
                                placeholder="E.g., Must live in TX or OK; commission-only mindset required for this territory."
                                wire:model.live="screening_rules.{{ $i }}.message" />
                        </div>
                    </div>
                </div>
            @empty
                <x-ts-banner>No criteria added yet. Add at least one rule to filter for must-haves.</x-ts-banner>
            @endforelse

            <div>
                <x-ts-button class="btn-brand outline" wire:click="addRuleRow">
                    + Add criterion
                </x-ts-button>
            </div>
        </div>
    </x-ts-card>

    <div class="flex items-center justify-end gap-2">
        <x-ts-button class="btn-brand" wire:click="save('stay')">Save</x-ts-button>
        <x-ts-button class="btn-accent" wire:click="save('index')">Save & return</x-ts-button>
    </div>
</div>
