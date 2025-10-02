<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">KYC Review</h2>
            {{-- Quick access to KYC Admin home (optional shortcut) --}}
            <a href="{{ route('admin.kyc.index') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">
                KYC Admin
            </a>
        </div>
    </x-slot>

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
                            <tr>
                                <td class="py-3">
                                    <div class="font-medium">
                                        <a href="{{ route('admin.kyc.show', $t) }}" class="hover:underline">
                                            {{ $t->name ?? '—' }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Status:
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                            @class([
                                                'bg-gray-100 text-gray-700'   => $t->kyc_status === 'new',
                                                'bg-yellow-100 text-yellow-800' => $t->kyc_status === 'pending_review',
                                                'bg-emerald-100 text-emerald-800' => $t->kyc_status === 'approved',
                                                'bg-rose-100 text-rose-800' => $t->kyc_status === 'rejected',
                                                'bg-orange-100 text-orange-800' => $t->kyc_status === 'suspended',
                                            ])">
                                            {{ ucfirst(str_replace('_',' ',$t->kyc_status ?? '—')) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3">{{ $t->hq_country ?? '—' }}</td>
                                <td class="py-3">{{ $t->company_type ?? '—' }}</td>
                                <td class="py-3">
                                    @if($t->website)
                                        <a href="{{ $t->website }}" target="_blank" class="text-[var(--brand-700)] underline">
                                            {{ $t->website }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3">{{ optional($t->kyc_submitted_at)->diffForHumans() ?? '—' }}</td>
                                <td class="py-3 text-right space-x-2">
                                    {{-- View button --}}
                                    <a href="{{ route('admin.kyc.show', $t) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50">
                                        View
                                    </a>

                                    {{-- Approve --}}
                                    <form action="{{ route('admin.kyc.approve',$t) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Reject w/ reason --}}
                                    <details class="inline-block align-top">
                                        <summary class="px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg cursor-pointer">Reject</summary>
                                        <form action="{{ route('admin.kyc.reject',$t) }}" method="POST" class="mt-2 p-3 bg-rose-50 rounded-lg border border-rose-200">
                                            @csrf @method('PUT')
                                            <label class="block text-xs text-rose-700 mb-1">Reason</label>
                                            <textarea name="reason" rows="3" class="w-64 rounded-md border-gray-300" placeholder="Reason…" required></textarea>
                                            <div class="mt-2 text-right">
                                                <button class="px-3 py-1.5 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Send</button>
                                            </div>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No items.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $teams->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
