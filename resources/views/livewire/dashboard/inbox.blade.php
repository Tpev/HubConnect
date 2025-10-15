<x-ts-card class="relative ring-brand overflow-hidden">
    {{-- Emerald top rule --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
    <x-slot name="header" class="font-semibold">Inbox & Requests</x-slot>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- RECEIVED --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-slate-700">Pending introductions (Received)</div>
                <button
                    type="button"
                    x-data
                    x-on:click="Livewire.dispatch('toggle-connections')"
                    class="text-[11px] px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100"
                    aria-label="View connections panel"
                >
                    View
                </button>
            </div>

            @if(count($pendingReceived))
                <ul class="space-y-2">
                    @foreach($pendingReceived as $r)
                        @php $from = $this->companyMeta($r['from_company_id'] ?? null); @endphp
                        <li class="rounded-lg ring-1 ring-slate-200 bg-white px-3 py-2 text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($from['photo'])
                                    <img src="{{ $from['photo'] }}" class="h-6 w-6 rounded-md object-cover ring-1 ring-slate-200" alt="{{ $from['name'] }}">
                                @else
                                    <div class="h-6 w-6 rounded-md bg-slate-200 grid place-items-center text-[10px] font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ \Illuminate\Support\Str::of($from['name'])->substr(0,1) }}
                                    </div>
                                @endif
                                <div class="truncate">
                                    <span class="text-slate-800 font-medium truncate">From:</span>
                                    <span class="truncate">{{ $from['name'] }}</span>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 shrink-0">
                                {{ \Illuminate\Support\Carbon::parse($r['created_at'])->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No pending requests.</p>
            @endif
        </div>

        {{-- SENT --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-slate-700">Pending requests (Sent)</div>
                <button
                    type="button"
                    x-data
                    x-on:click="Livewire.dispatch('toggle-connections')"
                    class="text-[11px] px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100"
                    aria-label="View connections panel"
                >
                    View
                </button>
            </div>

            @if(count($pendingSent))
                <ul class="space-y-2">
                    @foreach($pendingSent as $r)
                        @php $to = $this->companyMeta($r['to_company_id'] ?? null); @endphp
                        <li class="rounded-lg ring-1 ring-slate-200 bg-white px-3 py-2 text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($to['photo'])
                                    <img src="{{ $to['photo'] }}" class="h-6 w-6 rounded-md object-cover ring-1 ring-slate-200" alt="{{ $to['name'] }}">
                                @else
                                    <div class="h-6 w-6 rounded-md bg-slate-200 grid place-items-center text-[10px] font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ \Illuminate\Support\Str::of($to['name'])->substr(0,1) }}
                                    </div>
                                @endif
                                <div class="truncate">
                                    <span class="text-slate-800 font-medium truncate">To:</span>
                                    <span class="truncate">{{ $to['name'] }}</span>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 shrink-0">
                                {{ \Illuminate\Support\Carbon::parse($r['created_at'])->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No pending requests sent.</p>
            @endif
        </div>

        {{-- RECENT CONNECTIONS --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-slate-700">Recent connections</div>
                <button
                    type="button"
                    x-data
                    x-on:click="Livewire.dispatch('toggle-connections')"
                    class="text-[11px] px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100"
                    aria-label="View connections panel"
                >
                    View
                </button>
            </div>

            @if(count($recentConnections))
                <ul class="space-y-2">
                    @foreach($recentConnections as $c)
                        @php
                            $a = $this->companyMeta($c['company_a_id'] ?? null);
                            $b = $this->companyMeta($c['company_b_id'] ?? null);
                        @endphp
                        <li class="rounded-lg ring-1 ring-slate-200 bg-white px-3 py-2 text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="flex -space-x-2">
                                    <div class="h-6 w-6 rounded-md ring-2 ring-white overflow-hidden bg-slate-200">
                                        @if($a['photo'])
                                            <img src="{{ $a['photo'] }}" class="h-6 w-6 object-cover" alt="{{ $a['name'] }}">
                                        @else
                                            <div class="h-6 w-6 grid place-items-center text-[10px] font-medium text-slate-600">
                                                {{ \Illuminate\Support\Str::of($a['name'])->substr(0,1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="h-6 w-6 rounded-md ring-2 ring-white overflow-hidden bg-slate-200">
                                        @if($b['photo'])
                                            <img src="{{ $b['photo'] }}" class="h-6 w-6 object-cover" alt="{{ $b['name'] }}">
                                        @else
                                            <div class="h-6 w-6 grid place-items-center text-[10px] font-medium text-slate-600">
                                                {{ \Illuminate\Support\Str::of($b['name'])->substr(0,1) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="truncate">
                                    <div class="text-slate-800 font-medium truncate">{{ $a['name'] }} <span class="text-slate-400">×</span> {{ $b['name'] }}</div>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 shrink-0">
                                {{ \Illuminate\Support\Carbon::parse($c['created_at'])->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No recent connections yet.</p>
            @endif
        </div>
    </div>
</x-ts-card>
