<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight">Companies</h1>
                <p class="mt-1 text-sm text-slate-500">Browse manufacturers & distributors and request intros.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Filters + stats --}}
            <livewire:companies.search-bar />

            {{-- Results grid --}}
            <livewire:companies.results />
        </div>
    </div>
</x-app-layout>
