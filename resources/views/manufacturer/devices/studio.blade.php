{{-- resources/views/manufacturer/devices/studio.blade.php --}}
<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Device Studio</h2>
                <p class="text-sm text-gray-500">
                    Manage all settings for: <span class="font-medium">{{ $device->name }}</span>
                </p>
            </div>

            <x-ts-button as="a" href="{{ route('m.devices') }}" variant="secondary">
                ← Back to devices
            </x-ts-button>
        </div>
    </x-slot>

    @php
        // Normalize ?tab= to match Tab labels
        $allowed  = ['Overview','Regulatory','Reimbursement','Targeting','Documents'];
        $selected = ucfirst(strtolower(request('tab', 'overview')));
        if (! in_array($selected, $allowed)) $selected = 'Overview';
    @endphp

    <style>[x-cloak]{display:none!important}</style>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-ts-card class="p-0 overflow-hidden">
                <div class="p-6">
                    {{-- Alpine only remembers selection; TSUI handles switching --}}
                    <div
                        x-data="{
                            remember(t){
                                const url = new URL(location);
                                url.searchParams.set('tab', t.toLowerCase());
                                history.replaceState(null,'',url);
                                localStorage.setItem('deviceStudioTab', t);
                            }
                        }"
                        x-init="
                            // If no ?tab, restore from localStorage (deep link friendly)
                            (() => {
                                const url = new URL(location);
                                if (!url.searchParams.get('tab')) {
                                    const mem = localStorage.getItem('deviceStudioTab');
                                    if (mem) {
                                        url.searchParams.set('tab', mem.toLowerCase());
                                        history.replaceState(null,'',url);
                                    }
                                }
                            })();
                        "
                    >
                        {{-- TSUI Tabs: each items block contains its own panel content --}}
                        <x-ts-tab selected="{{ $selected }}" scroll-on-mobile>
                            <x-ts-tab.items tab="Overview" x-on:click="remember('Overview')">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                    <div class="lg:col-span-8">
                                        <x-ts-card class="p-4">
                                            <livewire:manufacturer.device-form
                                                :deviceId="$device->id"
                                                :key="'device-form-'.$device->id"
                                                lazy
                                            />
                                        </x-ts-card>
                                    </div>

                                    <div class="lg:col-span-4">
                                        <x-ts-card class="p-4 space-y-3">
                                            <x-ts-badge>Tips</x-ts-badge>
                                            <p class="text-sm text-gray-600">
                                                Update device basics here. Use the <span class="font-medium">Save</span> button in each section.
                                            </p>
                                            {{-- FIX: use a valid Heroicons name --}}
                                            <x-ts-alert
                                                icon="information-circle"
                                                title="Heads up"
                                                description="You can switch tabs without losing unsaved changes in the current section."
                                            />
                                        </x-ts-card>
                                    </div>
                                </div>
                            </x-ts-tab.items>

                            <x-ts-tab.items tab="Regulatory" x-on:click="remember('Regulatory')">
                                <x-ts-card class="p-4">
                                    <livewire:manufacturer.device-regulatory
                                        :deviceId="$device->id"
                                        :key="'device-regulatory-'.$device->id"
                                        lazy
                                    />
                                </x-ts-card>
                            </x-ts-tab.items>

                            <x-ts-tab.items tab="Reimbursement" x-on:click="remember('Reimbursement')">
                                <x-ts-card class="p-4">
                                    <livewire:manufacturer.device-reimbursement
                                        :deviceId="$device->id"
                                        :key="'device-reimbursement-'.$device->id"
                                        lazy
                                    />
                                </x-ts-card>
                            </x-ts-tab.items>

                            <x-ts-tab.items tab="Targeting" x-on:click="remember('Targeting')">
                                <x-ts-card class="p-4">
                                    <livewire:manufacturer.device-targeting
                                        :deviceId="$device->id"
                                        :key="'device-targeting-'.$device->id"
                                        lazy
                                    />
                                </x-ts-card>
                            </x-ts-tab.items>

                            <x-ts-tab.items tab="Documents" x-on:click="remember('Documents')">
                                <x-ts-card class="p-4">
                                    <livewire:manufacturer.device-documents
                                        :deviceId="$device->id"
                                        :key="'device-documents-'.$device->id"
                                        lazy
                                    />
                                </x-ts-card>
                            </x-ts-tab.items>
                        </x-ts-tab>
                    </div>
                </div>

                {{-- Sticky helper bar --}}
                <div class="border-t bg-gray-50/60 px-6 py-3 flex items-center justify-between">
                    <div class="text-xs text-gray-500">
                        Each tab saves independently. Look for the “Save” button inside the section.
                    </div>
                    <div class="flex gap-2">
                        <x-ts-button as="a" href="{{ route('m.devices') }}" variant="secondary">Back</x-ts-button>
                    </div>
                </div>
            </x-ts-card>
        </div>
    </div>

    {{-- Toasts:
        use TallStackUi\Traits\Interactions;
        $this->toast()->success('Saved', 'Your changes have been saved.')->send();
    --}}
</x-app-layout>
