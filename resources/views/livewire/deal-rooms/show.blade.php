<div class="max-w-6xl mx-auto w-full" 
     x-data 
     x-init="$nextTick(() => document.getElementById('dealroom-bottom')?.scrollIntoView({behavior:'instant'}))"
     x-on:livewire:navigated.window="document.getElementById('dealroom-bottom')?.scrollIntoView({behavior:'instant'})">

    <div class="grid grid-cols-1 lg:grid-cols-[1fr,20rem] gap-4">
        {{-- Chat column --}}
        <div class="bg-white shadow rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <div>
                    @php
                        $myId    = $this->companyId ?? null;
                        $otherId = $this->room->otherCompanyId($myId);
                        $other   = $this->room->otherCompany($myId);
                    @endphp
                    <h2 class="text-lg font-semibold">
                        Deal Room —
                        <span class="font-normal text-gray-600">
                            {{ $this->room->companySmall?->name }} ↔ {{ $this->room->companyLarge?->name }}
                        </span>
                    </h2>
                    <div class="text-xs text-gray-500 mt-1">
                        @if($otherTyping)
                            <span class="text-indigo-600">The other side is typing…</span>
                        @else
                            @if($otherOnline)
                                <span class="text-green-700 inline-flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span> Online
                                </span>
                            @else
                                <span>Last seen {{ optional($this->room->participantFor($otherId)?->last_seen_at)->diffForHumans() ?? '—' }}</span>
                            @endif
                        @endif
                    </div>
                </div>

                @if($other)
                    <a href="{{ route('companies.show', $other) }}"
                       class="px-3 py-1.5 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 text-sm">
                        View {{ $other->name }} profile
                    </a>
                @endif
            </div>

            {{-- Messages --}}
            <div wire:poll.2s="tick" class="p-5 space-y-4 max-h-[60vh] overflow-y-auto" id="dealroom-scroll">
                @forelse($room->messages as $msg)
                    @php
                        $mine = ($msg->company_id === ($this->companyId ?? 0));
                        $label = $mine ? 'You' : ($msg->user->name ?? $msg->company->name ?? 'Partner');
                    @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="rounded-xl px-4 py-2 max-w-[80%] {{ $mine ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                            <div class="text-xs opacity-75 mb-1">{{ $label }}</div>
                            <div class="whitespace-pre-wrap break-words">{{ $msg->body }}</div>
                            <div class="mt-1 text-[11px] opacity-70 flex items-center gap-2">
                                <span>{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                                @if($mine && $msg->read_at)
                                    <span class="inline-flex items-center">✓ Seen</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500">No messages yet — say hello 👋</div>
                @endforelse
                <div id="dealroom-bottom"></div>
            </div>

            {{-- Composer --}}
            <form wire:submit.prevent="send" class="border-t p-4 flex gap-3"
                  x-data
                  x-on:keydown.enter.prevent="
                    if (!$event.shiftKey) { $dispatch('submit'); } else { /* allow newline with shift */ }">
                <textarea
                    wire:model.defer="messageText"
                    wire:keydown.debounce.400ms="setTyping"
                    class="flex-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 resize-y min-h-[44px] max-h-40"
                    placeholder="Write a message... (Enter to send, Shift+Enter for a new line)"></textarea>
                <button
                    type="submit"
                    class="self-end px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                    Send
                </button>
            </form>
        </div>

        {{-- Files rail --}}
        <livewire:deal-rooms.files-rail :room="$room" />
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', () => {
            const el = document.getElementById('dealroom-bottom');
            if (el) el.scrollIntoView({behavior:'smooth'});
        });
    });
</script>
@endpush
