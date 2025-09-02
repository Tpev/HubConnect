{{-- resources/views/livewire/public/device-index.blade.php --}}
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Discover Devices</h1>
            <p class="text-sm text-slate-500">Find manufacturers to represent. Filter by specialty &amp; territory.</p>
        </div>
    </div>

    <x-ts-card class="p-4 space-y-3">
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            {{-- Search with buttons --}}
            <div class="flex items-center gap-2 md:w-96">
                <x-ts-input
                    icon="magnifying-glass"
                    placeholder="Search devices…"
                    wire:model.defer="search"
                    class="flex-1"
                />
                <x-ts-button wire:click="applyFilters" size="sm">Search</x-ts-button>
                <x-ts-button wire:click="resetFilters" size="sm" variant="secondary">Reset</x-ts-button>
            </div>

            @php
                $specialtyOptions = $allSpecialties->map(fn($s) => ['label'=>$s->name, 'value'=>$s->id])->toArray();
                $territoryOptions = $allTerritories->map(fn($t) => ['label'=>$t->name, 'value'=>$t->id])->toArray();
                $sortOptions = [
                    ['label'=>'Newest','value'=>'newest'],
                    ['label'=>'Name','value'=>'name'],
                    ['label'=>'Highest commission','value'=>'commission'],
                ];
            @endphp

            {{-- TallStack selects (as you had them) --}}
            <x-ts-select.styled
                :options="$specialtyOptions"
                wire:model="specialtyIds"
                multiple
                searchable
                placeholder="Specialties"
                class="md:w-80"
            />

            <x-ts-select.styled
                :options="$territoryOptions"
                wire:model="territoryIds"
                multiple
                searchable
                placeholder="Open territories"
                class="md:w-80"
            />

            <x-ts-select.styled
                :options="$sortOptions"
                wire:model="sort"
                placeholder="Sort by"
                class="md:w-48"
            />
        </div>
    </x-ts-card>

    @if($rows->isEmpty())
        <x-ts-card class="p-6 text-center text-slate-500">
            No devices found. Try clearing filters or ask the manufacturer to publish the device.
        </x-ts-card>
    @endif

    @php
        // License check (Team::hasActiveLicense() preferred, else fallbacks)
        $__team = auth()->user()?->currentTeam ?? null;
        $__hasActiveLicense = false;
        if ($__team) {
            if (method_exists($__team, 'hasActiveLicense')) {
                $__hasActiveLicense = (bool) $__team->hasActiveLicense();
            } elseif (isset($__team->license_active)) {
                $__hasActiveLicense = (bool) $__team->license_active;
            } elseif (isset($__team->has_active_license)) {
                $__hasActiveLicense = (bool) $__team->has_active_license;
            } elseif (($__team->license_status ?? null) === 'active') {
                $__hasActiveLicense = true;
            }
        }
    @endphp

    <div class="relative">
        {{-- Results are present; blurred and non-interactive only when not licensed --}}
        <div @class([
                'contents' => $__hasActiveLicense,
                'pointer-events-none select-none' => ! $__hasActiveLicense,
            ])>
            <div @class([
                    '' => $__hasActiveLicense,
                    'opacity-30 blur-sm' => ! $__hasActiveLicense,
                ])>

                {{-- Equal-height cards via items-stretch + auto-rows-fr and h-full cards --}}
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch auto-rows-fr">
                    @foreach($rows as $row)
                        @php
                            $territories = $row->territories ?? collect();
                            $territoryPreview = $territories->take(4);
                            $territoryOverflow = max($territories->count() - 4, 0);

                            $specialtyPreview = $row->specialties->take(4);
                            $specialtyOverflow = max($row->specialties->count() - 4, 0);

                            $fda = strtoupper($row->fda_pathway ?? 'none');
                            $isReimb = (bool) ($row->reimbursable ?? false);
                        @endphp

                        <x-ts-card class="flex flex-col h-full p-0 overflow-hidden ring-1 ring-slate-100 hover:shadow-md transition-shadow" x-data="{ open: false }">
                            <div class="flex-1 p-4 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ route('devices.show', $row->slug) }}" class="font-semibold hover:underline line-clamp-1">
                                        {{ $row->name }}
                                    </a>

                                    @if($row->commission_percent)
                                        <span class="text-xs rounded-full px-2 py-1 bg-indigo-50 text-indigo-700">
                                            {{ rtrim(rtrim(number_format($row->commission_percent,2), '0'), '.') }}% commission
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta badges: FDA + Reimbursable --}}
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($fda && $fda !== 'NONE')
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                                            FDA: {{ $fda }}
                                        </span>
                                    @else
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-slate-50 text-slate-500">
                                            FDA: None
                                        </span>
                                    @endif

                                    @if($isReimb)
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">
                                            Reimbursable
                                        </span>
                                    @endif
                                </div>

                                {{-- Description --}}
                                <p class="text-sm text-slate-600 line-clamp-3">{{ $row->description }}</p>

                                {{-- Manufacturer --}}
                                <div class="text-xs text-slate-500">
                                    Manufacturer: {{ $row->company?->name ?? '—' }}
                                </div>

                                {{-- Specialties --}}
                                @if($specialtyPreview->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 pt-2">
                                        @foreach($specialtyPreview as $sp)
                                            <span class="text-[10px] px-2 py-1 rounded-full bg-slate-100">{{ $sp->name }}</span>
                                        @endforeach
                                        @if($specialtyOverflow > 0)
                                            <span class="text-[10px] px-2 py-1 rounded-full bg-slate-100">+{{ $specialtyOverflow }}</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Territories (US states) --}}
                                @if($territoryPreview->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 pt-2">
                                        @foreach($territoryPreview as $tt)
                                            <span class="text-[10px] px-2 py-1 rounded-full bg-teal-50 text-teal-700">
                                                {{ $tt->name }}
                                            </span>
                                        @endforeach
                                        @if($territoryOverflow > 0)
                                            <span class="text-[10px] px-2 py-1 rounded-full bg-teal-50 text-teal-700">
                                                +{{ $territoryOverflow }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Footer CTA --}}
                            <div class="border-t p-3 flex items-center justify-between bg-slate-50/50">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="open = true" class="text-xs text-slate-600 hover:text-slate-900 underline underline-offset-2">Quick view</button>
                                </div>
                                <x-ts-button as="a" href="{{ route('devices.show', $row->slug) }}" size="sm">
                                    View details
                                </x-ts-button>
                            </div>

                            {{-- Quick View Modal (teleported to body so it ignores blur/overlays) --}}
                            <template x-teleport="body">
                                <div x-cloak x-show="open" x-transition.opacity
                                     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

                                    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-5">
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <h3 class="text-lg font-semibold line-clamp-1">{{ $row->name }}</h3>
                                            <button type="button" @click="open=false" class="text-slate-500 hover:text-slate-800">✕</button>
                                        </div>

                                        <p class="text-sm text-slate-600 mb-3 line-clamp-5">{{ $row->description }}</p>

                                        <div class="space-y-2 mb-4">
                                            <div class="text-xs text-slate-500">Manufacturer: {{ $row->company?->name ?? '—' }}</div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(($row->specialties->take(6) ?? collect()) as $sp)
                                                    <span class="text-[10px] px-2 py-1 rounded-full bg-slate-100">{{ $sp->name }}</span>
                                                @endforeach
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach((($row->territories->take(8)) ?? collect()) as $tt)
                                                    <span class="text-[10px] px-2 py-1 rounded-full bg-teal-50 text-teal-700">{{ $tt->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-2 border-t">
                                            <button type="button" class="text-sm text-slate-600 hover:text-slate-900" @click="open=false">Close</button>
                                            <x-ts-button as="a" href="{{ route('devices.show', $row->slug) }}" size="sm">Go to details</x-ts-button>
                                        </div>

                                        <p class="mt-2 text-[11px] text-slate-500">
                                            You can request a connection from the device page after reviewing full details.
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </x-ts-card>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $rows->links() }}
                </div>
            </div>
        </div>


@unless($__hasActiveLicense)
    {{-- Full overlay with CTA to upgrade --}}
    <div class="absolute inset-0 z-50 flex items-center justify-center">
        <div class="pointer-events-auto w-full max-w-xl mx-auto">
            <div class="rounded-2xl border bg-white/90 backdrop-blur p-6 shadow-xl">
                <h3 class="text-xl font-semibold tracking-tight">Unlock new opportunities</h3>
                <p class="text-sm text-slate-600 mt-1">
                    Gain full access to manufacturer profiles, open territories, and commission details. Connect directly with partners and turn opportunities into deals.
                </p>

                <ul class="mt-3 grid grid-cols-1 gap-1 text-sm text-slate-700">
                    <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Direct introductions & secure messaging</li>
                    <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Full visibility on territories & coverage</li>
                    <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Commission structures at a glance</li>
                </ul>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ url('/license-tiers/pricing') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">
                        Upgrade to unlock
                    </a>

                    @guest
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-xl border px-4 py-2 font-medium hover:bg-slate-50">
                            Create an account
                        </a>
                    @endguest
                </div>

                <p class="mt-2 text-xs text-slate-500">
                    @auth
                        Already licensed on another team? <a class="underline" href="{{ url('/license-tiers/pricing') }}">Manage your plan</a>.
                    @else
                        Already have a license? <a class="underline" href="{{ route('login') }}">Sign in</a>.
                    @endauth
                </p>
            </div>
        </div>
    </div>
@endunless
    </div>
</div>
