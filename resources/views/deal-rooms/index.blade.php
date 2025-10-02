<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Deal Rooms</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-2xl">
                @if($rooms->isEmpty())
                    <div class="p-6 text-gray-500">No deal rooms yet.</div>
                @else
                    <ul class="divide-y">
                        @foreach($rooms as $room)
                            @php
                                $otherId   = $room->otherCompanyId($companyId);
                                $other     = $room->otherCompany($companyId);
                                $lastMsg   = $room->messages->first(); // loaded as latest 1
                                $unread    = $room->unreadCountFor($companyId);
                                $otherPart = $room->participants->firstWhere('company_id', $otherId);
                                $isOnline  = $otherPart && $otherPart->last_seen_at && $otherPart->last_seen_at->gt(now()->subMinutes(2));
                                $isTyping  = $otherPart && $otherPart->last_typing_at && $otherPart->last_typing_at->gt(now()->subSeconds(6));
                            @endphp
                            <li class="py-4 px-6 flex items-start justify-between hover:bg-gray-50">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('deal-rooms.show', $room) }}" class="font-medium text-gray-900 hover:text-indigo-700">
                                            {{ $other?->name ?? 'Partner' }}
                                        </a>
                                        @if($isOnline)
                                            <span class="inline-flex items-center text-xs text-green-700">
                                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span> Online
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                Last seen {{ optional($otherPart?->last_seen_at)->diffForHumans() ?? '—' }}
                                            </span>
                                        @endif
                                        @if($isTyping)
                                            <span class="ml-2 text-xs text-indigo-600">typing…</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600 line-clamp-1">
                                        @if($lastMsg)
                                            <span class="text-gray-500">
                                                {{ $lastMsg->company_id === $companyId ? 'You' : ($lastMsg->company?->name ?? 'They') }}:
                                            </span>
                                            {{ $lastMsg->body }}
                                        @else
                                            No messages yet
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <div class="text-xs text-gray-400">
                                        {{ optional($lastMsg?->created_at)->diffForHumans() }}
                                    </div>
                                    @if($unread > 0)
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $unread }}
                                        </span>
                                    @endif
                                    <div>
                                        <a href="{{ route('deal-rooms.show', $room) }}"
                                           class="mt-2 inline-block px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
