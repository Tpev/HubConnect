@php
    $statusClass = fn(string $status) => match($status) {
        'new'         => 'inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs',
        'shortlisted' => 'inline-flex items-center px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-xs',
        'rejected'    => 'inline-flex items-center px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-xs',
        'hired'       => 'inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs',
        default       => 'inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs',
    };

    $verdictBadge = function (?string $v) {
        return match($v) {
            'hard_block' => 'bg-rose-100 text-rose-700',
            'soft_block' => 'bg-amber-100 text-amber-700',
            'pass'       => 'bg-emerald-100 text-emerald-700',
            default      => 'bg-slate-100 text-slate-600',
        };
    };

    $verdictLabel = fn (?string $v) => match($v) {
        'hard_block' => 'Hard fail',
        'soft_block' => 'Soft fail',
        'pass'       => 'Pass',
        default      => '—',
    };
@endphp

<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl font-semibold tracking-tight truncate">Applicants — {{ $opening->title }}</h1>
            <p class="text-sm text-slate-500">Manage applications, statuses, screening, and invites.</p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('employer.openings') }}" class="text-sm text-[var(--brand-700)] hover:underline">
                ← Back to openings
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <x-ts-card class="p-4 ring-brand">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5">
                <x-ts-input
                    label="Search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search name, email, phone, location…"
                    leading-icon="search"
                    class="w-full"
                />
            </div>

            <div class="md:col-span-3">
                <x-ts-select.styled
                    wire:model.live="status"
                    label="Status"
                    :options="$statusOptions"
                    select="label:label|value:value"
                    :clearable="false"
                    class="w-full"
                />
            </div>

            <div class="md:col-span-2">
                <x-ts-select.styled
                    wire:model.live="sort"
                    label="Sort"
                    :options="$sortOptions"
                    select="label:label|value:value"
                    :clearable="false"
                    class="w-full"
                />
            </div>

            <div class="md:col-span-2">
                <x-ts-select.styled
                    wire:model.live="perPage"
                    label="Per page"
                    :options="$perPageOptions"
                    select="label:label|value:value"
                    :clearable="false"
                    class="w-full"
                />
            </div>

            <div class="md:col-span-12 flex justify-end">
                <x-ts-button wire:click="$refresh" class="btn-brand outline">Refresh</x-ts-button>
            </div>
        </div>
    </x-ts-card>

    {{-- Desktop table --}}
    <x-ts-card class="p-0 overflow-hidden ring-brand hidden md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed divide-y divide-slate-200">
                <colgroup>
                    <col style="width: 18rem;">   {{-- Candidate --}}
                    <col style="width: 16rem;">   {{-- Contact --}}
                    <col style="width: 8rem;">    {{-- Status --}}
                    <col style="width: 12rem;">   {{-- Screening --}}
                    <col style="width: 7rem;">    {{-- Score --}}
                    <col style="width: 10rem;">   {{-- Roleplay --}}
                    <col style="width: 22rem;">   {{-- Actions --}}
                </colgroup>

                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-4 py-3">Candidate</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Screening</th>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Roleplay</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($applications as $app)
                    @php
                        $sc = $app->computed_screening ?? null;
                        $v  = $sc['verdict'] ?? $app->screening_verdict ?? 'pass';
                        $fc = (int) ($sc['fail_count'] ?? $app->screening_fail_count ?? 0);
                        $fl = (int) ($sc['flag_count'] ?? $app->screening_flag_count ?? 0);
                        $synced = (bool) ($sc['synced'] ?? true);

                        $rp = $app->completed_at
                                ? ('Done' . (!is_null($app->roleplay_score) ? ' (' . number_format($app->roleplay_score, 0) . ')' : ''))
                                : ($app->invite_token
                                    ? ('Invited ' . ($app->invited_at?->shortAbsoluteDiffForHumans() ?? $app->invited_at?->diffForHumans()))
                                    : '—');
                    @endphp

                    <tr wire:key="app-{{ $app->id }}">
                        {{-- Candidate --}}
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <div class="font-medium text-slate-900 truncate" title="{{ $app->candidate_name }}">
                                    {{ $app->candidate_name }}
                                </div>
                                <div class="text-xs text-slate-500 truncate">
                                    Applied {{ $app->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="px-4 py-3 text-sm">
                            <div class="min-w-0 space-y-0.5">
                                <div class="truncate" title="{{ $app->email }}">{{ $app->email }}</div>
                                @if($app->phone)
                                    <div class="text-slate-500 truncate" title="{{ $app->phone }}">{{ $app->phone }}</div>
                                @endif
                                @if($app->location)
                                    <div class="text-slate-500 truncate" title="{{ $app->location }}">{{ $app->location }}</div>
                                @endif
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="{{ $statusClass($app->status) }} whitespace-nowrap">{{ ucfirst($app->status) }}</span>
                        </td>

                        {{-- Screening --}}
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-xs px-2 py-0.5 rounded {{ $verdictBadge($v) }}">
                                    {{ $verdictLabel($v) }}
                                </span>
                                <span class="text-xs text-slate-500">F{{ $fc }} • L{{ $fl }}</span>
                                @unless($synced)
                                    <span class="text-[10px] px-1 py-0.5 rounded bg-sky-100 text-sky-700" title="Updated to current rules">sync</span>
                                @endunless
                            </div>
                        </td>

                        {{-- Score --}}
                        <td class="px-4 py-3 text-sm">
                            <div class="text-right">
                                @if(!is_null($app->score))
                                    <span class="font-medium">{{ number_format($app->score, 0) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Roleplay --}}
                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <span class="text-slate-700">{{ $rp }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                {{-- Primary: View (separated visually) --}}
                                <x-ts-button
                                    size="sm"
                                    class="btn-brand !py-1.5 !px-3 text-xs"
                                    wire:click="openDrawer({{ $app->id }})"
                                    title="View details"
                                >View</x-ts-button>

                                {{-- Divider to separate View from the rest --}}
                                <span class="mx-1 h-6 w-px bg-slate-200 inline-block"></span>

                                {{-- Secondary actions --}}
                                @if($app->status !== 'shortlisted')
                                    <x-ts-button
                                        size="sm"
                                        class="btn-brand outline !py-1.5 !px-2.5 text-xs"
                                        wire:click="shortlist({{ $app->id }})"
                                        title="Mark as shortlisted"
                                    >Shortlist</x-ts-button>
                                @endif

                                @if($app->status !== 'rejected')
                                    <x-ts-button
                                        size="sm"
                                        class="btn-accent outline !py-1.5 !px-2.5 text-xs"
                                        wire:click="reject({{ $app->id }})"
                                        title="Reject"
                                    >Reject</x-ts-button>
                                @endif

                                @if($app->status !== 'hired')
                                    <x-ts-button
                                        size="sm"
                                        class="btn-brand outline !py-1.5 !px-2.5 text-xs"
                                        wire:click="hire({{ $app->id }})"
                                        title="Mark as hired"
                                    >Hire</x-ts-button>
                                @endif

                                @if(!$app->invite_token)
                                    <x-ts-button
                                        size="sm"
                                        class="btn-accent outline !py-1.5 !px-2.5 text-xs"
                                        wire:click="sendInvite({{ $app->id }})"
                                        title="Invite to roleplay"
                                    >Invite</x-ts-button>
                                @else
                                    <x-ts-button
                                        size="sm"
                                        class="btn-muted outline !py-1.5 !px-2.5 text-xs"
                                        wire:click="regenerateInvite({{ $app->id }})"
                                        title="Regenerate invite"
                                    >Reinvite</x-ts-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10">
                            <div class="text-center text-slate-500">
                                <div class="text-base font-medium">No applicants yet</div>
                                <div class="text-sm">Share the public opening page to start receiving applications.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200">
            {{ $applications->onEachSide(1)->links() }}
        </div>
    </x-ts-card>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($applications as $app)
            @php
                $sc = $app->computed_screening ?? null;
                $v  = $sc['verdict'] ?? $app->screening_verdict ?? 'pass';
                $fc = (int) ($sc['fail_count'] ?? $app->screening_fail_count ?? 0);
                $fl = (int) ($sc['flag_count'] ?? $app->screening_flag_count ?? 0);

                $rp = $app->completed_at
                        ? ('Done' . (!is_null($app->roleplay_score) ? ' (' . number_format($app->roleplay_score, 0) . ')' : ''))
                        : ($app->invite_token
                            ? ('Invited ' . ($app->invited_at?->shortAbsoluteDiffForHumans() ?? $app->invited_at?->diffForHumans()))
                            : '—');
            @endphp

            <x-ts-card class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-medium text-[var(--ink)] truncate">{{ $app->candidate_name }}</div>
                        <div class="text-xs text-slate-500">Applied {{ $app->created_at?->diffForHumans() }}</div>
                    </div>
                    <span class="{{ $statusClass($app->status) }}">{{ ucfirst($app->status) }}</span>
                </div>

                <div class="mt-3 text-sm space-y-1.5">
                    <div class="truncate">{{ $app->email }}</div>
                    @if($app->phone)
                        <div class="text-slate-500 truncate">{{ $app->phone }}</div>
                    @endif
                    @if($app->location)
                        <div class="text-slate-500 truncate">{{ $app->location }}</div>
                    @endif
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2 py-0.5 rounded {{ $verdictBadge($v) }}">{{ $verdictLabel($v) }}</span>
                    <span class="text-slate-500">F{{ $fc }} • L{{ $fl }}</span>
                    <span class="text-slate-500">Score:
                        @if(!is_null($app->score)) {{ number_format($app->score,0) }} @else — @endif
                    </span>
                    <span class="text-slate-500">Roleplay: {{ $rp }}</span>
                </div>

                {{-- Actions: View is primary and full-width; others stacked below --}}
                <div class="mt-4 space-y-2">
                    <x-ts-button
                        class="btn-brand w-full"
                        wire:click="openDrawer({{ $app->id }})"
                    >View</x-ts-button>

                    <div class="grid grid-cols-2 gap-2">
                        @if($app->status !== 'shortlisted')
                            <x-ts-button class="btn-brand outline w-full" wire:click="shortlist({{ $app->id }})">Shortlist</x-ts-button>
                        @endif
                        @if($app->status !== 'rejected')
                            <x-ts-button class="btn-accent outline w-full" wire:click="reject({{ $app->id }})">Reject</x-ts-button>
                        @endif
                        @if($app->status !== 'hired')
                            <x-ts-button class="btn-brand outline w-full" wire:click="hire({{ $app->id }})">Hire</x-ts-button>
                        @endif
                        @if(!$app->invite_token)
                            <x-ts-button class="btn-accent outline w-full" wire:click="sendInvite({{ $app->id }})">Invite</x-ts-button>
                        @else
                            <x-ts-button class="btn-muted outline w-full" wire:click="regenerateInvite({{ $app->id }})">Reinvite</x-ts-button>
                        @endif
                    </div>
                </div>
            </x-ts-card>
        @empty
            <x-ts-card class="p-6 text-center text-slate-500">
                <div class="text-base font-medium">No applicants yet</div>
                <div class="text-sm">Share the public opening page to start receiving applications.</div>
            </x-ts-card>
        @endforelse

        <div>
            {{ $applications->onEachSide(1)->links() }}
        </div>
    </div>

    {{-- Drawer --}}
    @if($selectedId)
        <livewire:recruitment.applicant-drawer
            :application-id="$selectedId"
            :opening-id="$opening->id"
            wire:key="drawer-{{ $selectedId }}" />
    @endif
</div>
