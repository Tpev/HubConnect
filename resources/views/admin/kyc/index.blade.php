<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">KYC Review</h2>
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
                                    <div class="font-medium">{{ $t->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">Status: {{ $t->kyc_status }}</div>
                                </td>
                                <td class="py-3">{{ $t->hq_country ?? '—' }}</td>
                                <td class="py-3">{{ $t->company_type ?? '—' }}</td>
                                <td class="py-3">
                                    @if($t->website)
                                        <a href="{{ $t->website }}" target="_blank" class="text-[var(--brand-700)] underline">{{ $t->website }}</a>
                                    @else — @endif
                                </td>
                                <td class="py-3">{{ optional($t->kyc_submitted_at)->diffForHumans() ?? '—' }}</td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('admin.kyc.approve',$t) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <details class="inline-block ms-2">
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
