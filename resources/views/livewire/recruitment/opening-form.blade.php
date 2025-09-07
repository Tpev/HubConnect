<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
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
                <x-ts-button wire:click="save('stay')">Save</x-ts-button>
            @endif
            <x-ts-button class="btn-accent" wire:click="save('index')">Save & return</x-ts-button>
        </div>
    </div>

    <x-ts-card class="p-5 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2">
                <x-ts-input label="Title" placeholder="e.g., Territory Rep — Spine Ortho (Île-de-France)" wire:model.live="title" />
                @error('title') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled label="Company Type" wire:model.live="company_type" :options="$companyTypeOptions" select="label:label|value:value" />
                @error('company_type') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled label="Status" wire:model.live="status" :options="$statusOptions" select="label:label|value:value" />
                @error('status') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-1">
                <x-ts-select.styled label="Specialties" wire:model.live="specialty_ids" :options="$specialtyOptions" multiple searchable select="label:label|value:value" placeholder="Select specialties" />
                @error('specialty_ids') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-1">
                <x-ts-select.styled label="Territories" wire:model.live="territory_ids" :options="$territoryOptions" multiple searchable select="label:label|value:value" placeholder="Select territories" />
                @error('territory_ids') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-1">
                <x-ts-input label="Compensation" placeholder="e.g., Commission 20% + monthly draw" wire:model.live="compensation" />
                @error('compensation') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="md:col-span-1">
                <x-ts-input type="date" label="Visible until" wire:model.live="visibility_until" />
                @error('visibility_until') <x-ts-error :text="$message" /> @enderror
            </div>

            <div class="col-span-1 md:col-span-2">
                <x-ts-textarea label="Description" wire:model.live="description" rows="6" placeholder="Describe the role, responsibilities, requirements…" />
                @error('description') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>
    </x-ts-card>

    <x-ts-card class="p-5 space-y-5">
        <h2 class="text-lg font-semibold">Roleplay Evaluation</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-ts-select.styled label="Policy" wire:model.live="roleplay_policy" :options="$roleplayPolicyOptions" select="label:label|value:value" />
                @error('roleplay_policy') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-select.styled label="Scenario Pack" wire:model.live="roleplay_scenario_pack_id" :options="$scenarioPackOptions" select="label:label|value:value" placeholder="Select pack (optional)" />
                @error('roleplay_scenario_pack_id') <x-ts-error :text="$message" /> @enderror
            </div>

            <div>
                <x-ts-input type="number" step="0.01" label="Pass threshold" placeholder="e.g., 75.00" wire:model.live="roleplay_pass_threshold" />
                @error('roleplay_pass_threshold') <x-ts-error :text="$message" /> @enderror
            </div>
        </div>

        <div class="pt-2">
            <x-ts-banner>
                If policy is <strong>Required</strong>, candidates must complete a roleplay and achieve the pass threshold before advancing to interview.
            </x-ts-banner>
        </div>
    </x-ts-card>

    <div class="flex items-center justify-end gap-2">
        <x-ts-button wire:click="save('stay')">Save</x-ts-button>
        <x-ts-button class="btn-accent" wire:click="save('index')">Save & return</x-ts-button>
    </div>
</div>
