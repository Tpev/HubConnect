<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Match Requests</h1>
        <div class="flex gap-2">
            <x-ts-button :class="$tab==='pending'?'':'ts-btn-ghost'" wire:click="$set('tab','pending')">Pending</x-ts-button>
            <x-ts-button :class="$tab==='accepted'?'':'ts-btn-ghost'" wire:click="$set('tab','accepted')">Accepted</x-ts-button>
            <x-ts-button :class="$tab==='rejected'?'':'ts-btn-ghost'" wire:click="$set('tab','rejected')">Rejected</x-ts-button>
        </div>
    </div>

    <x-ts-card class="p-0 overflow-hidden">
        <x-ts-table :headers="['Device','Distributor','Territories','Exclusivity','Proposed %','Actions']" :rows="$rows" hover>
            @interact('column_Device', $row)
                <a class="font-medium hover:underline" href="{{ route('m.devices.edit', $row->device_id) }}">{{ $row->device->name }}</a>
            @endinteract

            @interact('column_Distributor', $row)
                {{ $row->distributor?->name }}
            @endinteract

            @interact('column_Territories', $row)
                @php $tids = collect($row->requested_territory_ids ?? []); @endphp
                @if($tids->isEmpty())
                    <span class="text-slate-400 text-sm">—</span>
                @else
                    <div class="flex flex-wrap gap-1">
                        @foreach(app(\App\Models\Territory::class)->whereIn('id',$tids)->get(['name']) as $t)
                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $t->name }}</span>
                        @endforeach
                    </div>
                @endif
            @endinteract

            @interact('column_Exclusivity', $row)
                {{ $row->exclusivity ? 'Yes' : 'No' }}
            @endinteract

            @interact('column_Proposed %', $row)
                {{ $row->proposed_commission_percent ? rtrim(rtrim(number_format($row->proposed_commission_percent,2),'0'),'.').'%' : '—' }}
            @endinteract

            @interact('column_Actions', $row)
                @if($row->status === 'pending')
                    <div class="flex gap-2">
                        <x-ts-button size="sm" wire:click="approve({{ $row->id }})">Accept</x-ts-button>
                        <x-ts-button size="sm" flat wire:click="reject({{ $row->id }) }}">Reject</x-ts-button>
                    </div>
                @else
                    <span class="text-sm text-slate-500 capitalize">{{ $row->status }}</span>
                @endif
            @endinteract
        </x-ts-table>

        <div class="p-4">
            {{ $rows->links() }}
        </div>
    </x-ts-card>
</div>
