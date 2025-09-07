<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Recruitment — Openings</h1>
            <p class="text-sm text-slate-500">Create and manage public openings. Review applicants and send roleplay evaluations.</p>
        </div>

        <div class="flex items-center gap-3">
            <x-ts-button
                as="a"
                href="{{ route('employer.openings.create') }}"
                class="btn-accent"
            >
                New opening
            </x-ts-button>
        </div>
    </div>

    {{-- Filters / Toolbar --}}
    <x-ts-card class="p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:max-w-3xl">
                <div>
                    <x-ts-input
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search titles, description, compensation…"
                        class="w-full"
                    />
                </div>

                <div>
                    <x-ts-select.styled wire:model.live="status" class="w-full">
                        <option value="all">All statuses</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </x-ts-select.styled>
                </div>

                <div class="flex items-center gap-2">
                    <x-ts-select.styled wire:model.live="sort" class="w-full">
                        <option value="newest">Sort: Newest</option>
                        <option value="title">Sort: Title (A→Z)</option>
                        <option value="visibility">Sort: Visibility until</option>
                    </x-ts-select.styled>

                    <x-ts-select.styled wire:model.live="perPage" class="w-28">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </x-ts-select.styled>
                </div>
            </div>

            <div class="flex gap-2">
                <x-ts-button wire:click="$refresh" class="outline">Refresh</x-ts-button>
            </div>
        </div>
    </x-ts-card>

    {{-- Table --}}
    <x-ts-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Visibility</th>
                        <th class="px-4 py-3">Applications</th>
                        <th class="px-4 py-3">Roleplay</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($openings as $opening)
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">
                                    <a href="{{ route('employer.openings.edit', $opening) }}" class="hover:underline">
                                        {{ $opening->title }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ ucfirst($opening->company_type) }} •
                                    {{ \Illuminate\Support\Str::limit(strip_tags($opening->description), 90) }}
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach(($opening->specialty_ids ?? []) as $spec)
                                        <x-ts-badge class="badge-brand">{{ $spec }}</x-ts-badge>
                                    @endforeach
                                    @foreach(($opening->territory_ids ?? []) as $terr)
                                        <x-ts-badge class="badge-accent">{{ $terr }}</x-ts-badge>
                                    @endforeach
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $map = [
                                        'draft' => 'badge',
                                        'published' => 'badge-success',
                                        'archived' => 'badge-warning',
                                    ];
                                @endphp
                                <x-ts-badge class="{{ $map[$opening->status] ?? 'badge' }}">
                                    {{ ucfirst($opening->status) }}
                                </x-ts-badge>
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($opening->visibility_until)
                                    {{ \Carbon\Carbon::parse($opening->visibility_until)->format('Y-m-d') }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <x-ts-badge class="badge">{{ $opening->applications()->count() }}</x-ts-badge>
                                    <a href="{{ route('employer.openings.applications', $opening) }}" class="text-emerald-700 hover:underline text-sm">
                                        View
                                    </a>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if($opening->roleplay_policy === 'required')
                                    <x-ts-badge class="badge-success">Required</x-ts-badge>
                                @elseif($opening->roleplay_policy === 'optional')
                                    <x-ts-badge class="badge-accent">Optional</x-ts-badge>
                                @else
                                    <x-ts-badge class="badge">Disabled</x-ts-badge>
                                @endif
                                @if($opening->roleplay_pass_threshold)
                                    <div class="text-xs text-slate-500 mt-1">
                                        Pass ≥ {{ number_format($opening->roleplay_pass_threshold, 2) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if($opening->status !== 'published')
                                        <x-ts-button
                                            wire:click="publish({{ $opening->id }})"
                                            size="sm"
                                            class="btn-success"
                                        >
                                            Publish
                                        </x-ts-button>
                                    @endif

                                    @if($opening->status !== 'archived')
                                        <x-ts-button
                                            wire:click="archive({{ $opening->id }})"
                                            size="sm"
                                            class="outline"
                                        >
                                            Archive
                                        </x-ts-button>
                                    @endif

                                    <x-ts-button
                                        as="a"
                                        href="{{ route('employer.openings.edit', $opening) }}"
                                        size="sm"
                                        class="outline"
                                    >
                                        Edit
                                    </x-ts-button>

                                    <x-ts-button
                                        wire:click="duplicate({{ $opening->id }})"
                                        size="sm"
                                        class="outline"
                                    >
                                        Duplicate
                                    </x-ts-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <div class="text-center text-slate-500">
                                    <div class="text-base font-medium">No openings yet</div>
                                    <div class="text-sm">Create your first opening to start receiving applications.</div>
                                    <div class="mt-4">
                                        <x-ts-button
                                            as="a"
                                            href="{{ route('employer.openings.create') }}"
                                            class="btn-accent"
                                        >
                                            New opening
                                        </x-ts-button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $openings->links() }}
        </div>
    </x-ts-card>
</div>
