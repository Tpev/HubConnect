{{-- resources/views/livewire/matchmaking/request-match.blade.php --}}
<div>
<x-ts-button size="sm" wire:click="$toggle('open')">
    Request Connection
</x-ts-button>


    <x-ts-modal wire:model="open" title="Request to represent {{ $device?->name }}">
        <div class="space-y-3">
            @php
                $openTerritoryOptions = collect($openTerritories)->map(
                    fn($t) => ['label'=>$t->name, 'value'=>$t->id]
                )->toArray();
            @endphp

            <x-ts-select.styled
                :options="$openTerritoryOptions"
                wire:model="territoryIds"
                multiple
                searchable
                placeholder="Select territories"
            />

            <div class="flex items-center gap-2">
                <x-ts-toggle wire:model="exclusivity" />
                <span class="text-sm">Request exclusivity</span>
            </div>

            <x-ts-input
                type="number"
                step="0.01"
                wire:model="proposedCommissionPercent"
                placeholder="Proposed commission % (optional)"
            />

            <x-ts-textarea
                wire:model="message"
                placeholder="Short intro & plan to sell (optional)">
            </x-ts-textarea>
        </div>

        <x-slot:footer>
            <x-ts-button wire:click="submit">Send request</x-ts-button>
            <x-ts-button flat wire:click="$set('open', false)">Cancel</x-ts-button>
        </x-slot:footer>
    </x-ts-modal>
</div>
