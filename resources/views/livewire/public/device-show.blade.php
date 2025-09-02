<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $device->name }}</h1>
            <p class="text-sm text-slate-500">by {{ $device->company?->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <livewire:matchmaking.request-match :deviceId="$device->id" :wire:key="'req-'.$device->id" />
        </div>
    </div>

    <x-ts-card class="p-4 space-y-3">
        <div class="text-slate-700">{{ $device->description }}</div>
        @if($device->commission_percent)
            <div class="text-sm">
                <span class="font-medium">Commission:</span>
                {{ rtrim(rtrim(number_format($device->commission_percent,2), '0'), '.') }}%
                <span class="text-slate-500">{{ $device->commission_notes }}</span>
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-4 pt-2">
            <div>
                <div class="text-sm font-medium mb-1">Specialties</div>
                <div class="flex flex-wrap gap-1">
                    @foreach($device->specialties as $sp)
                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $sp->name }}</span>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="text-sm font-medium mb-1">Open Territories</div>
                <div class="flex flex-wrap gap-1">
                    @foreach($device->territories as $t)
                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $t->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($device->clearance)
            <div class="pt-2 text-sm">
                <span class="font-medium">Regulatory:</span>
                {{ $device->clearance->clearance_type }} {{ $device->clearance->number }}
                @if($device->clearance->issue_date) — issued {{ $device->clearance->issue_date->format('Y-m-d') }} @endif
            </div>
        @endif

        @if($device->reimbursementCodes->count())
            <div class="pt-2">
                <div class="text-sm font-medium mb-1">Reimbursement Codes</div>
                <ul class="text-sm list-disc pl-5">
                    @foreach($device->reimbursementCodes as $c)
                        <li>{{ $c->system }} {{ $c->code }} — {{ $c->description }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-ts-card>
</div>
