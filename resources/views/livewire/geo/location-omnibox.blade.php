<div class="space-y-2" x-data>
    <label class="block text-sm font-medium text-slate-700">
        Location (Country, State/Province, or City)
    </label>

    <div class="relative">
        <input
            type="text"
            class="w-full rounded-lg border-slate-300 focus:ring-2 focus:ring-indigo-500"
            placeholder="Type: Paris, US-CA, France, Los Angeles…"
            wire:model.live.debounce.300ms="query"
            autocomplete="off"
        />

        @php
            $chipsSafe = $chips ?? [];
            $hasAny =
                !empty($suggestions['countries'] ?? []) ||
                !empty($suggestions['states'] ?? []) ||
                !empty($suggestions['cities'] ?? []) ||
                !empty($suggestions['provider']['countries'] ?? []) ||
                !empty($suggestions['provider']['states'] ?? []) ||
                !empty($suggestions['provider']['cities'] ?? []);
        @endphp

        @if($query && $hasAny)
            <div class="absolute z-30 mt-1 w-full rounded-lg border bg-white shadow max-h-80 overflow-auto">
                {{-- Local buckets --}}
                @foreach (['cities' => 'Cities', 'states' => 'States/Provinces', 'countries' => 'Countries'] as $bucket => $label)
                    @if(!empty($suggestions[$bucket] ?? []))
                        <div class="px-3 py-1 text-xs font-semibold text-slate-500">{{ $label }} (Local)</div>
                        @foreach(($suggestions[$bucket] ?? []) as $item)
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 hover:bg-slate-50"
                                wire:click="pick(@js($item))"
                            >
                                {{ $item['label'] }}
                                <span class="ml-2 text-[11px] text-slate-400 uppercase">{{ $item['type'] }}</span>
                            </button>
                        @endforeach
                    @endif
                @endforeach

                {{-- Google provider buckets --}}
                @foreach (['cities' => 'Cities', 'states' => 'States/Provinces', 'countries' => 'Countries'] as $bucket => $label)
                    @if(!empty($suggestions['provider'][$bucket] ?? []))
                        <div class="px-3 py-1 text-[11px] font-semibold text-slate-400">{{ $label }} (Google)</div>
                        @foreach(($suggestions['provider'][$bucket] ?? []) as $item)
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 hover:bg-slate-50"
                                wire:click="pick(@js($item))"
                            >
                                {{ $item['label'] }}
                                <span class="ml-2 text-[11px] text-slate-400 uppercase">{{ $item['type'] }}</span>
                            </button>
                        @endforeach
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Chips --}}
    @if(!empty($chipsSafe))
        <div class="flex flex-wrap gap-2 pt-1">
            @foreach($chipsSafe as $i => $chip)
                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm">
                    {{ $chip['label'] }}
                    <button
                        type="button"
                        class="text-slate-500 hover:text-slate-900"
                        wire:click="removeChip({{ $i }})"
                        aria-label="Remove {{ $chip['label'] }}"
                    >×</button>
                </span>
            @endforeach
        </div>
    @endif

    <p class="text-xs text-slate-500">
        Tip: you can mix levels (e.g., France + US-CA + Paris).
    </p>
</div>
