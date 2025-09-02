<div class="max-w-3xl mx-auto py-8" x-data>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Welcome to Hub Connect</h1>
        <p class="text-gray-600 mt-1">Pick your company type so we can tailor your setup.</p>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Selection Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Manufacturer -->
            <label
                class="group relative block rounded-2xl border p-5 cursor-pointer transition
                       hover:shadow-sm
                       @if($company_type==='manufacturer') border-indigo-500 ring-2 ring-indigo-200 @else border-gray-200 @endif"
            >
                <input
                    type="radio"
                    name="company_type"
                    value="manufacturer"
                    class="sr-only"
                    wire:model.live="company_type"
                />
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <!-- simple plus/box icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-3-3v6m8 4H4a2 2 0 01-2-2V7a2 2 0 012-2h7l2 2h7a2 2 0 012 2v10a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold">Manufacturer</div>
                        <p class="text-sm text-gray-600 mt-1">
                            You build medical devices and want US distribution partners.
                        </p>
                        <div class="mt-3">
                            <x-ts-badge state="info">List devices</x-ts-badge>
                            <x-ts-badge class="ml-1">Find distributors</x-ts-badge>
                        </div>
                    </div>
                </div>
                @if($company_type==='manufacturer')
                    <span class="absolute right-4 top-4 inline-flex items-center gap-1 text-sm text-indigo-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 9.435a1 1 0 011.414-1.414l3.222 3.221 6.657-6.657a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Selected
                    </span>
                @endif
            </label>

            <!-- Distributor -->
            <label
                class="group relative block rounded-2xl border p-5 cursor-pointer transition
                       hover:shadow-sm
                       @if($company_type==='distributor') border-indigo-500 ring-2 ring-indigo-200 @else border-gray-200 @endif"
            >
                <input
                    type="radio"
                    name="company_type"
                    value="distributor"
                    class="sr-only"
                    wire:model.live="company_type"
                />
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <!-- compass/search icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6.75L9 12l5.25 1.5M21 21l-4.35-4.35M10.5 3.75a6.75 6.75 0 100 13.5 6.75 6.75 0 000-13.5z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold">Distributor / Sales Rep</div>
                        <p class="text-sm text-gray-600 mt-1">
                            You represent products and want high‑quality lines to sell in your territory.
                        </p>
                        <div class="mt-3">
                            <x-ts-badge state="success">Find devices</x-ts-badge>
                            <x-ts-badge class="ml-1">Request intros</x-ts-badge>
                        </div>
                    </div>
                </div>
                @if($company_type==='distributor')
                    <span class="absolute right-4 top-4 inline-flex items-center gap-1 text-sm text-indigo-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 9.435a1 1 0 011.414-1.414l3.222 3.221 6.657-6.657a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Selected
                    </span>
                @endif
            </label>
        </div>

        @error('company_type')
            <x-ts-alert state="danger">{{ $message }}</x-ts-alert>
        @enderror

        <div class="flex items-center justify-between pt-2">
            <div class="text-sm text-gray-500">You can invite teammates later from Settings.</div>
            <x-ts-button
                type="submit"
                x-bind:disabled="$wire.company_type === ''"
                class="min-w-[140px]"
            >
                Continue
            </x-ts-button>
        </div>
    </form>
</div>
