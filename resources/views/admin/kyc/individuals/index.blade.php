{{-- resources/views/admin/kyc/individuals/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">KYC Review — Individuals</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-2xl p-6">

                {{-- Tabs: Companies / Individuals --}}
                <div class="flex items-center gap-2 mb-4">
                    <a href="{{ route('admin.kyc.index', ['status' => request('status','pending_review')]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.kyc.index') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Companies
                    </a>
                    <a href="{{ route('admin.kyc.individuals.index', ['status' => request('status','pending_review')]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.kyc.individuals.index') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Individuals
                    </a>
                </div>

                {{-- Filter --}}
                <form method="GET" class="mb-4">
                    <label class="text-sm text-gray-600 me-2">Status</label>
                    <select name="status" class="rounded-md border-gray-300" onchange="this.form.submit()">
                        @foreach(['pending_review','draft','approved','rejected'] as $s)
                            <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Candidate</th>
                            <th class="py-2">Location</th>
                            <th class="py-2">Phone</th>
                            <th class="py-2">Submitted</th>
                            <th class="py-2">Status</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($subs as $s)
                            <tr>
                                <td class="py-3">
                                    <div class="font-medium">{{ $s->full_name ?: ($s->user?->name ?? '—') }}</div>
                                    <div class="text-xs text-slate-500 break-all">{{ $s->user?->email ?? '—' }}</div>
                                </td>
                                <td class="py-3">
                                    {{ $s->city ? $s->city.', ' : '' }}{{ $s->region ? $s->region.', ' : '' }}{{ $s->country ?? '—' }}
                                </td>
                                <td class="py-3">
                                    {{ $s->phone ?? '—' }}
                                </td>
                                <td class="py-3">
                                    {{ optional($s->submitted_at)->diffForHumans() ?? '—' }}
                                    @if($s->submitted_at)
                                        <div class="text-xs text-gray-400">{{ $s->submitted_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs
                                        @class([
                                            'bg-amber-50 text-amber-700' => $s->status === 'pending_review',
                                            'bg-emerald-50 text-emerald-700' => $s->status === 'approved',
                                            'bg-red-50 text-red-700' => $s->status === 'rejected',
                                            'bg-slate-100 text-slate-700' => $s->status === 'draft',
                                        ])">
                                        {{ ucfirst(str_replace('_',' ',$s->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right whitespace-nowrap">
                                    @if($s->status === 'pending_review')
                                        <form action="{{ route('admin.kyc.individuals.approve', $s) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <button class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <details class="inline-block ms-2 align-middle">
                                            <summary class="px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg cursor-pointer">Reject</summary>
                                            <form action="{{ route('admin.kyc.individuals.reject', $s) }}" method="POST" class="mt-2">
                                                @csrf @method('PUT')
                                                <textarea name="reason" rows="3" class="w-64 rounded-md border-gray-300" placeholder="Reason…" required></textarea>
                                                <div class="mt-2">
                                                    <button class="px-3 py-1.5 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Send</button>
                                                </div>
                                            </form>
                                        </details>
                                    @else
                                        <span class="text-xs text-slate-500">No actions</span>
                                    @endif
                                </td>
                            </tr>
                            @if($s->notes)
                                <tr>
                                    <td colspan="6" class="bg-slate-50">
                                        <div class="px-4 py-3 text-sm">
                                            <div class="text-slate-500">Notes</div>
                                            <div class="mt-1 rounded-md bg-white p-3 ring-1 ring-slate-200 text-slate-700 text-sm whitespace-pre-line">
                                                {{ $s->notes }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No items.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $subs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
