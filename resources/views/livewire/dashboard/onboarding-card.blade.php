<x-ts-card class="relative ring-brand overflow-hidden">
    {{-- Emerald top rule --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="font-semibold text-lg">
                Welcome back 👋
            </div>
            @if($kycStatus)
                <span class="text-xs px-2 py-0.5 rounded-full ring-1 
                    {{ $kycStatus === 'verified' ? 'ring-emerald-200 bg-emerald-50 text-emerald-700' : 'ring-amber-200 bg-amber-50 text-amber-700' }}">
                    KYC: {{ ucfirst($kycStatus) }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Completion bar --}}
        <div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-700 font-medium">Profile completion</span>
                <span class="text-slate-500">{{ $completion }}%</span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-emerald-500 transition-all" style="width: {{ $completion }}%"></div>
            </div>
        </div>

        {{-- Steps (only show incomplete ones) --}}
        @if(count($steps))
            <ul class="grid gap-2 sm:grid-cols-2">
                @foreach($steps as $s)
                    <li class="flex items-center justify-between gap-2 rounded-lg ring-1 ring-slate-200 bg-white px-3 py-2">
                        <span class="text-sm text-slate-700">
                            {{ $s['label'] }}
                        </span>

                        @if($s['url'])
                            <a href="{{ $s['url'] }}"
                               class="text-xs rounded-md px-2 py-1 ring-1 transition
                                      ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
                                Complete
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400">Unavailable</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            {{-- All done state --}}
            <div class="rounded-lg ring-1 ring-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                🎉 Your onboarding is complete. Keep things fresh by updating your profile and “what you’re looking for”.
            </div>

            <div class="flex flex-wrap gap-2">
                @if($company && app('router')->has('companies.profile.edit'))
                    <a href="{{ route('companies.profile.edit', $company) }}"
                       class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        Edit company profile
                    </a>
                @endif
                @if($company && app('router')->has('companies.intent.edit'))
                    <a href="{{ route('companies.intent.edit', $company) }}"
                       class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700">
                        Update what we’re looking for
                    </a>
                @endif
            </div>
        @endif
    </div>
</x-ts-card>
