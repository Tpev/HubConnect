<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Company profile</h1>
        <p class="text-gray-600 mt-1">A few details to get your Hub Connect workspace ready.</p>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <x-ts-alert state="info">
            <div class="font-medium">Tip</div>
            <div class="text-sm">You can update this anytime from Settings → Company.</div>
        </x-ts-alert>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-ts-input label="Website" placeholder="https://example.com" wire:model.defer="website" />
                @error('website') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <x-ts-input label="Phone" placeholder="+1 (xxx) xxx-xxxx" wire:model.defer="phone" />
            <x-ts-input label="HQ State" placeholder="NC, CA, NY..." wire:model.defer="hq_state" />

            <x-ts-select.native
                label="HQ Country"
                wire:model.defer="hq_country"
                :options="[
                    ['label' => 'United States', 'value' => 'US'],
                    ['label' => 'Canada',        'value' => 'CA'],
                    ['label' => 'France',        'value' => 'FR'],
                    ['label' => 'Germany',       'value' => 'DE'],
                ]"
            />
        </div>

        <x-ts-textarea label="About your company" rows="5" placeholder="What you do, core product lines, clinical focus..." wire:model.defer="about" />

        <div class="flex items-center justify-between">
            <a href="{{ route('onboarding.role') }}" class="text-sm text-gray-600 underline">Back</a>
            <x-ts-button type="submit" class="min-w-[140px]">Save & Continue</x-ts-button>
        </div>
    </form>
</div>
