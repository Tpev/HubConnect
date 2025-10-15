<div class="max-w-7xl mx-auto space-y-6">
    {{-- Row 1: Onboarding (full width) --}}
    @livewire('dashboard.onboarding-card')

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left column (main) --}}
        <div class="space-y-6 lg:col-span-2">
            @livewire('dashboard.kpis')
            @livewire('dashboard.inbox')
            @livewire('dashboard.recruitment')
            @livewire('dashboard.quick-actions')
        </div>

        {{-- Right column (secondary) --}}
        <div class="space-y-6">

            @livewire('dashboard.video-card')
			@livewire('dashboard.community-announcements')

        </div>
    </div>
</div>
