{{-- resources/views/livewire/admin/companies-index.blade.php --}}

<x-slot name="header">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">Companies</h1>
            <p class="mt-1 text-sm text-slate-500">Browse teams, see member counts, and jump into a company’s profile.</p>
        </div>
        <div class="hidden md:flex items-center gap-2">
            <x-ts-button as="a" href="{{ route('admin.dashboard') }}" class="btn-accent outline" icon="arrow-uturn-left">
                Back to Admin
            </x-ts-button>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto p-6 space-y-8">
    {{-- Subnav --}}
    <x-admin.nav />

    {{-- Loading bar --}}
    <div wire:loading class="h-0.5 w-full bg-gradient-to-r from-emerald-500 via-sky-500 to-fuchsia-500 animate-pulse"></div>

    {{-- Top strip like on landing --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2 text-sm">
            <span class="badge-brand">HubConnect</span>
            <span class="font-medium"> — Admin • Companies</span>
        </div>

        <div class="flex items-center gap-2">
            <x-ts-button as="button"
                         class="btn-accent outline"
                         icon="sparkles"
                         x-data
                         @click="$wire.$set('q',''); $wire.$set('perPage',25)">
                Reset filters
            </x-ts-button>
        </div>
    </div>

    <x-ts-card class="overflow-hidden ring-1 ring-slate-200 bg-white">
        {{-- Toolbar --}}
        <div class="p-4 sm:p-5 border-b border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-5">
                    <x-ts-input
                        wire:model.debounce.300ms="q"
                        placeholder="Search name, slug, type, or country…"
                        leading="magnifying-glass"
                    />
                </div>

                <div class="lg:col-span-2">
                    <x-ts-select.native wire:model.live="perPage" :options="[
                        ['label' => '10 per page',  'value' => 10],
                        ['label' => '25 per page',  'value' => 25],
                        ['label' => '50 per page',  'value' => 50],
                        ['label' => '100 per page', 'value' => 100],
                    ]" />
                </div>

                <div class="lg:col-span-5 flex items-center justify-end">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ number_format($companies->total()) }}</span> total companies
                    </div>
                </div>
            </div>
        </div>

        {{-- Table / Empty state --}}
        <div class="p-0">
            @if ($companies->count() === 0)
                <div class="p-10 text-center">
                    <div class="mx-auto mb-3 h-10 w-10 rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center">
                        <x-ts-icon name="magnifying-glass" class="h-5 w-5 text-slate-400" />
                    </div>
                    <div class="text-slate-900 font-medium">No companies found</div>
                    <div class="text-sm text-slate-500 mt-1">Try adjusting your search.</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 w-16">ID</th>
                                <th class="px-4 py-3">Company</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Country</th>

                                {{-- NEW: Listed --}}
                                <th class="px-4 py-3 text-center w-28">Listed</th>

                                <th class="px-4 py-3 text-center w-28">Members</th>
                                <th class="px-4 py-3 w-28"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($companies as $c)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3 text-slate-500">{{ $c->id }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($c->team_profile_photo_path)
                                                <img src="{{ Storage::url($c->team_profile_photo_path) }}" class="h-9 w-9 rounded-lg ring-1 ring-slate-200" alt="">
                                            @else
                                                <div class="h-9 w-9 rounded-lg bg-slate-100 ring-1 ring-slate-200"></div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-medium text-slate-900 truncate">{{ $c->name }}</div>
                                                <div class="text-[11px] text-slate-500 truncate">{{ $c->slug }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        @if($c->company_type)
                                            <x-ts-badge class="badge-brand">{{ $c->company_type }}</x-ts-badge>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        @if($c->hq_country)
                                            <x-ts-badge class="badge-accent">{{ $c->hq_country }}</x-ts-badge>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>

                                    {{-- NEW: Listed toggle --}}
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            wire:click="toggleListed({{ $c->id }})"
                                            wire:loading.attr="disabled"
                                            aria-pressed="{{ $c->is_listed ? 'true' : 'false' }}"
                                            class="inline-flex items-center gap-2 group focus:outline-none"
                                            title="{{ $c->is_listed ? 'Visible in directory' : 'Hidden from directory' }}"
                                        >
                                            <span
                                                class="relative inline-flex h-5 w-9 rounded-full transition-colors duration-200 ease-out ring-1 ring-slate-200
                                                       {{ $c->is_listed ? 'bg-emerald-500/90' : 'bg-slate-300' }}">
                                                <span
                                                    class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200 ease-out
                                                           {{ $c->is_listed ? 'translate-x-4' : 'translate-x-0' }}">
                                                </span>
                                            </span>
                                            <span class="text-xs {{ $c->is_listed ? 'text-slate-700' : 'text-slate-500' }}">
                                                {{ $c->is_listed ? 'Visible' : 'Hidden' }}
                                            </span>
                                        </button>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="font-medium text-slate-900">{{ $c->members_count ?? 0 }}</span>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <x-ts-button as="a"
                                                     href="{{ route('admin.companies.show', $c->id) }}"
                                                     size="sm"
                                                     class="btn-accent">
                                            View
                                        </x-ts-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-4">
                    {{ $companies->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </x-ts-card>
</div>
