<div class="max-w-3xl mx-auto p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Identity Verification (Simple)</h1>
        <p class="text-slate-500">Confirm your basic details to unlock job applications.</p>
    </div>

    @if (session('status') === 'submitted')
        <div class="p-3 rounded bg-amber-50 text-amber-800 border border-amber-200">
            Your KYC has been submitted and is pending review.
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="text-sm font-medium">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="full_name" class="mt-1 w-full rounded-md border-slate-300" placeholder="e.g., Jordan A. Williams">
                @error('full_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Country <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="country" class="mt-1 w-full rounded-md border-slate-300" placeholder="United States">
                @error('country') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">State</label>
                <input type="text" wire:model.defer="region" class="mt-1 w-full rounded-md border-slate-300" placeholder="e.g., California, NY, TX">
                @error('region') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">City</label>
                <input type="text" wire:model.defer="city" class="mt-1 w-full rounded-md border-slate-300" placeholder="e.g., San Diego">
                @error('city') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input
                    type="tel"
                    wire:model.defer="phone"
                    class="mt-1 w-full rounded-md border-slate-300"
                    inputmode="tel"
                    placeholder="e.g., +1 (555) 123-4567"
                >
                <p class="text-xs text-slate-500 mt-1">US format recommended (country code +1).</p>
                @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Notes (optional)</label>
            <textarea
                wire:model.defer="notes"
                rows="4"
                class="mt-1 w-full rounded-md border-slate-300"
                placeholder="Optional context for reviewers (e.g., preferred contact times in ET/PT, relocation notes)."
            ></textarea>
            @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="button" wire:click="saveDraft" class="px-4 py-2 rounded-md border border-slate-300 text-slate-700">Save draft</button>
            @if ($currentStatus === 'pending_review')
                <span class="text-sm text-amber-700">Status: Pending review</span>
            @elseif ($currentStatus === 'approved')
                <span class="text-sm text-emerald-700">Status: Approved</span>
            @elseif ($currentStatus === 'rejected')
                <span class="text-sm text-red-700">Status: Rejected — please update and resubmit</span>
            @else
                <button type="button" wire:click="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Submit for review</button>
            @endif
        </div>
    </div>
</div>
