{{-- resources/views/landing.blade.php --}}
{{-- HubConnect — Public Home (US Market) --}}
<x-guest-layout>
<div
    x-data="{
        audience: 'manufacturer',
        scrolled: false,
        setAudience(a){ this.audience = a },
    }"
    x-init="
        const onScroll = () => { scrolled = window.scrollY > 260 }
        onScroll(); window.addEventListener('scroll', onScroll)
    "
    class="relative min-h-screen bg-slate-50 text-slate-900 selection:bg-indigo-200/60 selection:text-slate-900"
>
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-29ZRSRYL8W"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-29ZRSRYL8W');
</script>
</head>
{{-- ===== Sticky CTA on scroll ===== --}}
<div
    x-show="scrolled"
    x-transition.opacity
    class="fixed top-3 inset-x-0 z-40"
    aria-hidden="true"
>
    <div class="mx-auto max-w-4xl">
        <div class="mx-4 rounded-2xl bg-white/90 backdrop-blur ring-1 ring-emerald-200 shadow-md">
            <div class="flex flex-col sm:flex-row items-center gap-3 px-4 py-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="badge-brand">HubConnect</span>
                    <span class="font-medium"> — Match. Manage. Close.</span>
                </div>
                <div class="flex items-center gap-2 sm:ml-auto">
                    <a href="{{ route('register') }}" class="btn-brand text-sm inline-flex items-center gap-1">
                        <x-ts-icon name="bolt" class="h-4 w-4"/> Start free
                    </a>
                    <a href="{{ route('manufacturers') }}" class="btn-accent outline text-sm inline-flex items-center gap-1">
                        <x-ts-icon name="play" class="h-4 w-4"/> See how it works
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ================= HERO (Two-Sided Matchmaking First) ================= --}}
<section class="relative overflow-hidden grad-hero">
    <div
        class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28"
        x-data="{ audience:'manufacturer', scrolled:false, setAudience(a){ this.audience=a } }"
        x-init="
            const onScroll = () => { scrolled = window.scrollY > 260 }
            onScroll(); window.addEventListener('scroll', onScroll)
        "
    >
        <div class="grid items-center gap-10 lg:grid-cols-12">
            {{-- Copy + Audience toggle --}}
            <div class="lg:col-span-7">
                <x-ts-badge class="mb-3 badge-accent">US MedTech • Two-Sided Matchmaking</x-ts-badge>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl/tight font-extrabold tracking-tight">
<span>
    <span class="text-emerald-700 font-semibold">Manufacturers</span> & 
    <span class="text-orange-600 font-semibold">Distributors/Reps</span> 
    — find each other fast.
</span>

<span class="block mt-2">
    Match by specialty, territory & accounts — then manage coverage and commissions in one place.
</span>

                </h1>

                <p class="mt-4 text-base sm:text-lg text-slate-700">
                    Built for the U.S. device market. <span class="font-semibold text-slate-900">Match</span> by state, ZIP clusters and IDNs, collaborate in a secure
                    <span class="font-semibold text-slate-900">deal room</span>, and <span class="font-semibold text-slate-900">e-sign</span> without leaving HubConnect.
                </p>

                {{-- Audience switch (keeps the page personalized, but the core message is matchmaking) --}}
                <div class="mt-6 inline-flex rounded-xl ring-1 ring-slate-200 bg-white p-1">
                    <button
                        :class="audience==='manufacturer' ? 'btn-brand text-white' : 'text-slate-700'"
                        class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition"
                        @click="setAudience('manufacturer')"
                        type="button"
                    >
                        I’m a Manufacturer
                    </button>
                    <button
                        :class="audience==='rep' ? 'btn-brand text-white' : 'text-slate-700'"
                        class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition"
                        @click="setAudience('rep')"
                        type="button"
                    >
                        I’m a Distributor / Rep
                    </button>
                </div>

                {{-- Value bullets switch --}}
                <div class="mt-4">
                    <template x-if="audience==='manufacturer'">
                        <ul class="grid sm:grid-cols-2 gap-y-2 gap-x-5 text-sm text-slate-700">
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Match with vetted U.S. distributors & reps</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Target by state, ZIP clusters & named IDNs</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Deal room for files, redlines, approvals & e-sign</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Consignment lots, alerts & coverage analytics</li>
                        </ul>
                    </template>
                    <template x-if="audience==='rep'">
                        <ul class="grid sm:grid-cols-2 gap-y-2 gap-x-5 text-sm text-slate-700">
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Discover high-fit lines in your territories</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Clear protected accounts & overlap rules</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Simple commission tracking & split approvals</li>
                            <li class="flex items-center gap-2"><x-ts-icon name="check" class="h-4 w-4 text-emerald-600"/> Visibility with verified manufacturers</li>
                        </ul>
                    </template>
                </div>

                {{-- Dual CTA (no Alpine in route(); two buttons toggled) --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt" class="w-full btn-brand">
                        Start free
                    </x-ts-button>

                    {{-- Manufacturer CTA --}}
                    <x-ts-button
                        as="a"
                        href="{{ route('manufacturers') }}"
                        x-show="audience==='manufacturer'"
                        x-cloak
                        size="lg"
                        icon="play"
                        class="w-full btn-accent outline"
                    >
                        Find distributors & reps
                    </x-ts-button>

                    {{-- Rep CTA --}}
                    <x-ts-button
                        as="a"
                        href="{{ route('reps') }}"
                        x-show="audience==='rep'"
                        x-cloak
                        size="lg"
                        icon="play"
                        class="w-full btn-accent outline"
                    >
                        Find device lines to represent
                    </x-ts-button>
                </div>
                <p class="mt-3 text-xs sm:text-sm text-slate-600">Free to start • No long-term contracts • See value in minutes</p>
            </div>

            {{-- DASHBOARD SNAPSHOT (role-aware: manufacturer / rep) --}}
            <div class="lg:col-span-5">
                <x-ts-card class="p-5 sm:p-6 shadow-xl ring-brand bg-white/90 backdrop-blur">
                    <div class="space-y-4">
                        <h3 class="text-base sm:text-lg font-semibold">Your dashboard (sample)</h3>

                        <div class="rounded-2xl ring-1 ring-slate-200 bg-gradient-to-br from-white to-slate-50 p-3 sm:p-4">

                            {{-- ========== Manufacturer view ========== --}}
                            <div x-show="audience==='manufacturer'" x-cloak class="space-y-3">
                                <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">9</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">New Matches</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">6</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Deals in Progress</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">3</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Awaiting E-Sign</div>
                                    </x-ts-card>
                                </div>

                                <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">82%</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">State Coverage</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">14</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Low-Stock Sites</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">$480k</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">30-Day Pipeline</div>
                                    </x-ts-card>
                                </div>

                                <div class="mt-2 grid sm:grid-cols-2 gap-3 text-sm">
                                    <x-ts-card class="p-3 ring-1 ring-slate-200 bg-white text-left">
                                        <div class="font-medium mb-1">Next Actions</div>
                                        <ul class="space-y-1 text-slate-700">
                                            <li>• Review 3 new rep applications</li>
                                            <li>• Add protected accounts: HCA TX</li>
                                            <li>• Upload updated price list</li>
                                        </ul>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 ring-1 ring-slate-200 bg-white text-left">
                                        <div class="font-medium mb-1">Alerts</div>
                                        <ul class="space-y-1 text-slate-700">
                                            <li>• Consignment low: Dallas (12 units)</li>
                                            <li>• Contract redlines pending (2)</li>
                                            <li>• Coverage gap: FL Panhandle</li>
                                        </ul>
                                    </x-ts-card>
                                </div>
                            </div>

                            {{-- ========== Distributor / Rep view ========== --}}
                            <div x-show="audience==='rep'" x-cloak class="space-y-3">
                                <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">7</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">New Lines Nearby</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">4</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Active Opportunities</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">$36k</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Commission Forecast</div>
                                    </x-ts-card>
                                </div>

                                <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">12%</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Avg. Split</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-emerald-50/60 ring-1 ring-emerald-100">
                                        <div class="text-xl sm:text-2xl font-bold">3</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Docs Awaiting You</div>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 bg-orange-50/60 ring-1 ring-orange-100">
                                        <div class="text-xl sm:text-2xl font-bold">5</div>
                                        <div class="text-[10px] sm:text-xs text-slate-600">Protected Accts</div>
                                    </x-ts-card>
                                </div>

                                <div class="mt-2 grid sm:grid-cols-2 gap-3 text-sm">
                                    <x-ts-card class="p-3 ring-1 ring-slate-200 bg-white text-left">
                                        <div class="font-medium mb-1">Next Actions</div>
                                        <ul class="space-y-1 text-slate-700">
                                            <li>• Apply to 2 ortho lines</li>
                                            <li>• Confirm splits on TX-North</li>
                                            <li>• Upload credential packet</li>
                                        </ul>
                                    </x-ts-card>
                                    <x-ts-card class="p-3 ring-1 ring-slate-200 bg-white text-left">
                                        <div class="font-medium mb-1">Reminders</div>
                                        <ul class="space-y-1 text-slate-700">
                                            <li>• Follow-up: Baylor Dallas buyer</li>
                                            <li>• Schedule demo for RPM line</li>
                                            <li>• Renew insurance (30 days)</li>
                                        </ul>
                                    </x-ts-card>
                                </div>
                            </div>
                        </div>

                        <x-ts-button as="a" href="{{ route('register') }}" class="w-full btn-brand" icon="arrow-right-end-on-rectangle">
                            Create your free account
                        </x-ts-button>
                        <p class="text-xs text-slate-500">Demo content. Your real dashboard updates as you match, start deals & log activity.</p>
                    </div>
                </x-ts-card>
            </div>

        </div>
    </div>
</section>

{{-- ================= MARKETPLACE FINDER (put immediately after the HERO) ================= --}}
<section class="py-6 bg-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <x-ts-card class="p-4 sm:p-5 ring-1 ring-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-ts-icon name="magnifying-glass" class="h-5 w-5 text-emerald-600"/>
                    <h3 class="font-semibold">Find your match</h3>
                </div>
                <div class="flex gap-2">
                    <x-ts-badge class="badge-accent">Live</x-ts-badge>
                    <x-ts-badge class="badge-accent">U.S. coverage</x-ts-badge>
                    <x-ts-badge class="badge-accent">Verified partners</x-ts-badge>
                </div>
            </div>

            {{-- Search style (non-functional demo; hook it up later) --}}
            <div class="mt-4 grid gap-3 sm:grid-cols-12">
                <div class="sm:col-span-5">
                    <x-ts-select.native :options="[
                        ['label' => 'Browse manufacturers seeking reps', 'value' => 'manufacturers'],
                        ['label' => 'Browse distributors / rep groups', 'value' => 'distributors'],
                        ['label' => 'Browse open territories', 'value' => 'open_territories'],
                    ]" />
                </div>

                <div class="sm:col-span-5">
                    <x-ts-input placeholder="Filter by state, IDN or specialty (e.g., Texas, HCA, Ortho)"/>
                </div>
                <div class="sm:col-span-2">
                    <x-ts-button as="a" href="{{ route('register') }}" class="w-full btn-brand" icon="bolt">Start</x-ts-button>
                </div>
            </div>
        </x-ts-card>
    </div>
</section>

{{-- ================= SOCIAL PROOF ================= --}}
<section class="py-10 sm:py-12 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl ring-1 ring-white/10 bg-white/5 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-slate-200">
                    Trusted by U.S. med-tech startups, device manufacturers & healthcare distributors.
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-4 opacity-80">
                    {{-- Replace with real logos when ready --}}
                    <div class="h-6 rounded bg-white/10"></div>
                    <div class="h-6 rounded bg-white/10"></div>
                    <div class="h-6 rounded bg-white/10"></div>
                    <div class="h-6 rounded bg-white/10"></div>
                    <div class="h-6 rounded bg-white/10"></div>
                    <div class="h-6 rounded bg-white/10"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= WHO IT'S FOR ================= --}}
<section class="py-16 lg:py-24 bg-gradient-to-b from-emerald-50 via-white to-orange-50">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-center">Who it’s for</h2>
        <p class="mt-3 text-center text-slate-700">Manufacturers & Distributors/Reps collaborate in one workspace.</p>

        <div class="mt-10 grid gap-4 sm:gap-6 sm:grid-cols-2">
            <x-ts-card class="p-5 sm:p-6 ring-brand bg-white">
                <div class="mb-2 text-emerald-600"><x-ts-icon name="building-office-2" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Manufacturers</h3>
                <ul class="mt-2 text-sm space-y-2 text-slate-700">
                    <li>✓ Reach vetted U.S. distributors & reps by specialty</li>
                    <li>✓ Target by state, ZIP clusters & IDNs</li>
                    <li>✓ Deal room: files, redlines, approvals & e-sign</li>
                    <li>✓ Consignment lots & low-stock alerts</li>
                </ul>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-accent bg-white">
                <div class="mb-2 text-orange-600"><x-ts-icon name="user-group" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Distributors & Reps</h3>
                <ul class="mt-2 text-sm space-y-2 text-slate-700">
                    <li>✓ Find high-fit device lines for your territories</li>
                    <li>✓ Clear protected accounts & overlap rules</li>
                    <li>✓ Simple commission tracking & split approvals</li>
                    <li>✓ Shared dashboards & performance visibility</li>
                </ul>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= TWO-SIDED MARKETPLACE (elevates the matchmaking idea) ================= --}}
<section class="py-16 lg:py-24 bg-gradient-to-b from-emerald-50 via-white to-orange-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl font-bold">The two-sided marketplace for U.S. medical devices</h2>
            <p class="mt-3 text-slate-700">Manufacturers list devices & target coverage. Distributors and reps find lines to represent—then both sides collaborate to close.</p>
        </div>

        <div class="mt-10 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-2">
            <x-ts-card class="p-6 ring-brand bg-white">
                <div class="mb-2 text-emerald-600"><x-ts-icon name="building-office-2" class="h-6 w-6"/></div>
                <h3 class="font-semibold">For Manufacturers</h3>
                <p class="mt-1 text-sm text-slate-700">Publish listings, define states & IDNs, invite or receive interest, manage terms & e-sign.</p>
                <ul class="mt-3 text-sm space-y-2 text-slate-700">
                    <li>✓ Match with vetted distributors & reps</li>
                    <li>✓ Target by state, ZIP clusters & named accounts</li>
                    <li>✓ Deal room: files, redlines, approvals & e-sign</li>
                </ul>
                <x-ts-button as="a" href="{{ route('manufacturers') }}" class="mt-4 btn-accent outline" icon="play">
                    Find distributors & reps
                </x-ts-button>
            </x-ts-card>

            <x-ts-card class="p-6 ring-accent bg-white">
                <div class="mb-2 text-orange-600"><x-ts-icon name="user-group" class="h-6 w-6"/></div>
                <h3 class="font-semibold">For Distributors & Reps</h3>
                <p class="mt-1 text-sm text-slate-700">Discover high-fit lines, claim or request territories, align splits, and close faster.</p>
                <ul class="mt-3 text-sm space-y-2 text-slate-700">
                    <li>✓ Find lines that fit your specialty & states</li>
                    <li>✓ Clear protected accounts & overlap rules</li>
                    <li>✓ Simple commission tracking & split approvals</li>
                </ul>
                <x-ts-button as="a" href="{{ route('reps') }}" class="mt-4 btn-accent outline" icon="play">
                    Find device lines
                </x-ts-button>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= PRODUCT TOUR ================= --}}
<section id="features" class="py-16 lg:py-24 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl sm:text-4xl font-bold">Everything you need to sell medical devices together</h2>
            <p class="mt-3 text-slate-700">Target precisely. Collaborate securely. Close faster.</p>
        </div>

        <div class="mt-10 sm:mt-12 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <x-ts-card class="p-5 sm:p-6 ring-brand bg-emerald-50/50 hover:bg-emerald-50 transition">
                <div class="mb-2 text-emerald-700"><x-ts-icon name="beaker" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Device Type Targeting</h3>
                <p class="mt-1 text-sm text-slate-700">Reach partners with the right product expertise (RPM, monitoring, ortho implants, wound care).</p>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-accent bg-orange-50/50 hover:bg-orange-50 transition">
                <div class="mb-2 text-orange-700"><x-ts-icon name="map" class="h-6 w-6"/></div>
                <h3 class="font-semibold">State & ZIP Coverage</h3>
                <p class="mt-1 text-sm text-slate-700">Control by state, ZIP clusters & named IDNs; add exclusions & protected accounts.</p>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-brand bg-emerald-50/50 hover:bg-emerald-50 transition">
                <div class="mb-2 text-emerald-700"><x-ts-icon name="briefcase" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Deal Room & E-Sign</h3>
                <p class="mt-1 text-sm text-slate-700">Files, structured terms, redlines, approvals & native e-signature in one place.</p>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-1 ring-amber-100 bg-amber-50/50 hover:bg-amber-50 transition">
                <div class="mb-2 text-amber-700"><x-ts-icon name="banknotes" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Commissions & Splits</h3>
                <p class="mt-1 text-sm text-slate-700">Multi-tier rates, shared deals, approvals & exports—no spreadsheets.</p>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-brand bg-emerald-50/50 hover:bg-emerald-50 transition">
                <div class="mb-2 text-emerald-700"><x-ts-icon name="cube" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Consignment & Stock</h3>
                <p class="mt-1 text-sm text-slate-700">Track by rep, lot & site with low-stock alerts; serialize where needed.</p>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-accent bg-orange-50/50 hover:bg-orange-50 transition">
                <div class="mb-2 text-orange-700"><x-ts-icon name="chart-bar" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Coverage & Performance</h3>
                <p class="mt-1 text-sm text-slate-700">See gaps & overlaps; watch pipeline velocity & win rates by device & territory.</p>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= SPOTLIGHT (US Examples) ================= --}}
<section id="spotlight" class="py-16 lg:py-24 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="text-3xl sm:text-4xl font-bold">Target precisely. Close confidently.</h2>
            <p class="mt-3 text-slate-700">A quick peek at territory targeting and the deal room inside HubConnect.</p>
        </div>

        <div class="mt-10 grid gap-4 sm:gap-6 lg:grid-cols-2">
            {{-- Targeting mock (US) --}}
            <x-ts-card class="p-5 sm:p-6 ring-1 ring-slate-200 bg-white">
                <div class="flex items-center gap-2">
                    <x-ts-icon name="funnel" class="h-5 w-5 text-emerald-600"/>
                    <h3 class="font-semibold">State, ZIP & Account Targeting</h3>
                </div>
                <div class="mt-4 space-y-4">
                    <div>
                        <x-ts-label>Device Categories</x-ts-label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-ts-badge class="badge-brand">RPM</x-ts-badge>
                            <x-ts-badge class="badge-brand">Patient Monitoring</x-ts-badge>
                            <x-ts-badge class="badge-brand">Orthopedic Implants</x-ts-badge>
                            <x-ts-badge class="badge-brand">Wound Care</x-ts-badge>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-ts-label>Care Settings</x-ts-label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-ts-badge class="badge-accent">Hospital</x-ts-badge>
                                <x-ts-badge class="badge-accent">Clinic</x-ts-badge>
                                <x-ts-badge class="badge-accent">ASC</x-ts-badge>
                                <x-ts-badge class="badge-accent">Home</x-ts-badge>
                            </div>
                        </div>
                        <div>
                            <x-ts-label>Regions</x-ts-label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-ts-badge class="badge-brand">Texas</x-ts-badge>
                                <x-ts-badge class="badge-brand">Florida</x-ts-badge>
                                <x-ts-badge class="badge-brand">California</x-ts-badge>
                                <x-ts-badge class="badge-brand">Mid-Atlantic</x-ts-badge>
                            </div>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-ts-label>Named Accounts (IDNs)</x-ts-label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-ts-badge class="badge-brand">HCA Healthcare</x-ts-badge>
                                <x-ts-badge class="badge-brand">Ascension</x-ts-badge>
                                <x-ts-badge class="badge-brand">Kaiser Permanente</x-ts-badge>
                            </div>
                        </div>
                        <div>
                            <x-ts-label>Exclusions</x-ts-label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-ts-badge class="badge-accent">Protected: Baylor Scott &amp; White – Dallas</x-ts-badge>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ts-card>

            {{-- Deal Room mock --}}
            <x-ts-card class="p-5 sm:p-6 ring-1 ring-slate-200 bg-white">
                <div class="flex items-center gap-2">
                    <x-ts-icon name="briefcase" class="h-5 w-5 text-orange-600"/>
                    <h3 class="font-semibold">Deal Room & Contracts</h3>
                </div>
                <div class="mt-4 space-y-4">
                    <div class="rounded-xl ring-1 ring-slate-200 p-4 bg-slate-50">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ts-badge class="badge-brand">ACME Ortho • TX-North</x-ts-badge>
                            <x-ts-badge class="badge-accent">NDA signed</x-ts-badge>
                            <x-ts-badge class="badge-accent">Rev-Share 18%</x-ts-badge>
                            <x-ts-badge class="badge-accent">Scope: Ortho</x-ts-badge>
                        </div>
                        <div class="mt-3 grid sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="font-medium">Files</div>
                                <ul class="mt-1 space-y-1 text-slate-700">
                                    <li>• NDA_v2_signed.pdf</li>
                                    <li>• PriceList_US_2025_Q4.xlsx</li>
                                    <li>• TerritoryMap_TX_North.png</li>
                                </ul>
                            </div>
                            <div>
                                <div class="font-medium">Tasks</div>
                                <ul class="mt-1 space-y-1 text-slate-700">
                                    <li>• Redline exclusivity clause — <span class="text-amber-600">Pending</span></li>
                                    <li>• Add named accounts — <span class="text-emerald-600">Done</span></li>
                                    <li>• Upload regulatory certs — <span class="text-amber-600">Pending</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-ts-button size="sm" icon="pencil-square" class="btn-brand outline">Edit Terms</x-ts-button>
                            <x-ts-button size="sm" icon="document-arrow-down" class="btn-accent outline">Export PDF</x-ts-button>
                            <x-ts-button size="sm" icon="check-badge" class="btn-brand">Send for e-signature</x-ts-button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">Demo content. Actual screens include role-based permissions, version history & full audit trail.</p>
                </div>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= HOW IT WORKS (short linear) ================= --}}
<section id="how" class="py-16 lg:py-24 bg-gradient-to-b from-white to-emerald-50/70">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold">From first match to signed contract</h2>
            <p class="mt-3 text-slate-700">A simple flow designed for real U.S. healthcare sales cycles.</p>
        </div>

        <div class="mt-8 sm:mt-10">
            <x-ts-step :items="[
                ['title' => 'Create', 'description' => 'Publish device listings or build your rep profile with specialty & territory.'],
                ['title' => 'Match', 'description' => 'We surface the best fits—by state, ZIP clusters, IDNs & history.'],
                ['title' => 'Collaborate', 'description' => 'Share files, redline terms and align incentives in the deal room.'],
                ['title' => 'Sign', 'description' => 'Generate agreements and collect e-signatures with an audit trail.'],
            ]" class="rounded-xl ring-1 ring-slate-200 bg-white p-4 sm:p-6"/>
        </div>

        <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <x-ts-button as="a" href="{{ route('register') }}" icon="bolt" class="btn-brand">
                Start free
            </x-ts-button>
            <x-ts-button as="a" href="{{ route('manufacturers') }}" icon="presentation-chart-bar" class="btn-accent outline">
                See Manufacturer benefits
            </x-ts-button>
            <x-ts-button as="a" href="{{ route('reps') }}" icon="user-group" class="btn-accent outline">
                See Distributor / Rep benefits
            </x-ts-button>
        </div>
    </div>
</section>

{{-- ================= ANALYTICS MOCK (US Coverage) ================= --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-2">
            <x-ts-card class="p-5 sm:p-6 ring-1 ring-slate-200 bg-slate-50">
                <div class="flex items-center gap-2">
                    <x-ts-icon name="globe-americas" class="h-5 w-5 text-emerald-600"/>
                    <h3 class="font-semibold">Coverage Heat (by State)</h3>
                </div>
                <div class="mt-4 text-sm text-slate-700">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>Texas</span><span class="font-semibold">92%</span>
                        </div>
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>Florida</span><span class="font-semibold">78%</span>
                        </div>
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>California</span><span class="font-semibold">64%</span>
                        </div>
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>Ohio</span><span class="font-semibold">71%</span>
                        </div>
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>Pennsylvania</span><span class="font-semibold">56%</span>
                        </div>
                        <div class="rounded-xl bg-white p-3 ring-1 ring-slate-200 flex items-center justify-between">
                            <span>New York</span><span class="font-semibold">48%</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Illustrative data. Real dashboards show gaps & overlaps at state, ZIP & account levels.</p>
                </div>
            </x-ts-card>

            <x-ts-card class="p-5 sm:p-6 ring-1 ring-slate-200 bg-slate-50">
                <div class="flex items-center gap-2">
                    <x-ts-icon name="arrows-right-left" class="h-5 w-5 text-orange-600"/>
                    <h3 class="font-semibold">Split Commissions (example)</h3>
                </div>
                <div class="mt-4 space-y-2 text-sm text-slate-700">
                    <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Deal: Ortho Implant – Kaiser NorCal</span>
                            <x-ts-badge class="badge-accent">Approved</x-ts-badge>
                        </div>
                        <div class="mt-2 grid sm:grid-cols-3 gap-3">
                            <div class="rounded-lg bg-emerald-50/50 p-3 ring-1 ring-emerald-100">
                                Manufacturer Share <span class="font-semibold">82%</span>
                            </div>
                            <div class="rounded-lg bg-orange-50/50 p-3 ring-1 ring-orange-100">
                                Rep A (Territory) <span class="font-semibold">12%</span>
                            </div>
                            <div class="rounded-lg bg-amber-50/50 p-3 ring-1 ring-amber-100">
                                Rep B (Assist) <span class="font-semibold">6%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">Approval flows & exports included. Keep finance out of spreadsheets.</p>
                </div>
            </x-ts-card>
        </div>
    </div>
</section>


{{-- ================= TESTIMONIALS ================= --}}
<section class="py-16 lg:py-24 bg-slate-50">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold">What customers say</h2>
            <p class="mt-3 text-slate-700">Outcomes from early U.S. manufacturers and distributors.</p>
        </div>

        <div class="mt-10 grid gap-4 sm:gap-6 sm:grid-cols-2">
            <x-ts-card class="p-6 bg-white ring-brand">
                <p class="text-slate-800">“We filled two open territories in 30 days and cut back-and-forth on contracts by half.”</p>
                <div class="mt-4 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-emerald-100 ring-1 ring-emerald-200"></div>
                    <div>
                        <div class="font-medium">VP Commercial, Ortho Device Co.</div>
                        <div class="text-xs text-slate-500">Texas & California</div>
                    </div>
                </div>
            </x-ts-card>

            <x-ts-card class="p-6 bg-white ring-accent">
                <p class="text-slate-800">“Commission splits & approvals are finally clean. My reps stopped arguing with spreadsheets.”</p>
                <div class="mt-4 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-orange-100 ring-1 ring-orange-200"></div>
                    <div>
                        <div class="font-medium">Principal Distributor</div>
                        <div class="text-xs text-slate-500">Mid-Atlantic</div>
                    </div>
                </div>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= SECURITY / TRUST ================= --}}
<section id="security" class="py-16 lg:py-24 bg-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <x-ts-card class="p-6 ring-brand bg-slate-50">
                <div class="mb-2 text-emerald-700"><x-ts-icon name="lock-closed" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Security & Controls</h3>
                <ul class="mt-2 text-sm space-y-2 text-slate-700">
                    <li>✓ Role-based access & audit logs</li>
                    <li>✓ SSO/SAML options</li>
                    <li>✓ US-hosted infrastructure</li>
                    <li>✓ No PHI required for matching</li>
                </ul>
            </x-ts-card>
            <x-ts-card class="p-6 ring-brand bg-slate-50">
                <div class="mb-2 text-emerald-700"><x-ts-icon name="shield-check" class="h-6 w-6"/></div>
                <h3 class="font-semibold">Compliance Mindset</h3>
                <ul class="mt-2 text-sm space-y-2 text-slate-700">
                    <li>✓ Secure file exchange & e-sign</li>
                    <li>✓ Retention policies & exports</li>
                    <li>✓ Least-privilege permissions</li>
                    <li>✓ Transparent data processing</li>
                </ul>
            </x-ts-card>
            <x-ts-card class="p-6 ring-accent bg-slate-50">
                <div class="mb-2 text-orange-700"><x-ts-icon name="question-mark-circle" class="h-6 w-6"/></div>
                <h3 class="font-semibold">FAQs</h3>
                <ul class="mt-2 text-sm space-y-2 text-slate-700">
                    <li><span class="font-medium">Pricing?</span> Free to start; upgrade any time.</li>
                    <li><span class="font-medium">Who sees my data?</span> Only parties you invite to a deal room.</li>
                    <li><span class="font-medium">Cancel?</span> Anytime. No long-term contracts.</li>
                </ul>
            </x-ts-card>
        </div>
    </div>
</section>

{{-- ================= OPEN TERRITORIES TEASER (blur) ================= --}}
<section class="py-14 sm:py-16 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <x-ts-card class="p-6 bg-white/10 backdrop-blur ring-1 ring-white/15">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-white font-semibold">See open territories</h3>
                    <p class="text-slate-200 text-sm">Real-time openings by state, specialty & setting. Sign up to unblur.</p>
                </div>
                <x-ts-button as="a" href="{{ route('register') }}" icon="eye" class="btn-accent">
                    Reveal opportunities
                </x-ts-button>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 blur-sm">
                    <div class="text-slate-200 text-sm">Cardio Monitoring • Texas (Dallas–Fort Worth)</div>
                    <div class="text-slate-400 text-xs">Pipeline: $300k • Commission: 12%</div>
                </div>
                <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 blur-sm">
                    <div class="text-slate-200 text-sm">Wound Care • Florida (Tampa Bay)</div>
                    <div class="text-slate-400 text-xs">Pipeline: $180k • Commission: 10%</div>
                </div>
                <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 blur-sm">
                    <div class="text-slate-200 text-sm">Orthopedic Implants • Ohio</div>
                    <div class="text-slate-400 text-xs">Pipeline: $240k • Commission: 15%</div>
                </div>
            </div>
        </x-ts-card>
    </div>
</section>

{{-- ================= FINAL CTA ================= --}}
<section class="relative py-14 sm:py-16 bg-white">
    <div class="relative mx-auto max-w-5xl rounded-3xl ring-1 ring-slate-200 bg-slate-50 p-6 sm:p-10 text-center shadow-lg">
        <h3 class="text-2xl sm:text-3xl font-bold text-slate-900">Start free. Prove value fast.</h3>
        <p class="mt-2 text-slate-600">
            Create your profile, target precisely, collaborate in a deal room & e-sign — all in one platform.
        </p>
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt" class="w-full btn-brand">
                Create your free account
            </x-ts-button>
            <x-ts-button as="a" href="{{ route('manufacturers') }}" size="lg" icon="play" class="w-full btn-accent outline">
                See how it works
            </x-ts-button>
        </div>
        <p class="mt-3 text-xs text-slate-500">Risk-free trial • No credit card • Cancel anytime</p>
    </div>
</section>




</div>
</x-guest-layout>
