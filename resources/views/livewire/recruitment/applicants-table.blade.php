@php
    $statusClass = fn(string $status) => match($status) {
        'new'         => 'badge-brand',
        'shortlisted' => 'badge-accent',
        'rejected'    => 'badge-accent',
        'hired'       => 'badge-brand',
        default       => 'badge-brand',
    };
@endphp

<div> {{-- SINGLE ROOT WRAPPER --}}

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Applicants — {{ $opening->title }}</h1>
                <p class="text-sm text-slate-500">Manage applications, statuses, and roleplay invites.</p>
            </div>

            <div>
                <a href="{{ route('employer.openings') }}" class="text-sm text-[var(--brand-700)] hover:underline">← Back to openings</a>
            </div>
        </div>

        {{-- Filter bar (aligned) --}}
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

        <x-ts-card class="p-0 overflow-hidden ring-brand">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="px-4 py-3">Candidate</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Score</th>
                            <th class="px-4 py-3">Roleplay</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($applications as $app)
                            <tr wire:key="app-{{ $app->id }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $app->candidate_name }}</div>
                                    <div class="text-xs text-slate-500">Applied {{ $app->created_at?->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div>{{ $app->email }}</div>
                                    @if($app->phone)
                                        <div class="text-slate-500">{{ $app->phone }}</div>
                                    @endif
                                    @if($app->location)
                                        <div class="text-slate-500">{{ $app->location }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="{{ $statusClass($app->status) }}">{{ ucfirst($app->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if(!is_null($app->score))
                                        <span class="font-medium">{{ number_format($app->score, 2) }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($app->completed_at)
                                        <div class="text-[var(--brand-700)] font-medium">Completed</div>
                                        @if(!is_null($app->roleplay_score))
                                            <div class="text-xs text-slate-500">Score: {{ number_format($app->roleplay_score, 2) }}</div>
                                        @endif
                                    @elseif($app->invite_token)
                                        <div>Invited {{ $app->invited_at?->diffForHumans() }}</div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-ts-button size="sm" class="btn-brand outline" wire:click="openDrawer({{ $app->id }})">
                                            View
                                        </x-ts-button>

                                        @if($app->status !== 'shortlisted')
                                            <x-ts-button size="sm" class="btn-brand outline" wire:click="shortlist({{ $app->id }})">
                                                Shortlist
                                            </x-ts-button>
                                        @endif

                                        @if($app->status !== 'rejected')
                                            <x-ts-button size="sm" class="btn-accent outline" wire:click="reject({{ $app->id }})">
                                                Reject
                                            </x-ts-button>
                                        @endif

                                        @if($app->status !== 'hired')
                                            <x-ts-button size="sm" class="btn-brand" wire:click="hire({{ $app->id }})">
                                                Hire
                                            </x-ts-button>
                                        @endif

                                        @if(!$app->invite_token)
                                            <x-ts-button size="sm" class="btn-accent" wire:click="sendInvite({{ $app->id }})">
                                                Invite to roleplay
                                            </x-ts-button>
                                        @else
                                            <x-ts-button size="sm" class="btn-brand outline" wire:click="regenerateInvite({{ $app->id }})">
                                                Regenerate invite
                                            </x-ts-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10">
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

        {{-- Drawer / Modal component --}}
        @if($selectedId)
            <livewire:recruitment.applicant-drawer
                :application-id="$selectedId"
                :opening-id="$opening->id"
                wire:key="drawer-{{ $selectedId }}" />
        @endif
    </div>

</div> {{-- /SINGLE ROOT WRAPPER --}}
