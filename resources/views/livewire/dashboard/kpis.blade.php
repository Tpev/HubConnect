<x-ts-card class="relative ring-brand overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
    <x-slot name="header" class="font-semibold">Key stats</x-slot>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl ring-1 ring-slate-200 bg-white p-3">
            <div class="text-[11px] text-slate-500">Pending requests</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($pendingRequests) }}</div>
        </div>
        <div class="rounded-xl ring-1 ring-slate-200 bg-white p-3">
            <div class="text-[11px] text-slate-500">Connections</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($connections) }}</div>
        </div>
        <div class="rounded-xl ring-1 ring-slate-200 bg-white p-3">
            <div class="text-[11px] text-slate-500">Deal rooms</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($dealRooms) }}</div>
        </div>
        <div class="rounded-xl ring-1 ring-slate-200 bg-white p-3">
            <div class="text-[11px] text-slate-500">Active intents</div>
            <div class="mt-1 text-xl font-semibold">{{ number_format($intentsActive) }}</div>
        </div>
    </div>
</x-ts-card>
