{{-- resources/views/public/how.blade.php --}}
<x-guest-layout>
<div class="relative min-h-screen bg-slate-50">

    {{-- ================= NAV ================= --}}
    <header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500"></div>
                    <span class="text-lg font-semibold tracking-tight">HubConnect</span>
                </a>
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="{{ route('manufacturers') }}" class="text-slate-600 hover:text-slate-900">For Manufacturers</a>
                    <a href="{{ route('reps') }}" class="text-slate-600 hover:text-slate-900">For Reps</a>
                    <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-slate-900">Pricing</a>
                </div>
                <div class="flex items-center gap-3">
                    <x-ts-button as="a" href="{{ route('login') }}" variant="ghost" class="hidden sm:inline-flex">Sign in</x-ts-button>
                    <x-ts-button as="a" href="{{ route('register') }}" icon="rocket-launch" class="shadow-lg shadow-indigo-500/20">Get started free</x-ts-button>
                </div>
            </div>
        </div>
    </header>

    {{-- ================= HERO ================= --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="max-w-3xl">
                <x-ts-badge class="mb-3">How it works</x-ts-badge>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                    From first match to repeat wins—<span class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 bg-clip-text text-transparent">without spreadsheets</span>
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    HubConnect aligns manufacturers and distributors/sales reps by specialty and territory, then gives both sides the tools to manage
                    commissions, stock, coverage, and performance in one place. Risk‑free to start.
                </p>
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-ts-button as="a" href="{{ route('manufacturers') }}" size="lg" icon="building-office-2" class="w-full">I’m a Manufacturer</x-ts-button>
                    <x-ts-button as="a" href="{{ route('reps') }}" size="lg" variant="secondary" icon="user-group" class="w-full">I’m a Distributor / Rep</x-ts-button>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= OVERVIEW STEPS ================= --}}
    <section class="bg-white border-y">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold sm:text-4xl">A simple 4‑step flow</h2>
                <p class="mt-3 text-slate-600">Built for how medical device sales actually happen.</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <x-ts-card class="p-6 text-center">
                    <x-ts-badge variant="primary">1</x-ts-badge>
                    <h3 class="mt-3 font-semibold">Create</h3>
                    <p class="mt-1 text-sm text-slate-600">Manufacturers list devices & terms. Reps define territory & specialty.</p>
                </x-ts-card>
                <x-ts-card class="p-6 text-center">
                    <x-ts-badge variant="primary">2</x-ts-badge>
                    <h3 class="mt-3 font-semibold">Match</h3>
                    <p class="mt-1 text-sm text-slate-600">Smart routing surfaces best‑fit partners—no guesswork.</p>
                </x-ts-card>
                <x-ts-card class="p-6 text-center">
                    <x-ts-badge variant="primary">3</x-ts-badge>
                    <h3 class="mt-3 font-semibold">Sell</h3>
                    <p class="mt-1 text-sm text-slate-600">Share collateral, track pipeline & tasks, keep momentum.</p>
                </x-ts-card>
                <x-ts-card class="p-6 text-center">
                    <x-ts-badge variant="primary">4</x-ts-badge>
                    <h3 class="mt-3 font-semibold">Measure</h3>
                    <p class="mt-1 text-sm text-slate-600">Commissions, stock, coverage, and performance—tracked.</p>
                </x-ts-card>
            </div>
        </div>
    </section>

    {{-- ================= DEMO / VIDEO PLACEHOLDER ================= --}}
    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-ts-card class="overflow-hidden border">
                <div class="grid lg:grid-cols-2">
                    <div class="p-6 lg:p-8">
                        <h3 class="text-xl font-semibold">See the workflow in action</h3>
                        <p class="mt-2 text-slate-600">
                            A quick walkthrough of creating a listing, matching with reps, and tracking commissions and coverage.
                        </p>
                        <ul class="mt-4 text-sm space-y-2 text-slate-600">
                            <li>✓ Device listing with structured specs</li>
                            <li>✓ Territory & specialty matching</li>
                            <li>✓ Shared pipeline & tasks</li>
                            <li>✓ Commission & stock tracking</li>
                        </ul>
                        <div class="mt-6 flex gap-3">
                            <x-ts-button as="a" href="{{ route('register') }}" icon="bolt">Start Free</x-ts-button>
                            <x-ts-button as="a" href="{{ route('contact') }}" variant="secondary" icon="chat-bubble-left-right">Talk to us</x-ts-button>
                        </div>
                    </div>
                    <div class="bg-slate-100 aspect-video lg:aspect-auto">
                        {{-- Replace with real embed/video when ready --}}
                        <div class="h-full w-full flex items-center justify-center text-slate-500">
                            <div class="text-center">
                                <div class="mx-auto h-16 w-16 rounded-full bg-white/70 border flex items-center justify-center">
                                    <x-ts-icon name="play" class="h-6 w-6"/>
                                </div>
                                <p class="mt-3 text-sm">Demo video placeholder</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= FOR MANUFACTURERS ================= --}}
    <section class="border-t bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5">
                    <x-ts-badge class="mb-2">Manufacturers</x-ts-badge>
                    <h3 class="text-2xl sm:text-3xl font-bold">Expand coverage with verified partners</h3>
                    <p class="mt-3 text-slate-600">
                        Reach qualified distributors & reps fast, set clear terms, and track execution in real time.
                    </p>
                    <div class="mt-6 space-y-3 text-sm text-slate-700">
                        <div class="flex gap-2"><x-ts-icon name="adjustments-horizontal" class="h-5 w-5 text-indigo-600"/> Smart matching by territory & specialty</div>
                        <div class="flex gap-2"><x-ts-icon name="banknotes" class="h-5 w-5 text-indigo-600"/> Commission rules, tiers & split approvals</div>
                        <div class="flex gap-2"><x-ts-icon name="cube" class="h-5 w-5 text-indigo-600"/> Stock & consignment tracking by rep and location</div>
                        <div class="flex gap-2"><x-ts-icon name="map" class="h-5 w-5 text-indigo-600"/> Coverage analytics to spot gaps & overlaps</div>
                        <div class="flex gap-2"><x-ts-icon name="chart-bar" class="h-5 w-5 text-indigo-600"/> Performance reports by device & territory</div>
                    </div>
                    <div class="mt-6">
                        <x-ts-button as="a" href="{{ route('manufacturers') }}" icon="arrow-right">See Manufacturer page</x-ts-button>
                    </div>
                </div>
                <div class="lg:col-span-7">
                    <x-ts-card class="p-6 ring-1 ring-slate-200/60">
                        <h4 class="text-base font-semibold mb-4">Manufacturer workflow snapshot</h4>
                        <div class="grid sm:grid-cols-3 gap-4 text-center">
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">18</div>
                                <div class="text-xs text-slate-500">New Matches</div>
                            </x-ts-card>
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">$240k</div>
                                <div class="text-xs text-slate-500">Pipeline</div>
                            </x-ts-card>
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">88%</div>
                                <div class="text-xs text-slate-500">Coverage</div>
                            </x-ts-card>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Example data. Live metrics update as reps engage and log activity.</p>
                    </x-ts-card>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FOR REPS ================= --}}
    <section class="border-t">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5 order-2 lg:order-1">
                    <x-ts-badge class="mb-2">Distributors & Sales Reps</x-ts-badge>
                    <h3 class="text-2xl sm:text-3xl font-bold">Add high‑fit lines and get paid accurately</h3>
                    <p class="mt-3 text-slate-600">
                        Priority access to products that fit your territory, with clear commissions and shared visibility.
                    </p>
                    <div class="mt-6 space-y-3 text-sm text-slate-700">
                        <div class="flex gap-2"><x-ts-icon name="sparkles" class="h-5 w-5 text-fuchsia-600"/> Priority invites for matching device lines</div>
                        <div class="flex gap-2"><x-ts-icon name="banknotes" class="h-5 w-5 text-fuchsia-600"/> Transparent commission & payout tracking</div>
                        <div class="flex gap-2"><x-ts-icon name="clipboard-document-check" class="h-5 w-5 text-fuchsia-600"/> Pipeline, notes & tasks—no spreadsheets</div>
                        <div class="flex gap-2"><x-ts-icon name="cube" class="h-5 w-5 text-fuchsia-600"/> Consignment & stock monitoring by account</div>
                        <div class="flex gap-2"><x-ts-icon name="presentation-chart-line" class="h-5 w-5 text-fuchsia-600"/> Coverage proof to win more lines</div>
                    </div>
                    <div class="mt-6">
                        <x-ts-button as="a" href="{{ route('reps') }}" variant="secondary" icon="arrow-right">See Rep page</x-ts-button>
                    </div>
                </div>
                <div class="lg:col-span-7 order-1 lg:order-2">
                    <x-ts-card class="p-6 ring-1 ring-slate-200/60">
                        <h4 class="text-base font-semibold mb-4">Rep workflow snapshot</h4>
                        <div class="grid sm:grid-cols-3 gap-4 text-center">
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">9</div>
                                <div class="text-xs text-slate-500">New Invites</div>
                            </x-ts-card>
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">$92k</div>
                                <div class="text-xs text-slate-500">Active Pipeline</div>
                            </x-ts-card>
                            <x-ts-card class="p-4">
                                <div class="text-2xl font-bold">7.8%</div>
                                <div class="text-xs text-slate-500">Avg. Commission</div>
                            </x-ts-card>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Example data. Actual numbers update as you accept lines and log deals.</p>
                    </x-ts-card>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SECURITY / TRUST ================= --}}
    <section class="border-t bg-white">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid md:grid-cols-3 gap-6">
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="shield-check" class="h-6 w-6"/></div>
                    <h4 class="font-semibold">Security & Compliance</h4>
                    <p class="mt-1 text-sm text-slate-600">SSO, roles & audit logs. Marketplace flows avoid PHI; guided setup when patient data is needed.</p>
                    <a href="{{ route('security') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">Learn more</a>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="sparkles" class="h-6 w-6"/></div>
                    <h4 class="font-semibold">Fast Onboarding</h4>
                    <p class="mt-1 text-sm text-slate-600">Invite your team, import reps via CSV, and go live in minutes.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="lifebuoy" class="h-6 w-6"/></div>
                    <h4 class="font-semibold">Support that scales</h4>
                    <p class="mt-1 text-sm text-slate-600">Email & in‑app support on Free. Priority support on paid tiers.</p>
                </x-ts-card>
            </div>
        </div>
    </section>

    {{-- ================= FAQ ================= --}}
    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-semibold sm:text-3xl">Frequently Asked Questions</h2>
            </div>

            @php
                $faqs = [
                    ['q' => 'Is HubConnect free to start?', 'a' => 'Yes. Both manufacturers and distributors/sales reps can start on the Free plan. Upgrade if you need advanced analytics, commission/stock tracking at scale, and enterprise controls.'],
                    ['q' => 'How do matches work?', 'a' => 'We route by specialty, territory, and profile signals so devices reach the best‑fit reps and distributors automatically.'],
                    ['q' => 'Can I invite my existing team?', 'a' => 'Yes. Invite or import via CSV, assign territories, and start tracking coverage and performance immediately.'],
                    ['q' => 'Do you handle PHI?', 'a' => 'Marketplace flows avoid PHI. If you enable patient workflows, we guide a compliant setup and controls.'],
                ];
            @endphp

            <x-ts-card class="mt-6 p-0 overflow-hidden divide-y">
                @foreach($faqs as $i => $item)
                    <details class="group open:!bg-slate-50/60">
                        <summary class="flex w-full items-center justify-between px-4 py-3 cursor-pointer">
                            <span class="text-[0.95rem] font-medium text-slate-900">{{ $item['q'] }}</span>
                            <x-ts-icon name="chevron-down" class="h-4 w-4 transition-transform duration-200 group-open:rotate-180"/>
                        </summary>
                        <div class="px-4 pb-4 text-sm leading-relaxed text-slate-600">
                            {{ $item['a'] }}
                        </div>
                    </details>
                @endforeach
            </x-ts-card>
        </div>
    </section>

    {{-- ================= FINAL CTA ================= --}}
    <section class="relative overflow-hidden py-16">
        <div class="mx-auto max-w-5xl rounded-3xl border bg-white/70 p-8 sm:p-12 text-center shadow-lg backdrop-blur">
            <h3 class="text-2xl font-bold sm:text-3xl">Start free. Prove value fast.</h3>
            <p class="mt-2 text-slate-600">Create your profile, get matches, and see coverage & pipeline in one place.</p>
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-ts-button as="a" href="{{ route('manufacturers') }}" size="lg" icon="bolt">I’m a Manufacturer</x-ts-button>
                <x-ts-button as="a" href="{{ route('reps') }}" size="lg" variant="secondary">I’m a Distributor / Rep</x-ts-button>
            </div>
            <p class="mt-3 text-xs text-slate-500">No credit card • Cancel anytime</p>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="border-t bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-7 w-7 rounded-lg bg-slate-900"></div>
                    <span class="text-sm font-semibold">HubConnect</span>
                </div>
                <div class="text-sm text-slate-500">© {{ date('Y') }} HubConnect. All rights reserved.</div>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-slate-900">Pricing</a>
                    <a href="{{ route('contact') }}" class="text-slate-600 hover:text-slate-900">Contact</a>
                    <a href="{{ route('security') }}" class="text-slate-600 hover:text-slate-900">Security</a>
                    <a href="{{ route('terms') }}" class="text-slate-600 hover:text-slate-900">Terms</a>
                    <a href="{{ route('privacy') }}" class="text-slate-600 hover:text-slate-900">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

</div>
</x-guest-layout>
