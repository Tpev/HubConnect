<div class="space-y-4">
    <x-ts-card class="relative ring-brand overflow-hidden">
        {{-- Slim emerald top accent (matches result cards) --}}
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>

        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div class="font-semibold text-lg">Find the right partner</div>

                {{-- Reset on the right --}}
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs ring-1 ring-slate-200 bg-white hover:bg-slate-50 text-slate-700"
                >
                    Reset
                </button>
            </div>
        </x-slot>

        {{-- 3-column layout --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Column 1: Keyword --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Keyword</label>
                <div class="relative">
                    <x-ts-input
                        wire:model.debounce.300ms="q"
                        placeholder="Company name, summary, website"
                        class="w-full"
                    />
                    @if($q)
                        <button
                            type="button"
                            wire:click="$set('q', null)"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                            aria-label="Clear keyword"
                        >
                            <x-ts-icon name="x-mark" class="w-4.5 h-4.5"/>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Column 2: Roles (segmented pills) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                @php
                    $roleOptions = [
                        [null,'Any','any'],
                        ['manufacturer','Manufacturer','manufacturer'],
                        ['distributor','Distributor','distributor'],
                        ['both','Both','both'],
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1">
                    @foreach($roleOptions as [$val,$label,$key])
                        @php $active = ($role === $val) || ($val === null && $role === null); @endphp
                        <button
                            type="button"
                            wire:click="$set('role', @js($val))"
                            class="px-2.5 py-1.5 rounded-md text-[13px] border transition
                                   {{ $active
                                        ? 'border-emerald-300 bg-emerald-50 text-emerald-800'
                                        : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                        >
                            <span class="font-medium">{{ $label }}</span>
                            <span class="ms-1 text-[11px] {{ $active ? 'text-emerald-700' : 'text-slate-500' }}">
                                ({{ number_format($facet[$key] ?? 0) }})
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Column 3: Territory, Specialty, Certification (stacked) --}}
            <div class="space-y-4">
                <x-ts-select.styled
                    label="Territory"
                    wire:model="territory"
                    :options="$countries"
                    searchable
                    placeholder="Any"
                />

                <x-ts-select.styled
                    label="Specialty"
                    wire:model="specialty"
                    :options="$allSpecialties->map(fn($s)=>['label'=>$s->name,'value'=>$s->id])->toArray()"
                    searchable
                    placeholder="Any"
                />

                <x-ts-select.styled
                    label="Certification"
                    wire:model="cert"
                    :options="$allCerts->map(fn($c)=>['label'=>$c->name,'value'=>$c->id])->toArray()"
                    searchable
                    placeholder="Any"
                />
            </div>
        </div>

        {{-- Active chips --}}
        @php
            $activeChips = [];
            if(!empty($q))              $activeChips[] = ['label'=>"Keyword: “$q”", 'prop'=>'q', 'val'=>null];
            if(!empty($role))           $activeChips[] = ['label'=>"Role: ".ucfirst($role), 'prop'=>'role', 'val'=>null];
            if(!empty($territory)) {
                $map = collect($countries ?? [])->mapWithKeys(fn($c)=>[$c['value'] => $c['label']]);
                $activeChips[] = ['label'=>"Territory: ".$map->get($territory, $territory), 'prop'=>'territory', 'val'=>null];
            }
            if(!empty($specialty)) {
                $sp = $allSpecialties->firstWhere('id',$specialty)?->name ?? $specialty;
                $activeChips[] = ['label'=>"Specialty: $sp", 'prop'=>'specialty', 'val'=>null];
            }
            if(!empty($cert)) {
                $ct = $allCerts->firstWhere('id',$cert)?->name ?? $cert;
                $activeChips[] = ['label'=>"Certification: $ct", 'prop'=>'cert', 'val'=>null];
            }
        @endphp

        @if(count($activeChips))
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach($activeChips as $chip)
                    <button
                        type="button"
                        wire:click="$set('{{ $chip['prop'] }}', @js($chip['val']))"
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700 ring-1 ring-slate-200 hover:bg-slate-200 transition"
                    >
                        {{ $chip['label'] }}
                        <x-ts-icon name="x-mark" class="w-3.5 h-3.5"/>
                    </button>
                @endforeach

                <button
                    type="button"
                    wire:click="clearFilters"
                    class="ms-1 inline-flex items-center gap-1 rounded-full bg-slate-50 px-2.5 py-1 text-xs text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100 transition"
                >
                    Clear all
                    <x-ts-icon name="arrow-path" class="w-3.5 h-3.5"/>
                </button>
            </div>
        @endif

        {{-- Mobile reset (optional) --}}
        <div class="mt-4 md:hidden">
            <button
                type="button"
                wire:click="clearFilters"
                class="w-full inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm ring-1 ring-slate-200 bg-white hover:bg-slate-50 text-slate-700"
            >
                Reset filters
            </button>
        </div>
    </x-ts-card>
</div>
