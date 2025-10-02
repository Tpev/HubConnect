{{-- resources/views/livewire/admin/users-index.blade.php --}}

<x-slot name="header">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">Users</h1>
            <p class="mt-1 text-sm text-slate-500">Browse, search and inspect users. Use filters to narrow results.</p>
        </div>
        <div class="hidden md:flex items-center gap-2">
            <x-ts-button as="a" href="{{ route('admin.dashboard') }}" class="btn-accent outline" icon="arrow-uturn-left">
                Back to Admin
            </x-ts-button>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- Top actions (mirrors landing style) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2 text-sm">
            <span class="badge-brand">HubConnect</span>
            <span class="font-medium"> — Admin • Users</span>
        </div>

        <div class="flex items-center gap-2">
            <x-ts-button as="a" href="{{ route('admin.users.index') }}" class="btn-brand" icon="arrow-path">
                Refresh
            </x-ts-button>
            <x-ts-button as="button"
                class="btn-accent outline"
                icon="sparkles"
                x-data
                @click="$wire.$set('q',''); $wire.$set('only','all'); $wire.$set('perPage',25)"
            >
                Reset filters
            </x-ts-button>
        </div>
    </div>

    <x-ts-card class="overflow-hidden ring-1 ring-slate-200 bg-white">
        {{-- Livewire loading bar --}}
        <div wire:loading class="h-0.5 w-full bg-gradient-to-r from-emerald-500 via-sky-500 to-fuchsia-500 animate-pulse"></div>

        {{-- Toolbar --}}
        <div class="p-4 sm:p-5 border-b border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-4">
                    <x-ts-input
                        wire:model.debounce.300ms="q"
                        placeholder="Search name or email…"
                        leading="magnifying-glass"
                    />
                </div>

                <div class="lg:col-span-3">
                    <x-ts-select.native wire:model.live="only" :options="[
                        ['label' => 'All users',    'value' => 'all'],
                        ['label' => 'Admins only',  'value' => 'admins'],
                        ['label' => 'Verified',     'value' => 'verified'],
                        ['label' => 'Unverified',   'value' => 'unverified'],
                    ]" />
                </div>

                <div class="lg:col-span-2">
                    <x-ts-select.native wire:model.live="perPage" :options="[
                        ['label' => '10 per page',  'value' => 10],
                        ['label' => '25 per page',  'value' => 25],
                        ['label' => '50 per page',  'value' => 50],
                        ['label' => '100 per page', 'value' => 100],
                    ]" />
                </div>

                <div class="lg:col-span-3 flex items-center justify-end">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ number_format($users->total()) }}</span> total users
                    </div>
                </div>
            </div>
        </div>

        {{-- Table / Empty state --}}
        <div class="p-0">
            @if ($users->count() === 0)
                <div class="p-10 text-center">
                    <div class="mx-auto mb-3 h-10 w-10 rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center">
                        <x-ts-icon name="magnifying-glass" class="h-5 w-5 text-slate-400" />
                    </div>
                    <div class="text-slate-900 font-medium">No users found</div>
                    <div class="text-sm text-slate-500 mt-1">Try adjusting your search or filters.</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 w-16">ID</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3 text-center w-28">Verified</th>
                                <th class="px-4 py-3 text-center w-28">Admin</th>
                                <th class="px-4 py-3 text-center w-24">Teams</th>
                                <th class="px-4 py-3 w-24"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $u)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3 text-slate-500">{{ $u->id }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $u->profile_photo_url }}" class="h-9 w-9 rounded-full ring-1 ring-slate-200" alt="">
                                            <div class="leading-tight">
                                                <div class="font-medium text-slate-900">{{ $u->name ?: '—' }}</div>
                                                <div class="text-[11px] text-slate-500">User #{{ $u->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-sm">{{ $u->email }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($u->email_verified_at)
                                            <x-ts-badge class="badge-brand">Yes</x-ts-badge>
                                        @else
                                            <x-ts-badge class="badge-accent">No</x-ts-badge>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($u->is_admin)
                                            <x-ts-badge class="badge-brand">Admin</x-ts-badge>
                                        @else
                                            <x-ts-badge class="">—</x-ts-badge>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $u->teams_count }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <x-ts-button as="a" href="{{ route('admin.users.show', $u) }}" size="sm" class="btn-accent">
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
                    {{ $users->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </x-ts-card>
</div>
