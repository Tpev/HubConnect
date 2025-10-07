<div
    x-data
    x-on:keydown.escape.window="Livewire.dispatch('close-connections')"
>
    @if($open)
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/30 z-40" x-on:click="Livewire.dispatch('close-connections')"></div>

        {{-- Slide-over --}}
        <div class="fixed inset-y-0 right-0 z-50 w-full max-w-lg bg-white shadow-xl border-l overflow-y-auto">
            <div class="sticky top-0 z-10 bg-white border-b px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Connections</h2>
                <button class="p-2 hover:bg-slate-100 rounded"
                        x-on:click="Livewire.dispatch('close-connections')"
                        aria-label="Close">
                    ✕
                </button>
            </div>

            <div class="p-5">
                {{-- Reuse your existing Requests list component --}}
                @livewire('requests.index', [], key('requests-index-panel'))
            </div>
        </div>
    @endif
</div>
