{{-- Community Announcements --}}
<x-ts-card class="overflow-hidden ring-brand">
    <x-slot name="header">
        <div class="relative">
            <div class="absolute -top-3 left-0 right-0 h-0.5 bg-[var(--brand-500)] rounded-full"></div>
            <div class="flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-semibold">Community announcements</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Beta Phase Message --}}
        <div class="rounded-xl p-3 bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="font-medium truncate">🌱 HubConnect Beta — Thank You for Building with Us</h4>
                        <span class="text-[11px] text-slate-500">October 2025</span>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed">
                        We’re thrilled to welcome all our early members to the <strong>HubConnect Beta</strong>!  
                        Every day, new manufacturers, distributors, and innovators are joining the network — and your feedback is helping shape the future of this platform.
                    </p>
                    <p class="mt-2 text-sm leading-relaxed">
                        As a beta user, you’re part of a small but growing community building the first dedicated hub for medical-device partnerships.  
                        If you notice a bug, have an idea, or want to suggest a new feature, please reach out — your input directly drives our roadmap.
                    </p>
                    <p class="mt-2 text-sm leading-relaxed">
                        Over the coming weeks, we’ll be refining the <strong>matching system</strong>, expanding <strong>recruitment tools</strong>,  
                        and adding more ways for you to connect and collaborate. Stay tuned — and thank you for being an early part of this journey. 💚
                    </p>
                </div>
            </div>
        </div>

        {{-- Optional smaller future messages --}}
        <div class="rounded-xl p-3 bg-slate-50 text-slate-700 ring-1 ring-slate-100">
            <h4 class="font-medium mb-1">💬 We’d Love Your Feedback</h4>
            <p class="text-sm leading-relaxed">
                Tell us what would make HubConnect even more useful for your business — new filters, metrics, onboarding tips, or integrations.  
                You can message us directly via the <strong>Help & Support</strong> button anytime.
            </p>
        </div>

        <div class="rounded-xl p-3 bg-amber-50 text-amber-800 ring-1 ring-amber-100">
            <h4 class="font-medium mb-1">🚀 Coming Soon</h4>
            <p class="text-sm leading-relaxed">
                We’re preparing public launch features including enhanced company profiles,  
                automatic matchmaking suggestions, and verified deal tracking.  
                Stay connected — updates will roll out progressively to all beta users.
            </p>
        </div>
    </div>
</x-ts-card>
