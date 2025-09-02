<div class="space-y-4">
  <h3 class="font-semibold">Targeting</h3>

  <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-6">
    <div>
      <div class="text-sm font-semibold mb-2">Specialties</div>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
        @foreach($specialties as $s)
          <label class="inline-flex items-center gap-2 p-2 rounded-lg border border-slate-200 hover:bg-slate-50">
            <input type="checkbox" class="rounded" value="{{ $s->id }}" wire:model="selected_specialties">
            <span>{{ $s->name }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <div>
      <div class="text-sm font-semibold mb-2">Territories (US States)</div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-80 overflow-auto pr-1">
        @foreach($territories as $t)
          <label class="inline-flex items-center gap-2 p-2 rounded-lg border border-slate-200 hover:bg-slate-50">
            <input type="checkbox" class="rounded" value="{{ $t->id }}" wire:model="selected_territories">
            <span>{{ $t->name }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <div class="flex justify-end">
      <x-ts-button wire:click="save">Save</x-ts-button>
    </div>
  </div>
</div>
