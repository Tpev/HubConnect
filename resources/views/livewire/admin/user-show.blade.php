{{-- resources/views/livewire/admin/user-show.blade.php --}}

<x-slot name="header">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                User #{{ $user->id }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">View profile, status and team memberships.</p>
        </div>
        <div class="hidden md:flex items-center gap-2">
            <x-ts-button as="a" href="{{ route('admin.users.index') }}" class="btn-accent outline" icon="arrow-uturn-left">
                Back to Users
            </x-ts-button>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- Admin subnav (same component you already use) --}}
    <x-admin.nav />

    {{-- Flash success --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Loading bar while toggling etc. --}}
    <div wire:loading class="h-0.5 w-full bg-gradient-to-r from-emerald-500 via-sky-500 to-fuchsia-500 animate-pulse"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile / Status --}}
        <x-ts-card class="lg:col-span-1 overflow-hidden ring-1 ring-slate-200 bg-white">
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>

            <div class="p-5 space-y-5">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->profile_photo_url }}" class="h-16 w-16 rounded-full ring-1 ring-slate-200" alt="">
                    <div class="min-w-0">
                        <div class="text-lg font-semibold text-slate-900 truncate">
                            {{ $user->name ?: '—' }}
                        </div>
                        <div class="text-sm text-slate-600 truncate">
                            {{ $user->email }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Verified</div>
                        <div class="mt-1">
                            @if($user->email_verified_at)
                                <x-ts-badge class="badge-brand">Yes</x-ts-badge>
                            @else
                                <x-ts-badge class="badge-accent">No</x-ts-badge>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Admin</div>
                        <div class="mt-1">
                            @if($user->is_admin)
                                <x-ts-badge class="badge-brand">Admin</x-ts-badge>
                            @else
                                <x-ts-badge>—</x-ts-badge>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-ts-button wire:click="toggleAdmin" class="btn-accent outline" icon="adjustments-horizontal">
                        Toggle Admin
                    </x-ts-button>
                    <x-ts-button as="a" href="mailto:{{ $user->email }}" class="btn-brand" icon="envelope-open">
                        Email User
                    </x-ts-button>
                </div>
            </div>
        </x-ts-card>

        {{-- Teams --}}
        <x-ts-card class="lg:col-span-2 ring-1 ring-slate-200 bg-white">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="font-semibold">Teams (Member Of)</div>
                <div class="text-sm text-slate-600">
                    {{ number_format($user->teams->count()) }} total
                </div>
            </div>

            <div class="p-0">
                @if($user->teams->count())
                    <ul class="divide-y divide-slate-100">
                        @foreach($user->teams as $team)
                            <li class="px-4 py-3 sm:px-5 flex items-center justify-between hover:bg-slate-50/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($team->team_profile_photo_path)
                                        <img src="{{ Storage::url($team->team_profile_photo_path) }}" class="h-9 w-9 rounded-lg ring-1 ring-slate-200" alt="">
                                    @else
                                        <div class="h-9 w-9 rounded-lg bg-slate-100 ring-1 ring-slate-200"></div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900 truncate">
                                            {{ $team->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 truncate">
                                            {{ $team->company_type ?? '—' }}
                                        </div>
                                    </div>
                                </div>

                                <x-ts-button
                                    as="a"
                                    href="{{ route('admin.companies.show', $team->id) }}"
                                    size="sm"
                                    class="btn-accent"
                                >
                                    View
                                </x-ts-button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center">
                        <div class="mx-auto mb-3 h-10 w-10 rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center">
                            <x-ts-icon name="users" class="h-5 w-5 text-slate-400"/>
                        </div>
                        <div class="text-slate-900 font-medium">No team memberships</div>
                        <div class="text-sm text-slate-500 mt-1">This user hasn’t joined any teams yet.</div>
                    </div>
                @endif
            </div>
        </x-ts-card>

    </div>
</div>
