<div class="space-y-4">
  <h3 class="font-semibold">Regulatory</h3>

  <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">
    <div class="grid gap-4 md:grid-cols-3">
      <x-ts-select.native
        label="Clearance Type"
        wire:model="clearance_type"
        :options="[
          ['label'=>'Exempt','value'=>'exempt'],
          ['label'=>'510(k)','value'=>'510k'],
          ['label'=>'PMA','value'=>'pma'],
        ]"
      />

      <x-ts-input label="Number" placeholder="e.g., K123456" wire:model.defer="number" />
      <x-ts-date label="Issue Date" wire:model.defer="issue_date" />

      <x-ts-input class="md:col-span-3" label="Public link" placeholder="https://..." wire:model.defer="link" />
    </div>

    <div class="flex justify-end">
      <x-ts-button wire:click="save">Save</x-ts-button>
    </div>

@if($clearance)
  <x-ts-card class="mt-3">
    <div class="p-4 grid gap-2 md:grid-cols-4 text-sm">
      <div><span class="text-slate-500">Type:</span> <span class="font-medium">{{ strtoupper($clearance->clearance_type) }}</span></div>
      <div><span class="text-slate-500">Number:</span> <span class="font-medium">{{ $clearance->number ?: '—' }}</span></div>
      <div><span class="text-slate-500">Issue date:</span> <span class="font-medium">{{ $clearance->issue_date ? \Illuminate\Support\Carbon::parse($clearance->issue_date)->toFormattedDateString() : '—' }}</span></div>
      <div>
        <span class="text-slate-500">Link:</span>
        @if($clearance->link)
          <a class="font-medium underline" href="{{ $clearance->link }}" target="_blank" rel="noopener">Open</a>
        @else
          <span class="font-medium">—</span>
        @endif
      </div>
    </div>
  </x-ts-card>
@endif

  </div>
</div>
