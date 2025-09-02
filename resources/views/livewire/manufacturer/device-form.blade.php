<div class="max-w-3xl mx-auto space-y-6">
  <div>
    <h1 class="text-2xl font-bold">
      {{ $device ? 'Edit Device' : 'New Device' }}
    </h1>
    <p class="text-gray-600 mt-1">Add the essentials. You can refine details later.</p>
  </div>

  <form wire:submit.prevent="save" class="space-y-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Basics</h2>
        @if($device)<span class="text-xs text-slate-500">ID #{{ $device->id }}</span>@endif
      </div>

      <x-ts-input
        label="Device name"
        placeholder="e.g., Smart Wound Monitor"
        wire:model.defer="name"
        required
      />
      @error('name') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror

      <div class="grid gap-4 md:grid-cols-2">
        <x-ts-select.native
          label="Category"
          wire:model.defer="category_id"
          :options="$categories->map(fn($c)=>['label'=>$c->name,'value'=>$c->id])->toArray()"
          placeholder="—"
        />
        <x-ts-input
          label="Indications (short)"
          placeholder="e.g., chronic wounds, diabetic ulcers"
          wire:model.defer="indications"
        />
      </div>

      <x-ts-textarea
        label="Description"
        rows="4"
        placeholder="What does it do, who is it for, what’s the differentiator?"
        wire:model.defer="description"
      />
      @error('description') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">
      <h2 class="font-semibold">Regulatory & Commercial</h2>

      <div class="grid gap-4 md:grid-cols-3">
        <x-ts-select.native
          label="FDA pathway"
          wire:model.defer="fda_pathway"
          :options="[
            ['label'=>'None','value'=>'none'],
            ['label'=>'Exempt','value'=>'exempt'],
            ['label'=>'510(k)','value'=>'510k'],
            ['label'=>'PMA','value'=>'pma'],
          ]"
        />

        <x-ts-toggle
          label="Reimbursable"
          wire:model.live="reimbursable"
        />

        <x-ts-input
          type="number"
          step="0.01"
          min="0"
          max="100"
          label="Margin target (%)"
          placeholder="e.g., 35"
          wire:model.defer="margin_target"
        />
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <x-ts-select.native
          label="Status"
          wire:model.defer="status"
          :options="[
            ['label'=>'Draft','value'=>'draft'],
            ['label'=>'Listed','value'=>'listed'],
            ['label'=>'Paused','value'=>'paused'],
          ]"
        />
      </div>

      {{-- Live preview for the toggle --}}
      <div class="pt-1">
        <div class="flex items-center gap-2 text-sm">
          <span class="text-slate-500">Preview:</span>
          @if($reimbursable)
            <x-ts-badge state="success">Reimbursable</x-ts-badge>
          @else
            <x-ts-badge>Not reimbursable</x-ts-badge>
          @endif
        </div>
      </div>
    </div>
<x-ts-switch label="Published" wire:model.defer="device.is_published" />
<x-ts-select label="Visibility" wire:model.defer="device.visibility">
    <x-ts-select.option value="public" label="Public" />
    <x-ts-select.option value="verified_only" label="Visible to verified users" />
    <x-ts-select.option value="invite_only" label="Invite only (hidden)" />
</x-ts-select>

<x-ts-input type="number" step="0.01" label="Commission %" wire:model.defer="device.commission_percent" />
<x-ts-textarea label="Commission notes" wire:model.defer="device.commission_notes" />

    <div class="flex items-center justify-between">
      <a href="{{ route('m.devices') }}" class="text-sm text-slate-600 underline">Cancel</a>
      <div class="flex gap-2">
        <x-ts-button type="button" variant="secondary" wire:click="$set('status','draft'); $wire.save();">
          Save as draft
        </x-ts-button>
        <x-ts-button type="submit">Save</x-ts-button>
      </div>
    </div>
  </form>
@if($device)
  <div class="mt-10 space-y-10">

  </div>
@else
  <x-ts-alert state="info" class="mt-6">
    Save the device first to manage documents, regulatory, reimbursement and targeting.
  </x-ts-alert>
@endif


</div>
