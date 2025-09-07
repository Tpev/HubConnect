<div
    x-data="roleplayUI()"
    x-init="init()"
    @transcript-updated.window="scrollToBottom()"
    class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6"
>
    {{-- Header --}}
    <x-ts-card class="p-5 ring-1 ring-emerald-100 bg-white/80 backdrop-blur">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-orange-500"></div>
            <div>
                <h1 class="text-lg font-semibold">AI Role-Play Simulator</h1>
                <p class="text-sm text-slate-600">Practice procurement conversations with instant scoring.</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                @if(!$started)
                    <x-ts-button class="btn-accent" wire:click="start">Start Scenario</x-ts-button>
                @else
                    <x-ts-badge>Scenario: {{ $scenario['title'] ?? 'N/A' }}</x-ts-badge>
                    <x-ts-button class="btn-ghost" @click="openDocs = !openDocs">
                        <span x-text="openDocs ? 'Hide Docs' : 'Show Docs'"></span>
                    </x-ts-button>
                @endif
            </div>
        </div>
    </x-ts-card>

    @if($started)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT: Chat + Composer --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- Scenario Summary (compact) --}}
                <x-ts-card class="p-4 ring-1 ring-slate-200/80 bg-white">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <x-ts-badge class="badge-brand">Goal</x-ts-badge>
                        <span class="text-slate-700">{{ $scenario['goal'] ?? '' }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                        <x-ts-badge>Persona</x-ts-badge>
                        <span class="text-slate-700">{{ $scenario['persona'] ?? '' }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $scenario['instructions'] ?? '' }}</p>
                </x-ts-card>

                {{-- Chat --}}
                <x-ts-card class="p-0 ring-1 ring-slate-200/80 overflow-hidden">
                    <div
                        x-ref="scroller"
                        class="max-h-[55vh] md:max-h-[60vh] overflow-y-auto p-4 space-y-3 bg-slate-50"
                    >
                        @foreach($transcript as $t)
                            @if($t['role'] === 'buyer')
                                <div class="flex gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-300"></div>
                                    <div class="px-3 py-2 rounded-2xl bg-white ring-1 ring-slate-200 text-slate-800 max-w-[80%]">
                                        {{ $t['text'] }}
                                    </div>
                                </div>
                            @else
                                <div class="flex gap-3 justify-end">
                                    <div class="px-3 py-2 rounded-2xl bg-emerald-600 text-white max-w-[80%]">
                                        {{ $t['text'] }}
                                    </div>
                                    <div class="h-8 w-8 rounded-full bg-emerald-400"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Composer --}}
                    <div class="p-3 border-t bg-white">
                        {{-- Quick replies --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            <x-ts-badge class="hover:cursor-pointer" wire:click="insert('I propose a 10-patient pilot with OR-time and infection-rate metrics.')">Pilot proposal</x-ts-badge>
                            <x-ts-badge class="hover:cursor-pointer" wire:click="insert('Let’s schedule a procurement committee review next week; I’ll bring a 1-page TCO summary.')">Committee step</x-ts-badge>
                            <x-ts-badge class="hover:cursor-pointer" wire:click="insert('I can provide a data request template to estimate total cost of ownership with your team.')">Data request</x-ts-badge>
                            <x-ts-badge class="hover:cursor-pointer" wire:click="insert('Training and 60-day on-site clinical support will minimize disruption during adoption.')">Change mgmt</x-ts-badge>
                        </div>

                        <div
                            class="flex items-end gap-3"
                            x-data="{
                                submitOnEnter(e) {
                                    if (e.key === 'Enter' && !e.shiftKey) {
                                        e.preventDefault();
                                        $wire.send();
                                    }
                                }
                            }"
                        >
                            {{-- Bigger textarea --}}
                            <textarea
                                wire:model.defer="input"
                                @keydown="submitOnEnter($event)"
                                rows="4"
                                class="w-full text-base md:text-lg leading-6 md:leading-7 resize-y rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 placeholder:sla te-400 p-3"
                                placeholder="Type your reply… Press Enter to send (Shift+Enter for newline). Type /end to finish."
                            ></textarea>

                            <div class="flex flex-col gap-2 shrink-0">
                                <x-ts-button wire:click="send" :disabled="$done">Send</x-ts-button>
                                <x-ts-button wire:click="finish" class="btn-ghost">Finish & Score</x-ts-button>
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Shortcuts: <kbd class="px-1 py-0.5 rounded bg-slate-100">Enter</kbd> send,
                            <kbd class="px-1 py-0.5 rounded bg-slate-100">Shift</kbd>+<kbd class="px-1 py-0.5 rounded bg-slate-100">Enter</kbd> newline.
                        </p>
                    </div>
                </x-ts-card>

                {{-- Scorecard --}}
                @if($score)
                    <x-ts-card class="p-5 ring-1 ring-emerald-200/70 bg-white">
                        <div class="flex items-center gap-4 flex-wrap">
                            <x-ts-badge class="badge-accent text-base">Overall: {{ $score['overall'] }}/100</x-ts-badge>
                            <x-ts-badge>Coverage: {{ $score['covered'] }}</x-ts-badge>
                        </div>
                        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3 mt-4 text-sm">
                            <div class="p-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-100">Objection handling: <b>{{ $score['per_dim']['objection_handling'] ?? '-' }}</b></div>
                            <div class="p-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-100">Clinical/Economic: <b>{{ $score['per_dim']['clinical_economic_value'] ?? '-' }}</b></div>
                            <div class="p-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-100">Communication: <b>{{ $score['per_dim']['communication_rapport'] ?? '-' }}</b></div>
                            <div class="p-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-100">Compliance: <b>{{ $score['per_dim']['compliance_awareness'] ?? '-' }}</b></div>
                            <div class="p-3 rounded-xl bg-emerald-50 ring-1 ring-emerald-100">Closing: <b>{{ $score['per_dim']['closing_progression'] ?? '-' }}</b></div>
                        </div>
                        @if(!empty($score['comments']))
                            <div class="mt-4">
                                <h4 class="font-semibold">Coaching</h4>
                                <ul class="list-disc ml-5 text-sm text-slate-700">
                                    @foreach($score['comments'] as $c)
                                        <li>{{ $c }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </x-ts-card>
                @endif
            </div>

            {{-- RIGHT: Sticky Documentation panel (hidden on mobile unless toggled) --}}
            <div class="lg:col-span-1" x-show="openDocs" x-transition>
                <div class="lg:sticky lg:top-4 space-y-3">
                    <x-ts-card class="p-4 ring-1 ring-slate-200/80 bg-white">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800">Documentation</h3>
                            <div class="flex items-center gap-2">
                                <x-ts-button size="sm" class="btn-ghost" @click="copyDocs($refs.docsBlock)">Copy</x-ts-button>
                                <x-ts-button size="sm" class="btn-ghost" @click="docsModal = true">Pop-out</x-ts-button>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-slate-600">
                            <p class="mb-2"><b>Tip:</b> Cite facts; if you need hospital-internal data, propose the process (pilot, committee, data request).</p>
                        </div>
                        <pre
                            x-ref="docsBlock"
                            class="text-[12px] leading-5 bg-slate-50 rounded-xl p-3 ring-1 ring-slate-200 overflow-auto max-h-[60vh] whitespace-pre-wrap"
                        >{{ $dossierText }}</pre>
                    </x-ts-card>
                </div>
            </div>
        </div>

        {{-- Pop-out modal for docs --}}
        <div
            x-cloak
            x-show="docsModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="docsModal=false"
        >
            <div class="w-full max-w-4xl bg-white rounded-2xl ring-1 ring-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Documentation</h3>
                    <div class="flex items-center gap-2">
                        <x-ts-button size="sm" class="btn-ghost" @click="copyDocs($refs.docsModalBlock)">Copy</x-ts-button>
                        <x-ts-button size="sm" class="btn-accent" @click="docsModal=false">Close</x-ts-button>
                    </div>
                </div>
                <pre
                    x-ref="docsModalBlock"
                    class="mt-3 text-sm leading-6 bg-slate-50 rounded-xl p-4 ring-1 ring-slate-200 overflow-auto max-h-[70vh] whitespace-pre-wrap"
                >{{ $dossierText }}</pre>
            </div>
        </div>
    @endif
</div>

<script>
function roleplayUI() {
    return {
        openDocs: true,     // default open on desktop (toggle in header)
        docsModal: false,
        init() {
            // Auto-open docs on desktop, collapse on small screens
            this.openDocs = window.matchMedia('(min-width: 1024px)').matches;
            this.scrollToBottom();
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const s = this.$refs.scroller;
                if (s) s.scrollTop = s.scrollHeight;
            });
        },
        copyDocs(el) {
            if (!el) return;
            const text = el.innerText || el.textContent || '';
            navigator.clipboard.writeText(text);
        },
    }
}
</script>
