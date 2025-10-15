<x-ts-card class="relative ring-brand overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
    <x-slot name="header" class="font-semibold">Recruitment</x-slot>

    <div class="flex items-center justify-between">
        <div class="text-sm text-slate-700">
            Applications received: <span class="font-medium">{{ number_format($applicationsCount) }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if(Route::has('employer.openings'))
                <a href="{{ route('employer.openings') }}"
                   class="text-xs rounded-md px-2 py-1 ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
                    Manage openings
                </a>
            @endif
            @if(Route::has('employer.openings.create'))
                <a href="{{ route('employer.openings.create') }}"
                   class="text-xs rounded-md px-2 py-1 ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700">
                   Post new job
                </a>
            @endif
        </div>
    </div>

    <div class="mt-4">
        @if(count($openings))
            <ul class="divide-y divide-slate-200">
                @foreach($openings as $o)
                    <li class="py-2 flex items-center justify-between">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">
                                {{ data_get($o, 'title', 'Untitled opening') }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{-- show location if it exists, otherwise fallback gracefully --}}
                                {{ data_get($o, 'location') ? data_get($o, 'location') . ' • ' : '' }}
                                {{ ucfirst(data_get($o, 'status', 'active')) }}
                                {{-- show created_at if present --}}
                                @if(data_get($o, 'created_at'))
                                    • {{ \Illuminate\Support\Carbon::parse(data_get($o, 'created_at'))->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        @if(Route::has('employer.openings'))
                            <a href="{{ route('employer.openings') }}"
                               class="text-xs text-emerald-700 hover:underline">View</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-sm text-slate-500">No active openings yet.</div>
        @endif
    </div>
</x-ts-card>
