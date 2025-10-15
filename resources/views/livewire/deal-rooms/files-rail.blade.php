{{-- resources/views/livewire/deal-rooms/files-rail.blade.php --}}
<div class="sticky top-0">
    <div class="rounded-2xl ring-1 ring-slate-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800">Files</h3>
            <div class="text-xs text-slate-500">
                {{ $files->count() }} item{{ $files->count() === 1 ? '' : 's' }}
            </div>
        </div>

        {{-- Upload --}}
        <div class="px-4 py-3 border-b">
            <x-ts-upload
                wire:model="upload"
                label="Upload a file"
                accept="*/*"
                :multiple="false"
                :show="['filename' => true, 'progress' => true, 'icon' => true]"
                :personalize="[
                    'placeholder' => [
                        'title' => 'Drop file or click to upload',
                        'subtitle' => 'Max 50 MB • PDF, images, docs, sheets…',
                    ],
                ]"
            >
                <x-slot:tip>
                    <span class="text-[11px] text-slate-500">Auto-uploads when selected.</span>
                </x-slot:tip>

                {{-- Keep footer slot to avoid TallStack null error --}}
                <x-slot:footer></x-slot:footer>
            </x-ts-upload>
        </div>

        {{-- List --}}
        <div class="divide-y">
            @forelse($files as $f)
                @php
                    $filename = $f->name ?? basename($f->path);
                    $ext = strtoupper(pathinfo($filename, PATHINFO_EXTENSION) ?: ($f->type ? explode('/', $f->type)[1] : 'FILE'));
                    $prettySize = function ($bytes) {
                        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2).' GB';
                        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2).' MB';
                        if ($bytes >= 1024)       return number_format($bytes / 1024, 2).' KB';
                        return $bytes.' B';
                    };
                @endphp

                <div class="px-4 py-3">
                    {{-- Line 1: Name (full width, wraps) --}}
                    <div class="text-sm font-medium text-slate-800 break-words leading-5">
                        {{ $filename }}
                    </div>

                    {{-- Line 2: Format + meta --}}
                    <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-slate-600">
                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 font-medium text-slate-700 border-slate-200">
                            {{ $ext }}
                        </span>
                        <span>{{ $prettySize($f->size ?? 0) }}</span>
                        <span>• Uploaded {{ optional($f->created_at)->diffForHumans() }}</span>
                        @if($f->uploader)
                            <span>• by {{ $f->uploader->name }}</span>
                        @endif
                    </div>

                    {{-- Line 3: Actions --}}
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            wire:click="preview({{ $f->id }})"
                            class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 hover:bg-slate-50">
                            Preview
                        </button>
                        <a
                            href="#"
                            wire:click.prevent="download({{ $f->id }})"
                            class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 hover:bg-slate-50">
                            Download
                        </a>
                        <button
                            type="button"
                            wire:click="remove({{ $f->id }})"
                            class="px-2.5 py-1.5 text-xs rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-slate-500">No files yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Preview Modal --}}
    <div
        x-data="{ open: @entangle('previewUrl').live }"
        x-cloak
        x-show="open"
        x-on:keydown.escape.window="open=false"
        class="relative z-50"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-black/40" x-show="open" x-transition.opacity></div>
        <div class="fixed inset-0 flex items-start justify-center p-4 sm:p-8">
            <div class="w-full max-w-4xl rounded-xl bg-white shadow-xl" x-show="open" x-transition>
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h3 class="text-base font-semibold">Preview</h3>
                    <button type="button" class="p-1 text-slate-400 hover:text-slate-600" x-on:click="open=false" aria-label="Close preview">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"/>
                        </svg>
                    </button>
                </div>
                <div class="px-4 py-4">
                    @if ($previewUrl)
                        @php $isPdf = str_contains(strtolower($previewUrl), '.pdf'); @endphp
                        @if ($isPdf)
                            <iframe src="{{ $previewUrl }}" class="w-full h-[70vh] rounded-lg border"></iframe>
                        @else
                            <img src="{{ $previewUrl }}" class="max-h-[70vh] mx-auto rounded-lg border" alt="Preview">
                        @endif
                    @else
                        <div class="text-sm text-slate-500">No preview available.</div>
                    @endif
                </div>
                <div class="flex justify-end gap-2 border-t px-4 py-3">
                    @if($previewId)
                        <a href="#" wire:click.prevent="download({{ $previewId }})" class="btn-accent outline text-sm px-3 py-1.5 rounded-lg">Download</a>
                    @endif
                    <button type="button" class="btn-brand text-sm px-3 py-1.5 rounded-lg" x-on:click="open=false">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
