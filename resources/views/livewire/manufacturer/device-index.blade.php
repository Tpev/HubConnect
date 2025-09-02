{{-- resources/views/livewire/manufacturer/device-index.blade.php --}}
<div class="space-y-5">
    @php
        $pageOptions = [
            ['label' => '10 / page', 'value' => 10],
            ['label' => '25 / page', 'value' => 25],
            ['label' => '50 / page', 'value' => 50],
        ];
    @endphp

    {{-- Toolbar --}}
    <x-ts-card class="p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold tracking-tight">Devices</h3>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ number_format($rows->total()) }} total
                </p>
            </div>

            <div class="flex flex-1 items-center gap-3 md:max-w-xl">
                <x-ts-input
                    placeholder="Search by name, category, territory…"
                    icon="magnifying-glass"
                    wire:model.live.debounce.300ms="search"
                />

                {{-- Quick status filters --}}
                <div class="hidden md:flex items-center gap-2">
                    @php $s = strtolower($status); @endphp
                    <x-ts-button :variant="$s === 'all' ? 'primary' : 'secondary'" wire:click="$set('status','all')">
                        All
                    </x-ts-button>
                    <x-ts-button :variant="$s === 'draft' ? 'primary' : 'secondary'" wire:click="$set('status','draft')">
                        Draft
                    </x-ts-button>
                    <x-ts-button :variant="$s === 'listed' ? 'primary' : 'secondary'" wire:click="$set('status','listed')">
                        Listed
                    </x-ts-button>
                    <x-ts-button :variant="$s === 'paused' ? 'primary' : 'secondary'" wire:click="$set('status','paused')">
                        Paused
                    </x-ts-button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-ts-select.styled
                    class="w-40"
                    wire:model.live="quantity"
                    :options="$pageOptions"
                    placeholder="Select page size"
                    required
                />

                <x-ts-button as="a" href="{{ route('m.devices.create') }}">
                    New Device
                </x-ts-button>
            </div>
        </div>
    </x-ts-card>

    {{-- Empty state --}}
    @if($rows->count() === 0 && blank($search))
        <x-ts-card class="p-12 text-center">
            <div class="space-y-2">
                <div class="text-xl font-semibold">No devices yet</div>
                <p class="text-slate-500">Get started by creating your first device.</p>
                <div class="pt-3">
                    <x-ts-button as="a" href="{{ route('m.devices.create') }}">
                        Create a Device
                    </x-ts-button>
                </div>
            </div>
        </x-ts-card>
    @endif

    {{-- Table --}}
    @if($rows->count() > 0)
        <x-ts-card class="p-0 overflow-hidden">
            <x-ts-table
                :headers="$headers"
                :rows="$rows"
                paginate
                striped
                hover
                id="devices-table"
            >
                {{-- NAME --}}
                @interact('column_name', $row)
                    <a class="font-semibold underline" href="{{ route('m.devices.edit', $row->id) }}">
                        {{ $row->name }}
                    </a>
                @endinteract

                {{-- CATEGORY --}}
                @interact('column_category', $row)
                    {{ optional($row->category)->name ?? '—' }}
                @endinteract

                {{-- MARGIN TARGET --}}
                @interact('column_margin_target', $row)
                    {{ isset($row->margin_target) ? number_format($row->margin_target, 1) . '%' : '—' }}
                @endinteract

                {{-- TERRITORIES --}}
                @interact('column_territories', $row)
                    {{ $row->territories?->pluck('name')->implode(', ') ?: '—' }}
                @endinteract

                {{-- STATUS (pill) --}}
                @interact('column_status', $row)
                    @php
                        $status = strtolower($row->status ?? 'draft');
                        $styles = [
                            'draft'  => 'bg-gray-200 text-gray-700',
                            'listed' => 'bg-green-200 text-green-800',
                            'paused' => 'bg-yellow-200 text-yellow-800',
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $styles[$status] ?? 'bg-gray-200 text-gray-700' }}">
                        {{ ucfirst($status) }}
                    </span>
                @endinteract

                {{-- ACTIONS --}}
                @interact('column_actions', $row)
                    <div class="flex items-center gap-2">
                        <x-ts-button as="a" size="sm" href="{{ route('m.devices.edit', $row->id) }}">
                            Edit
                        </x-ts-button>
                        <x-ts-button as="a" size="sm" variant="secondary"
                                     href="{{ route('m.devices') }}?impersonate={{ $row->id }}">
                            Preview
                        </x-ts-button>
                    </div>
                @endinteract
            </x-ts-table>
        </x-ts-card>

        {{-- MAP directly under the table, fed by THIS PAGE's territories --}}
        <x-ts-card class="p-4">
            <x-us-distributor-map :target-states="$targetStates" height="420" />
        </x-ts-card>
    @endif
</div>
