{{-- resources/views/livewire/admin/dashboard.blade.php --}}

<x-slot name="header">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Overview of your HubConnect instance.</p>
        </div>
        <div class="hidden md:flex items-center gap-2">
            <ts-button href="{{ route('admin.users.index') }}" variant="soft">Manage Users</ts-button>
            <ts-button href="{{ route('admin.companies.index') }}" variant="soft">Manage Companies</ts-button>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto p-6 space-y-8">

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Users --}}
        <ts-card class="overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
            <ts-card.content class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">Total Users</div>
                        <div class="mt-1 text-4xl font-bold text-slate-900">{{ $usersCount }}</div>
                    </div>
                    <div class="shrink-0 rounded-2xl p-3 bg-emerald-50 ring-1 ring-emerald-100">
                        {{-- user icon --}}
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <ts-button href="{{ route('admin.users.index') }}" size="sm" variant="outline">View users</ts-button>
                </div>
            </ts-card.content>
        </ts-card>

        {{-- Companies --}}
        <ts-card class="overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-sky-500 to-sky-300"></div>
            <ts-card.content class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">Total Companies</div>
                        <div class="mt-1 text-4xl font-bold text-slate-900">{{ $companiesCount }}</div>
                    </div>
                    <div class="shrink-0 rounded-2xl p-3 bg-sky-50 ring-1 ring-sky-100">
                        {{-- building icon --}}
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-sky-600" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 21h18" />
                            <path d="M19 21V8a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2v15" />
                            <path d="M9 8h6" />
                            <path d="M9 12h6" />
                            <path d="M9 16h6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <ts-button href="{{ route('admin.companies.index') }}" size="sm" variant="outline">View companies</ts-button>
                </div>
            </ts-card.content>
        </ts-card>

        {{-- Admins --}}
        <ts-card class="overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-violet-500 to-fuchsia-400"></div>
            <ts-card.content class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">Administrators</div>
                        <div class="mt-1 text-4xl font-bold text-slate-900">{{ $adminsCount }}</div>
                    </div>
                    <div class="shrink-0 rounded-2xl p-3 bg-violet-50 ring-1 ring-violet-100">
                        {{-- shield icon --}}
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 22s8-4 8-10V6l-8-4-8 4v6c0 6 8 10 8 10z" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <ts-badge color="primary">Admin-only area</ts-badge>
                </div>
            </ts-card.content>
        </ts-card>
    </div>

    {{-- Two-column lower section: shortcuts + docs/help --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <ts-card class="lg:col-span-2">
            <ts-card.header class="flex items-center justify-between">
                <div class="font-semibold">Quick Actions</div>
            </ts-card.header>
            <ts-card.content class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    <a href="{{ route('admin.users.index') }}" class="group">
                        <div class="w-full rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:shadow transition">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl p-2 bg-emerald-50 ring-1 ring-emerald-100">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">Browse Users</div>
                                    <div class="text-xs text-slate-500">Search, filter, and inspect user details.</div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.companies.index') }}" class="group">
                        <div class="w-full rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:shadow transition">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl p-2 bg-sky-50 ring-1 ring-sky-100">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-sky-600" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 21h18" />
                                        <path d="M19 21V8a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2v15" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">Browse Companies</div>
                                    <div class="text-xs text-slate-500">Teams, members, specialties, and more.</div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('dashboard') }}" class="group">
                        <div class="w-full rounded-xl border border-slate-200 p-4 hover:border-fuchsia-300 hover:shadow transition">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl p-2 bg-fuchsia-50 ring-1 ring-fuchsia-100">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-fuchsia-600" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 3h18v4H3zM3 11h18v10H3z" />
                                        <path d="M7 7v4M12 7v4M17 7v4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">Go to App</div>
                                    <div class="text-xs text-slate-500">Back to the main application.</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </ts-card.content>
        </ts-card>

        <ts-card>
            <ts-card.header class="font-semibold">Help & Tips</ts-card.header>
            <ts-card.content class="p-5 space-y-3 text-sm text-slate-700">
                <p>Need to grant admin privileges?</p>
                <pre class="bg-slate-50 rounded-lg p-3 text-xs overflow-x-auto border border-slate-200">
php artisan tinker
&gt;&gt;&gt; App\Models\User::where('email','test@test.com')->update(['is_admin' =&gt; true]);
                </pre>
                <p class="text-slate-500">Use the Users page to verify email status and team membership. Companies page shows team metadata and members.</p>
            </ts-card.content>
        </ts-card>
    </div>
</div>
