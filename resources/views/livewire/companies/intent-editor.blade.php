<div class="max-w-3xl mx-auto space-y-8" wire:init>
    <x-ts-card class="ring-brand">
        <x-slot name="header" class="flex items-center justify-between">
            <div class="font-semibold text-lg">Currently Looking For</div>
            <div class="flex items-center gap-2">
                <span wire:loading class="text-xs text-slate-500">Saving…</span>
                @if (session('saved'))
                    <x-ts-badge class="badge-brand">{{ session('saved') }}</x-ts-badge>
                @endif
            </div>
        </x-slot>

        <div class="space-y-6">
            {{-- Territories (config/countries only; no Intl) --}}
            <x-ts-select.styled
                label="Territories"
                wire:model="territories"
                :options="$countries"
                multiple
                searchable
                placeholder="Choose territories"
            />
            @error('territories') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('territories.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            {{-- Specialties --}}
            <x-ts-select.styled
                label="Specialties"
                wire:model="specialties"
                :options="$allSpecialties->map(fn($s)=>['label'=>$s->name,'value'=>$s->id])->toArray()"
                multiple
                searchable
                placeholder="Select specialties"
            />
            @error('specialties') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('specialties.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="grid sm:grid-cols-3 gap-4">
                <x-ts-select.styled
                    label="Exclusivity"
                    wire:model="pref_exclusivity"
                    :options="[
                        ['label'=>'No preference','value'=>null],
                        ['label'=>'Yes','value'=>true],
                        ['label'=>'No','value'=>false],
                    ]"
                    placeholder="Select"
                />
                @error('pref_exclusivity') <p class="text-sm text-red-600 sm:col-span-1">{{ $message }}</p> @enderror

                <x-ts-select.styled
                    label="Consignment"
                    wire:model="pref_consignment"
                    :options="[
                        ['label'=>'No preference','value'=>null],
                        ['label'=>'Yes','value'=>true],
                        ['label'=>'No','value'=>false],
                    ]"
                    placeholder="Select"
                />
                @error('pref_consignment') <p class="text-sm text-red-600 sm:col-span-1">{{ $message }}</p> @enderror

                <x-ts-input
                    label="Min Commission (%)"
                    type="number"
                    wire:model.defer="pref_commission_min"
                    min="0" max="100"
                    placeholder="e.g. 10"
                />
                @error('pref_commission_min') <p class="text-sm text-red-600 sm:col-span-1">{{ $message }}</p> @enderror
            </div>

            <x-ts-input label="Urgency / Timeline" wire:model.defer="urgency" placeholder="e.g., Priority Q4 2025" />
            @error('urgency') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <x-ts-textarea label="Capacity / Notes" wire:model.defer="capacity_note" rows="3" />
            @error('capacity_note') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <x-ts-textarea label="Other Notes" wire:model.defer="notes" rows="3" />
            @error('notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="flex items-center gap-3">
                <x-ts-button wire:click="save" wire:loading.attr="disabled" class="btn-accent">
                    <span wire:loading.remove>Save “Looking For”</span>
                    <span wire:loading>Saving…</span>
                </x-ts-button>
                <span class="text-xs text-slate-500">Updates go live immediately.</span>
            </div>
        </div>
    </x-ts-card>
</div>
