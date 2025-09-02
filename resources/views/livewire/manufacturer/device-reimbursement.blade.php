<div class="space-y-4">
  <h3 class="font-semibold">Reimbursement</h3>

  <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
    <div class="grid gap-3 md:grid-cols-4">
      <x-ts-select.native
        label="Type"
        wire:model="code_type"
        :options="[
          ['label'=>'CPT','value'=>'CPT'],
          ['label'=>'HCPCS','value'=>'HCPCS'],
          ['label'=>'DRG','value'=>'DRG'],
          ['label'=>'ICD10','value'=>'ICD10'],
        ]"
      />
      <x-ts-input label="Code" placeholder="e.g., 99213" wire:model.defer="code" />
      <x-ts-input class="md:col-span-2" label="Description" placeholder="Short description" wire:model.defer="description" />
    </div>
    <div class="flex justify-end">
      <x-ts-button wire:click="add">Add code</x-ts-button>
    </div>

    @if($codes->isEmpty())
      <x-ts-card><div class="p-4 text-slate-600">No codes yet.</div></x-ts-card>
    @else
      <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50"><tr>
            <th class="px-4 py-2 text-left text-sm font-semibold">Type</th>
            <th class="px-4 py-2 text-left text-sm font-semibold">Code</th>
            <th class="px-4 py-2 text-left text-sm font-semibold">Description</th>
            <th class="px-4 py-2"></th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($codes as $c)
              <tr>
                <td class="px-4 py-2">{{ $c->code_type }}</td>
                <td class="px-4 py-2 font-semibold">{{ $c->code }}</td>
                <td class="px-4 py-2">{{ $c->description }}</td>
                <td class="px-4 py-2 text-right">
                  <x-ts-button.circle color="red" icon="trash" wire:click="remove({{ $c->id }})" />
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
