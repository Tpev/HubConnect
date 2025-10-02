<div class="max-w-5xl mx-auto space-y-6" wire:init>
    @if (session('msg'))
        <x-ts-alert color="green" class="ring-brand">{{ session('msg') }}</x-ts-alert>
    @endif

    <x-ts-card class="ring-brand">
        <x-slot name="header" class="flex items-center justify-between">
            <div class="font-semibold text-lg">Requests</div>
            <div class="flex gap-2">
                <button class="btn-brand {{ $tab==='received' ? '' : 'outline' }}" wire:click="$set('tab','received')">Received</button>
                <button class="btn-brand {{ $tab==='sent' ? '' : 'outline' }}"     wire:click="$set('tab','sent')">Sent</button>
            </div>
        </x-slot>

        {{-- Skeleton while switching tabs --}}
        <div wire:loading.delay>
            @for($i=0;$i<3;$i++)
                <div class="py-4 flex items-start justify-between animate-pulse">
                    <div class="space-y-2 w-2/3">
                        <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                        <div class="h-3 bg-slate-200 rounded w-2/3"></div>
                        <div class="h-3 bg-slate-200 rounded w-1/3"></div>
                    </div>
                    <div class="w-40 h-8 bg-slate-200 rounded"></div>
                </div>
                <div class="border-t border-slate-100"></div>
            @endfor
        </div>

        {{-- Received --}}
        @if ($tab === 'received')
            <div class="divide-y" wire:loading.remove>
                @forelse ($received as $r)
                    <div class="py-4 flex items-start justify-between">
                        <div>
                            <div class="font-medium">
                                <a href="{{ route('companies.show', $r->fromCompany) }}" class="hover:underline">
                                    {{ $r->fromCompany->name }}
                                </a>
                            </div>
                            @if($r->note)<div class="text-sm text-slate-600">{{ $r->note }}</div>@endif
                            <div class="text-xs text-slate-500">Status: {{ ucfirst($r->status) }} • {{ $r->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex gap-2">
                            @if ($r->status === 'pending')
                                <button class="btn-brand" wire:click="accept({{ $r->id }})">Accept</button>
                                <button class="btn-accent outline" wire:click="decline({{ $r->id }})">Decline</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-ts-error title="No requests received yet." />
                @endforelse
            </div>
        @else
            {{-- Sent --}}
            <div class="divide-y" wire:loading.remove>
                @forelse ($sent as $r)
                    <div class="py-4 flex items-start justify-between">
                        <div>
                            <div class="font-medium">
                                <a href="{{ route('companies.show', $r->toCompany) }}" class="hover:underline">
                                    {{ $r->toCompany->name }}
                                </a>
                            </div>
                            @if($r->note)<div class="text-sm text-slate-600">{{ $r->note }}</div>@endif
                            <div class="text-xs text-slate-500">Status: {{ ucfirst($r->status) }} • {{ $r->created_at->diffForHumans() }}</div>
                        </div>
                        <div></div>
                    </div>
                @empty
                    <x-ts-error title="No requests sent yet." />
                @endforelse
            </div>
        @endif
    </x-ts-card>
</div>
