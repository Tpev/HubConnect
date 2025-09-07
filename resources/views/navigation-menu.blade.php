{{-- resources/views/navigation-menu.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;

    $user        = Auth::user();
    $team        = $user?->currentTeam;
    $companyType = $team?->company_type; // 'manufacturer' | 'distributor' | 'both' | null

    $isActive = function (string|array $patterns): string {
        foreach ((array)$patterns as $p) {
            if (request()->routeIs($p)) return 'text-[var(--brand-700)]';
        }
        return 'text-slate-600 hover:text-[var(--brand-700)]';
    };
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white/85 backdrop-blur border-b border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Left: Logo + Primary links --}}
            <div class="flex items-center gap-6">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center shrink-0">
                    <x-application-mark class="block h-9 w-auto" />
                </a>

                {{-- Desktop nav --}}
                <ul class="hidden sm:flex items-center gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-lg font-semibold {{ $isActive('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    {{-- Manufacturer menu --}}
                    @if($companyType === 'manufacturer' || $companyType === 'both')
                        <li>
                            <a href="{{ route('m.devices') }}"
                               class="px-3 py-2 rounded-lg font-semibold {{ $isActive(['m.devices','m.devices.*']) }}">
                                My Devices
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('m.devices.create') }}"
                               class="px-3 py-2 rounded-lg font-semibold {{ $isActive('m.devices.create') }}">
                                New Device
                            </a>
                        </li>
                    @endif

                    {{-- Distributor menu --}}
                    @if($companyType === 'distributor' || $companyType === 'both')
                        <li>
                            <a href="{{ route('devices.index') }}"
                               class="px-3 py-2 rounded-lg font-semibold {{ $isActive(['devices.index','devices.show']) }}">
                                Catalog
                            </a>
                        </li>
                    @endif

                    {{-- Recruitment (employer) --}}
                    @if(Route::has('employer.openings'))
                        <li>
                            <a href="{{ route('employer.openings') }}"
                               class="px-3 py-2 rounded-lg font-semibold {{ $isActive(['employer.openings','employer.openings.*']) }}">
                                Recruitment
                            </a>
                        </li>
                    @endif

                    {{-- Admin --}}
                    @if($user?->is_admin ?? false)
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-2 rounded-lg font-semibold {{ $isActive('admin.*') }}">
                                Admin
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Right: Actions + Team + User --}}
            <div class="hidden sm:flex items-center gap-3">

                {{-- Public jobs quick link (opens the public board) --}}
                @if(Route::has('openings.index'))
                    <a href="{{ route('openings.index') }}" class="btn-accent outline text-sm">
                        Public Jobs
                    </a>
                @endif

                {{-- Teams Dropdown (Jetstream) --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg
                                               text-slate-600 hover:text-[var(--brand-700)] bg-white ring-1 ring-[var(--border)]">
                                    {{ Auth::user()->currentTeam->name }}
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        Team Settings
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            Create New Team
                                        </x-dropdown-link>
                                    @endcan

                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 my-1"></div>
                                        <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>
                                        @foreach (Auth::user()->allTeams() as $teamOption)
                                            <x-switchable-team :team="$teamOption" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                {{-- User Dropdown (Jetstream) --}}
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-[var(--brand-300)] transition">
                                    <img class="size-8 rounded-full object-cover"
                                         src="{{ Auth::user()->profile_photo_url }}"
                                         alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg
                                               text-slate-600 hover:text-[var(--brand-700)] bg-white ring-1 ring-[var(--border)]">
                                    {{ Auth::user()->name }}
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>
                            <x-dropdown-link href="{{ route('profile.show') }}">Profile</x-dropdown-link>
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">API Tokens</x-dropdown-link>
                            @endif
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-slate-500 hover:text-[var(--brand-700)] hover:bg-[var(--brand-50)]
                               focus:outline-none transition">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-[var(--border)]">
        <div class="px-4 pt-2 pb-3 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('dashboard') }}">
                Dashboard
            </a>

            @if($companyType === 'manufacturer' || $companyType === 'both')
                <a href="{{ route('m.devices') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive(['m.devices','m.devices.*']) }}">
                    My Devices
                </a>
                <a href="{{ route('m.devices.create') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('m.devices.create') }}">
                    New Device
                </a>
            @endif

            @if($companyType === 'distributor' || $companyType === 'both')
                <a href="{{ route('devices.index') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive(['devices.index','devices.show']) }}">
                    Catalog
                </a>
            @endif

            @if(Route::has('employer.openings'))
                <a href="{{ route('employer.openings') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive(['employer.openings','employer.openings.*']) }}">
                    Recruitment
                </a>
            @endif

            @if($user?->is_admin ?? false)
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('admin.*') }}">
                    Admin
                </a>
            @endif

            @if(Route::has('openings.index'))
                <a href="{{ route('openings.index') }}" class="inline-flex items-center mt-2 btn-accent outline text-sm">
                    Public Jobs
                </a>
            @endif
        </div>

        {{-- Mobile: user/teams --}}
        <div class="pt-4 pb-4 border-t border-[var(--border)]">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover"
                             src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif
                <div>
                    <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <a href="{{ route('profile.show') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('profile.show') }}">
                    Profile
                </a>
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <a href="{{ route('api-tokens.index') }}"
                       class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('api-tokens.index') }}">
                        API Tokens
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                       class="block px-3 py-2 rounded-lg font-semibold text-slate-600 hover:text-[var(--brand-700)]">
                        Log Out
                    </a>
                </form>

                {{-- Teams (mobile) --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-[var(--border)] my-2"></div>
                    <div class="block px-3 py-2 text-xs text-gray-400">Manage Team</div>
                    <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                       class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('teams.show') }}">
                        Team Settings
                    </a>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <a href="{{ route('teams.create') }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('teams.create') }}">
                            Create New Team
                        </a>
                    @endcan
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-[var(--border)] my-2"></div>
                        <div class="block px-3 py-2 text-xs text-gray-400">Switch Teams</div>
                        @foreach (Auth::user()->allTeams() as $teamOption)
                            <x-switchable-team :team="$teamOption" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
