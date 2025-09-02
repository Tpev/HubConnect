<div
  class="space-y-6"
  x-data="{ uploading:false, progress:0, filter:'all' }"
  x-on:livewire-upload-start="uploading = true; progress = 0"
  x-on:livewire-upload-progress="progress = $event.detail.progress"
  x-on:livewire-upload-error="uploading = false"
  x-on:livewire-upload-finish="uploading = false"
>
  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <h3 class="font-semibold">Documents</h3>
    <div class="text-sm text-slate-500">
      {{ $docs->count() }} uploaded
    </div>
  </div>

  {{-- CONTROLS --}}
  <div class="grid gap-4 md:grid-cols-3">
    <x-ts-select.native
      label="Category"
      wire:model="type"
      :options="[
        ['label'=>'Brochure','value'=>'brochure'],
        ['label'=>'IFUs','value'=>'ifus'],
        ['label'=>'Training','value'=>'training'],
        ['label'=>'Evidence','value'=>'evidence'],
      ]"
    />

    <div class="md:col-span-2">
      {{-- UPLOAD --}}
      <x-ts-upload
        label="Upload files"
        multiple
        wire:model="files"
        tip="PDF, DOCX, PNG, JPG up to 20 MB each."
        accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
        x-transition:enter="opacity-100"  {{-- workaround for floating --}}
      >
        <x-slot:footer when-uploaded>
          <div class="flex items-center gap-3 w-full">
            <div class="text-sm text-slate-600" x-show="$wire.files?.length">
              <span x-text="$wire.files?.length || 0"></span> selected
            </div>
            <x-ts-button class="ml-auto w-full md:w-auto" wire:click="storeFiles" :disabled="empty($files)">
              Upload selected files
            </x-ts-button>
          </div>
        </x-slot:footer>
      </x-ts-upload>

      {{-- VALIDATION --}}
      @error('files.*')
        <p class="text-sm text-rose-600 mt-2">{{ $message }}</p>
      @enderror

      {{-- LIVEWIRE UPLOAD PROGRESS --}}
      <div x-show="uploading" class="mt-3">
        <div class="h-2 bg-slate-200 rounded">
          <div class="h-2 bg-primary-500 rounded" :style="`width:${progress}%;`"></div>
        </div>
        <p class="mt-1 text-xs text-slate-500" x-text="`Uploading… ${progress}%`"></p>
      </div>

      {{-- PREVIEW SELECTED (NOT YET SAVED) --}}
      @if(!empty($files))
        <div class="mt-3">
          <p class="text-xs text-slate-500 mb-1">Ready to upload:</p>
          <ul class="text-sm text-slate-700 grid gap-1">
            @foreach($files as $f)
              <li class="flex items-center justify-between">
                <span>{{ $f->getClientOriginalName() }}</span>
                <span class="text-slate-400">{{ number_format($f->getSize()/1024, 0) }} KB</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  </div>

  {{-- FILTER PILLs --}}
  @php
    $types = ['all'=>'All','brochure'=>'Brochure','ifus'=>'IFUs','training'=>'Training','evidence'=>'Evidence'];
  @endphp
  @if($docs->isNotEmpty())
    <div class="flex flex-wrap items-center gap-2">
      @foreach($types as $val => $label)
        <button
          type="button"
          class="px-3 py-1.5 rounded-full text-sm border transition
                 "
          :class="filter === '{{ $val }}'
                   ? 'bg-primary-50 text-primary-700 border-primary-200'
                   : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
          x-on:click="filter='{{ $val }}'"
        >
          {{ $label }}
        </button>
      @endforeach
    </div>
  @endif

  {{-- EXISTING DOCS LIST --}}
  @if($docs->isEmpty())
    <x-ts-card>
      <div class="p-4 text-slate-600">No documents yet.</div>
    </x-ts-card>
  @else
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-semibold">File</th>
            <th class="px-4 py-2 text-left text-sm font-semibold">Type</th>
            <th class="px-4 py-2 text-left text-sm font-semibold">Uploaded</th>
            <th class="px-4 py-2 text-right"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($docs as $d)
            <tr x-show="filter==='all' || filter==='{{ $d->type }}'">
              <td class="px-4 py-2">
                <a class="underline decoration-slate-300 hover:decoration-slate-600"
                   href="{{ Storage::disk('public')->url($d->path) }}" target="_blank">
                  {{ $d->original_name ?? basename($d->path) }}
                </a>
              </td>
              <td class="px-4 py-2">
                <x-ts-badge>
                  {{ \Illuminate\Support\Str::title($d->type) }}
                </x-ts-badge>
              </td>
              <td class="px-4 py-2 text-slate-500">
                {{ \Illuminate\Support\Carbon::parse($d->created_at)->diffForHumans() }}
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center justify-end gap-1">
                  {{-- View --}}
                  <x-ts-button.circle
                    color="gray" icon="eye"
                    href="{{ Storage::disk('public')->url($d->path) }}"
                    target="_blank"
                    title="Open"
                  />
                  {{-- Copy link (simple Alpine copy) --}}
                  <x-ts-button.circle
                    color="blue" icon="clipboard"
                    x-data
                    x-on:click.prevent="navigator.clipboard.writeText('{{ Storage::disk('public')->url($d->path) }}')"
                    title="Copy link"
                  />
                  {{-- Delete --}}
                  <x-ts-button.circle
                    color="red" icon="trash"
                    wire:click="delete({{ $d->id }})"
                    title="Delete"
                  />
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
