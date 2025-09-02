{{-- resources/views/manufacturer/devices/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">
                My Devices
            </h2>
            <x-ts-button as="a" href="{{ route('m.devices.create') }}">
                New Device
            </x-ts-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Optional page-level note or actions (no $rows here) --}}
            <x-ts-card class="p-4">
                <p class="text-sm text-slate-600">
                    Manage your devices. Use search and filters below. The table and map are powered by Livewire.
                </p>
            </x-ts-card>

            {{-- Livewire component owns data ($rows, $targetStates, etc.) --}}
            <livewire:manufacturer.device-index />

        </div>
    </div>
</x-app-layout>
