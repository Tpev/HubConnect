{{-- resources/views/admin/kyc/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">KYC Review</h2>
    </x-slot>

    {{-- Prevent initial flash of hidden rows --}}
    <style>[x-cloak]{ display:none !important; }</style>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-2xl p-6">
                <form method="GET" class="mb-4">
                    <label class="text-sm text-gray-600 me-2">Status</label>
                    <select name="status" class="rounded-md border-gray-300" onchange="this.form.submit()">
                        @foreach(['pending_review','new','approved','rejected','suspended'] as $s)
                            <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </form>

                <div x-data="{ openId: null }">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2">Company</th>
                                <th class="py-2">Country</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Website</th>
                                <th class="py-2">Submitted</th>
                                <th class="py-2 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                        @forelse($teams as $t)
                            {{-- MAIN ROW --}}
                            <tr class="align-top">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @if($t->team_profile_photo_path ?? false)
                                            <img src="{{ Storage::url($t->team_profile_photo_path) }}" class="h-9 w-9 rounded-lg ring-1 ring-slate-200" alt="">
                                        @else
                                            <div class="h-9 w-9 rounded-lg bg-slate-100 ring-1 ring-slate-200"></div>
                                        @endif
                                        <div>
                                            <div class="font-medium">{{ $t->name ?? '—' }}</div>
                                            <div class="text-xs text-gray-500">
                                                Status: <span class="font-medium">{{ $t->kyc_status }}</span>
                                                @if($t->kyc_verified_at)
                                                    • Verified {{ $t->kyc_verified_at->diffForHumans() }}
                                                @endif
                                            </div>

                                            <button type="button"
                                                    @click="openId = (openId === {{ $t->id }}) ? null : {{ $t->id }}"
                                                    class="mt-1 inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs ring-1 ring-slate-200 hover:bg-slate-50">
                                                <svg class="h-3.5 w-3.5 transition-transform"
                                                     :class="openId === {{ $t->id }} ? 'rotate-180' : ''"
                                                     viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                                </svg>
                                                <span x-text="openId === {{ $t->id }} ? 'Hide details' : 'View details'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">{{ $t->hq_country ?? '—' }}</td>
                                <td class="py-3">{{ $t->company_type ?? '—' }}</td>
                                <td class="py-3">
                                    @if($t->website)
                                        <a href="{{ $t->website }}" target="_blank" class="text-[var(--brand-700)] underline break-all">{{ $t->website }}</a>
                                    @else — @endif
                                </td>
                                <td class="py-3">
                                    {{ optional($t->kyc_submitted_at)->diffForHumans() ?? '—' }}
                                    @if($t->kyc_submitted_at)
                                        <div class="text-xs text-gray-400">{{ $t->kyc_submitted_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                </td>
                                <td class="py-3 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.kyc.approve',$t) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <details class="inline-block ms-2 align-middle">
                                        <summary class="px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg cursor-pointer">Reject</summary>
                                        <form action="{{ route('admin.kyc.reject',$t) }}" method="POST" class="mt-2">
                                            @csrf @method('PUT')
                                            <textarea name="reason" rows="3" class="w-64 rounded-md border-gray-300" placeholder="Reason…" required></textarea>
                                            <div class="mt-2">
                                                <button class="px-3 py-1.5 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Send</button>
                                            </div>
                                        </form>
                                    </details>
                                </td>
                            </tr>

                            {{-- DETAILS ROW --}}
                            <tr x-show="openId === {{ $t->id }}" x-transition x-cloak>
                                <td colspan="6" class="pb-5">
                                    <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            {{-- Company basics + profile --}}
                                            <div class="space-y-2">
                                                <div class="text-xs font-semibold text-slate-600">Company & Profile</div>
                                                <div class="text-sm">
                                                    <div><span class="text-slate-500">Name:</span> <span class="font-medium text-slate-900">{{ $t->name ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Slug:</span> <span class="font-medium text-slate-900 break-all">{{ $t->slug ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Type:</span> <span class="font-medium text-slate-900">{{ $t->company_type ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Country:</span> <span class="font-medium text-slate-900">{{ $t->hq_country ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Website:</span>
                                                        @if($t->website)
                                                            <a class="font-medium text-[var(--brand-700)] underline break-all" target="_blank" href="{{ $t->website }}">{{ $t->website }}</a>
                                                        @else
                                                            <span class="text-slate-900">—</span>
                                                        @endif
                                                    </div>
                                                    <div><span class="text-slate-500">Year founded:</span> <span class="font-medium text-slate-900">{{ $t->year_founded ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Headcount:</span> <span class="font-medium text-slate-900">{{ $t->headcount ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Stage:</span> <span class="font-medium text-slate-900">{{ $t->stage ?? '—' }}</span></div>

                                                    @if($t->summary)
                                                        <div class="mt-2">
                                                            <div class="text-slate-500">Summary</div>
                                                            <div class="mt-1 rounded-md bg-white p-3 ring-1 ring-slate-200 text-slate-700 text-sm whitespace-pre-line">
                                                                {{ $t->summary }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="text-xs text-slate-500 mt-2">
                                                        Created: {{ optional($t->created_at)->format('Y-m-d H:i') ?? '—' }}
                                                        • Updated: {{ optional($t->updated_at)->format('Y-m-d H:i') ?? '—' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- KYC block --}}
                                            <div class="space-y-2">
                                                <div class="text-xs font-semibold text-slate-600">KYC</div>
                                                <div class="text-sm">
                                                    <div><span class="text-slate-500">Status:</span> <span class="font-medium text-slate-900">{{ $t->kyc_status }}</span></div>
                                                    <div><span class="text-slate-500">Submitted:</span> <span class="font-medium text-slate-900">{{ optional($t->kyc_submitted_at)->format('Y-m-d H:i') ?? '—' }}</span></div>
                                                    <div><span class="text-slate-500">Verified:</span> <span class="font-medium text-slate-900">{{ optional($t->kyc_verified_at)->format('Y-m-d H:i') ?? '—' }}</span></div>
                                                    @if($t->kyc_notes)
                                                        <div class="mt-2">
                                                            <div class="text-slate-500">Notes</div>
                                                            <div class="mt-1 rounded-md bg-white p-2 ring-1 ring-slate-200 text-slate-700 text-xs">{{ $t->kyc_notes }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Owner & members --}}
                                            <div class="space-y-2">
                                                <div class="text-xs font-semibold text-slate-600">People</div>
                                                <div class="text-sm">
                                                    <div class="mb-2">
                                                        <div class="text-slate-500">Owner</div>
                                                        <div class="font-medium text-slate-900">
                                                            {{ $t->owner?->name ?? '—' }}
                                                        </div>
                                                        <div class="text-xs text-slate-600 break-all">
                                                            {{ $t->owner?->email ?? '—' }}
                                                        </div>
                                                    </div>
                                                    <div class="text-slate-500">Members ({{ $t->users_count }})</div>
                                                    @if($t->users->count())
                                                        <ul class="mt-1 space-y-1 max-h-36 overflow-auto pr-1">
                                                            @foreach($t->users as $u)
                                                                <li class="flex items-center justify-between gap-3">
                                                                    <span class="font-medium text-slate-800 truncate">{{ $u->name }}</span>
                                                                    <span class="text-xs text-slate-600 truncate">{{ $u->email }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <div class="text-xs text-slate-600">—</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex items-center justify-end gap-2">
                                            @if(Route::has('admin.companies.show'))
                                                <a href="{{ route('admin.companies.show', $t->id) }}"
                                                   class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm ring-1 ring-slate-200 hover:bg-slate-100">
                                                    View company
                                                </a>
                                            @endif
                                            @if(Route::has('companies.show'))
                                                <a href="{{ route('companies.show', $t->id) }}"
                                                   class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm ring-1 ring-slate-200 hover:bg-slate-100" target="_blank">
                                                    Public profile
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No items.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $teams->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
