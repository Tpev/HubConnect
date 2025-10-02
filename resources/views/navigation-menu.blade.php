{{-- resources/views/navigation-menu.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;

    $user        = Auth::user();
    $team        = $user?->currentTeam;
    $companyId   = $team?->id;
    $companyType = $team?->company_type; // 'manufacturer' | 'distributor' | 'both' | null

    // Profile completeness (tweak to your needs)
    $profileIncomplete = $team && (
        blank($team->company_type) ||
        blank($team->name) ||
        blank($team->hq_country) ||
        blank($team->website)
    );

    // KYC state
    $companyVerified = (bool) ($team?->kyc_status === 'approved');
    $needsBasics     = $team
        ? (blank($team->name) || blank($team->company_type) || blank($team->hq_country))
        : false;

    // Pending connection requests (badge)
    $pendingRequestsCount = $team
        ? \App\Models\MatchRequest::where('to_company_id', $team->id)->where('status', 'pending')->count()
        : 0;

    // Deal Rooms unread total (badge)
    $dealUnreadTotal = 0;
    if ($companyId) {
        $roomsForNav = \App\Models\DealRoom::query()->forCompany($companyId)->get();
        foreach ($roomsForNav as $r) {
            $dealUnreadTotal += $r->unreadCountFor($companyId);
        }
    }
    $dealUnreadDisplay = $dealUnreadTotal > 99 ? '99+' : $dealUnreadTotal;

    // Directory label based on company type
    $exploreLabel = match ($companyType) {
        'manufacturer' => 'Find Distributors',
        'distributor'  => 'Find Manufacturers',
        'both'         => 'Explore Partners',
        default        => 'Explore',
    };

    // Recruitment label (simple, clear)
    $hiringLabel = 'Hiring';

    // helper for active states
    $isActive = function (string|array $patterns): string {
        foreach ((array)$patterns as $p) {
            if (request()->routeIs($p)) return 'text-[var(--brand-700)]';
        }
        return 'text-slate-600 hover:text-[var(--brand-700)]';
    };

    // When locked, where should the user be sent?
    $lockedHref = function () use ($team, $needsBasics) {
        return $needsBasics
            ? ($team ? route('companies.profile.edit', $team) : route('dashboard'))
            : route('kyc.gate');
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
                           class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    {{-- Explore directory (locked until verified) --}}
                    <li>
                        @if($companyVerified)
                            <a href="{{ route('companies.index') }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive(['companies.index','companies.show','companies.profile.edit','companies.intent.edit']) }}">
                                {{ $exploreLabel }}
                            </a>
                        @else
                            <a href="{{ $lockedHref() }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap text-slate-400 hover:text-slate-500"
                               title="{{ $needsBasics ? 'Complete your company basics to submit for verification' : 'Pending verification — usually within one business day' }}">
                                {{-- lock icon --}}
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <span>{{ $exploreLabel }}</span>
                            </a>
                        @endif
                    </li>

                    {{-- Connections (locked until verified) --}}
                    <li>
                        @if($companyVerified)
                            <a href="{{ route('requests.index') }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive('requests.index') }}">
                                <span>Connections</span>
                                @if($pendingRequestsCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-rose-600 text-white text-[11px] leading-none">
                                        {{ $pendingRequestsCount }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ $lockedHref() }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap text-slate-400 hover:text-slate-500"
                               title="{{ $needsBasics ? 'Complete your company basics to submit for verification' : 'Pending verification — usually within one business day' }}">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <span>Connections</span>
                            </a>
                        @endif
                    </li>

                    {{-- Deal Rooms (locked until verified) --}}
                    <li>
                        @if($companyVerified)
                            <a href="{{ route('deal-rooms.index') }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive('deal-rooms.index') }}">
                                <span>Deal Rooms</span>
                                @if($dealUnreadTotal > 0)
                                    <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-indigo-600 text-white text-[11px] leading-none">
                                        {{ $dealUnreadDisplay }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ $lockedHref() }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap text-slate-400 hover:text-slate-500"
                               title="{{ $needsBasics ? 'Complete your company basics to submit for verification' : 'Pending verification — usually within one business day' }}">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <span>Deal Rooms</span>
                            </a>
                        @endif
                    </li>

                    {{-- Hiring (Recruitment) --}}
                    @if(Route::has('employer.openings'))
                        <li>
                            <a href="{{ route('employer.openings') }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive(['employer.openings','employer.openings.*']) }}">
                                {{ $hiringLabel }}
                            </a>
                        </li>
                    @endif

                    {{-- Admin --}}
                    @if($user?->is_admin ?? false)
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-1.5 whitespace-nowrap {{ $isActive('admin.*') }}">
                                Admin
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Right: Actions + Team + User --}}
            <div class="hidden sm:flex items-center gap-3">

                {{-- Public board --}}
                @if(Route::has('openings.index'))
                    <a href="{{ route('openings.index') }}" class="btn-accent outline text-sm whitespace-nowrap inline-flex items-center">
                        Jobs Board
                    </a>
                @endif

                {{-- Company / Team Dropdown (Jetstream) --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg
                                               text-slate-600 hover:text-[var(--brand-700)] bg-white ring-1 ring-[var(--border)] whitespace-nowrap">
                                    {{ $team?->name ?? 'My Company' }}
                                    @if(!$companyVerified)
                                        <span class="ms-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                                     {{ $needsBasics ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $needsBasics ? 'Complete basics' : 'Pending review' }}
                                        </span>
                                    @elseif($profileIncomplete)
                                        <span class="ms-2 inline-block w-2 h-2 rounded-full bg-amber-500" title="Profile incomplete"></span>
                                    @endif
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">Company</div>

                                    @if($team)
                                        <x-dropdown-link href="{{ route('companies.show', $team) }}">
                                            View Company
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('companies.profile.edit', $team) }}">
                                            Edit Company
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('companies.intent.edit', $team) }}">
                                            Partner Preferences
                                        </x-dropdown-link>

                                        @if(!$companyVerified)
                                            <div class="px-4 py-2 text-xs
                                                {{ $needsBasics ? 'text-amber-700' : 'text-blue-700' }}">
                                                {{ $needsBasics
                                                    ? 'Complete your basics to submit for verification.'
                                                    : 'Your company is pending manual review (usually within one business day).'
                                                }}
                                                <a href="{{ $lockedHref() }}" class="underline">View status</a>
                                            </div>
                                        @elseif($profileIncomplete)
                                            <div class="px-4 py-2 text-xs text-amber-600">
                                                Complete your profile to appear higher in search.
                                            </div>
                                        @endif
                                    @endif

                                    <div class="border-t border-gray-200 my-1"></div>
                                    <div class="block px-4 py-2 text-xs text-gray-400">Team</div>

                                    @if($team)
                                        <x-dropdown-link href="{{ route('teams.show', $team->id) }}">
                                            Team Settings
                                        </x-dropdown-link>
                                    @endif

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            Create New Team
                                        </x-dropdown-link>
                                    @endcan

                                    @if ($user && $user->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 my-1"></div>
                                        <div class="block px-4 py-2 text-xs text-gray-400">Switch Company</div>
                                        @foreach ($user->allTeams() as $teamOption)
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
                                         src="{{ $user->profile_photo_url }}"
                                         alt="{{ $user->name }}" />
                                </button>
                            @else
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg
                                               text-slate-600 hover:text-[var(--brand-700)] bg-white ring-1 ring-[var(--border)] whitespace-nowrap">
                                    {{ $user->name }}
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
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

            {{-- Explore (mobile) --}}
            @if($companyVerified)
                <a href="{{ route('companies.index') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive(['companies.index','companies.show','companies.profile.edit','companies.intent.edit']) }}">
                    {{ $exploreLabel }}
                </a>
            @else
                <a href="{{ $lockedHref() }}"
                   class="block px-3 py-2 rounded-lg font-semibold text-slate-400 hover:text-slate-500"
                   title="{{ $needsBasics ? 'Complete your company basics to submit for verification' : 'Pending verification — usually within one business day' }}">
                    {{-- lock icon --}}
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span>{{ $exploreLabel }}</span>
                    </span>
                </a>
            @endif

            {{-- Connections (mobile) --}}
            @if($companyVerified)
                <a href="{{ route('requests.index') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('requests.index') }}">
                    Connections
                    @if($pendingRequestsCount > 0)
                        <span class="ms-2 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-rose-600 text-white text-xs">
                            {{ $pendingRequestsCount }}
                        </span>
                    @endif
                </a>
            @else
                <a href="{{ $lockedHref() }}"
                   class="block px-3 py-2 rounded-lg font-semibold text-slate-400 hover:text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span>Connections</span>
                    </span>
                </a>
            @endif

            {{-- Deal Rooms (mobile) --}}
            @if($companyVerified)
                <a href="{{ route('deal-rooms.index') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('deal-rooms.index') }}">
                    Deal Rooms
                    @if($dealUnreadTotal > 0)
                        <span class="ms-2 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-indigo-600 text-white text-xs">
                            {{ $dealUnreadDisplay }}
                        </span>
                    @endif
                </a>
            @else
                <a href="{{ $lockedHref() }}"
                   class="block px-3 py-2 rounded-lg font-semibold text-slate-400 hover:text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="4.75" y="10" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span>Deal Rooms</span>
                    </span>
                </a>
            @endif

            @if(Route::has('employer.openings'))
                <a href="{{ route('employer.openings') }}"
                   class="block px-3 py-2 rounded-lg font-semibold {{ $isActive(['employer.openings','employer.openings.*']) }}">
                    {{ $hiringLabel }}
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
                    Jobs Board
                </a>
            @endif
        </div>

        {{-- Mobile: user/teams --}}
        <div class="pt-4 pb-4 border-t border-[var(--border)]">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover"
                             src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                    </div>
                @endif
                <div>
                    <div class="font-medium text-base text-slate-800">{{ $user->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ $user->email }}</div>
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

                {{-- Company / Team (mobile) --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-[var(--border)] my-2"></div>
                    <div class="block px-3 py-2 text-xs text-gray-400">Company</div>
                    @if($team)
                        <a href="{{ route('companies.show', $team) }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('companies.show') }}">
                            View Company
                        </a>
                        <a href="{{ route('companies.profile.edit', $team) }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('companies.profile.edit') }}">
                            Edit Company
                        </a>
                        <a href="{{ route('companies.intent.edit', $team) }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('companies.intent.edit') }}">
                            Partner Preferences
                        </a>

                        @if(!$companyVerified)
                            <div class="px-3 py-2 text-xs {{ $needsBasics ? 'text-amber-700' : 'text-blue-700' }}">
                                {{ $needsBasics
                                    ? 'Complete your basics to submit for verification.'
                                    : 'Pending manual review (usually within one business day).'
                                }}
                                <a href="{{ $lockedHref() }}" class="underline">View status</a>
                            </div>
                        @elseif($profileIncomplete)
                            <div class="px-3 py-2 text-xs text-amber-600">
                                Complete your profile to appear higher in search.
                            </div>
                        @endif
                    @endif

                    <div class="border-t border-[var(--border)] my-2"></div>
                    <div class="block px-3 py-2 text-xs text-gray-400">Team</div>
                    @if($team)
                        <a href="{{ route('teams.show', $team->id) }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('teams.show') }}">
                            Team Settings
                        </a>
                    @endif
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <a href="{{ route('teams.create') }}"
                           class="block px-3 py-2 rounded-lg font-semibold {{ $isActive('teams.create') }}">
                            Create New Team
                        </a>
                    @endcan
                    @if ($user && $user->allTeams()->count() > 1)
                        <div class="border-t border-[var(--border)] my-2"></div>
                        <div class="block px-3 py-2 text-xs text-gray-400">Switch Company</div>
                        @foreach ($user->allTeams() as $teamOption)
                            <x-switchable-team :team="$teamOption" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
