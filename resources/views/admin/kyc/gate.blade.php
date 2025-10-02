<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Welcome to the Club</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-2xl p-8 space-y-6">
                <p class="text-gray-600">
                    We verify every new company to keep the network trusted.
                    Reviews are typically completed within <strong>one business day</strong>.
                    We’ll email you when your account is approved.
                </p>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs
                        @class([
                          'bg-amber-100 text-amber-800' => in_array($team->kyc_status,['new','pending_review']),
                          'bg-emerald-100 text-emerald-800' => $team->kyc_status==='approved',
                          'bg-rose-100 text-rose-800' => in_array($team->kyc_status,['rejected','suspended']),
                        ])">
                        {{ $team->kycStatusLabel() }}
                    </span>
                    @if($team->kyc_status === 'pending_review' && $team->kyc_submitted_at)
                        <span class="text-xs text-gray-500">Submitted {{ $team->kyc_submitted_at->diffForHumans() }}</span>
                    @endif
                </div>

                @if($team->kyc_status === 'rejected' && $team->kyc_notes)
                    <div class="p-4 rounded-lg bg-rose-50 text-rose-700">
                        <div class="font-semibold mb-1">Review notes</div>
                        <div class="text-sm whitespace-pre-wrap">{{ $team->kyc_notes }}</div>
                    </div>
                @endif

                <div class="grid sm:grid-cols-2 gap-3">
                    <a href="{{ route('companies.profile.edit', $team) }}" class="btn-accent text-center">
                        Edit Company Profile
                    </a>
                    <a href="mailto:support@yourapp.com" class="btn-subtle text-center">Contact support</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
