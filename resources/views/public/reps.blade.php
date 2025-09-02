{{-- resources/views/public/reps.blade.php --}}
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
                <div class="flex items-center gap-3">
                    <x-ts-button as="a" href="{{ route('login') }}" variant="ghost" class="hidden sm:inline-flex">
                        Sign in
                    </x-ts-button>
                    <x-ts-button as="a" href="{{ route('register') }}" icon="rocket-launch" class="shadow-lg shadow-indigo-500/20">
                        Get started free
                    </x-ts-button>
                </div>
            </div>
        </div>
    </header>

    {{-- ================= HERO ================= --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 lg:py-28 grid lg:grid-cols-12 items-center gap-12">
            <div class="lg:col-span-7">
                <x-ts-badge class="mb-3">For Distributors & Independent Sales Reps</x-ts-badge>
                <h1 class="text-4xl/tight sm:text-5xl/tight font-extrabold tracking-tight">
                    Add high‑fit device lines to your territory
                    <br>
                    <span class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 bg-clip-text text-transparent">
                        with transparent terms and less admin
                    </span>
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    HubConnect matches you with verified manufacturers that fit your specialty and territory.
                    Gain priority access to new lines, keep commissions and pipeline organized, and build trust
                    with clear performance visibility.
                    <span class="font-semibold text-slate-900">Free to start. Cancel anytime.</span>
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt">
                        Create My Rep Profile
                    </x-ts-button>
                    <x-ts-button as="a" href="#features" size="lg" variant="secondary">
                        See How It Helps Me
                    </x-ts-button>
                </div>
            </div>
            <div class="lg:col-span-5">
                <x-ts-card class="p-6 shadow-xl ring-1 ring-slate-200/60">
                    <h3 class="text-base font-semibold mb-4">Opportunities that fit your book</h3>
                    <div class="rounded-xl border bg-gradient-to-br from-white to-slate-50 p-4">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">12</div>
                                <div class="text-xs text-slate-500">New Line Invites</div>
                            </x-ts-card>
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">$74k</div>
                                <div class="text-xs text-slate-500">Active Pipeline</div>
                            </x-ts-card>
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">6</div>
                                <div class="text-xs text-slate-500">Manufacturers</div>
                            </x-ts-card>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-center">
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">8.0%</div>
                                <div class="text-xs text-slate-500">Avg. Commission</div>
                            </x-ts-card>
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">4</div>
                                <div class="text-xs text-slate-500">Open Tasks</div>
                            </x-ts-card>
                            <x-ts-card class="p-3">
                                <div class="text-2xl font-bold">92%</div>
                                <div class="text-xs text-slate-500">Territory Fit</div>
                            </x-ts-card>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Example data. Metrics update as you accept lines and log activity.</p>
                </x-ts-card>
            </div>
        </div>
    </section>

    {{-- ================= PAIN / SOLUTION ================= --}}
    <section class="py-16 lg:py-24 border-t bg-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold sm:text-4xl">Spend more time selling, less time searching</h2>
            <p class="mt-3 text-lg text-slate-600">We remove the busywork so you can focus on relationships and revenue.</p>
        </div>
        <div class="mt-12 grid sm:grid-cols-2 gap-8 max-w-6xl mx-auto px-4">
            <x-ts-card class="p-6">
                <h3 class="font-semibold">The Hard Way</h3>
                <ul class="mt-2 text-sm text-slate-600 space-y-2">
                    <li>✗ Cold outreach to misaligned products</li>
                    <li>✗ Confusing commission terms and late updates</li>
                    <li>✗ Juggling spreadsheets for deals and stock</li>
                    <li>✗ Little visibility with manufacturers</li>
                </ul>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">With HubConnect</h3>
                <ul class="mt-2 text-sm text-slate-600 space-y-2">
                    <li>✓ Matches by territory, specialty, and fit</li>
                    <li>✓ Clear commission rules and payout tracking</li>
                    <li>✓ Built‑in pipeline, tasks, and consignment tracking</li>
                    <li>✓ Shared dashboards that build trust</li>
                </ul>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= FEATURES ================= --}}
    <section id="features" class="py-16 lg:py-24">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold sm:text-4xl">Features built for reps & distributors</h2>
            <p class="mt-3 text-lg text-slate-600">Expand your portfolio, keep deals organized, and get paid accurately.</p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto px-4">
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Priority Access</h3>
                <p class="mt-2 text-sm text-slate-600">See new device lines that match your territory and specialty first.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Transparent Commissions</h3>
                <p class="mt-2 text-sm text-slate-600">Know the rules up front. Track tiers, splits, approvals, and payouts.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Pipeline & Tasks</h3>
                <p class="mt-2 text-sm text-slate-600">Manage leads, notes, and follow‑ups without spreadsheets.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Stock & Consignment</h3>
                <p class="mt-2 text-sm text-slate-600">Monitor units by account and location with low‑stock alerts.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Manufacturer Profiles</h3>
                <p class="mt-2 text-sm text-slate-600">Work with verified companies and clear product documentation.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Coverage Insights</h3>
                <p class="mt-2 text-sm text-slate-600">Show where you excel and win more lines with proof.</p>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section class="py-16 lg:py-24 bg-slate-50 border-t">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold sm:text-4xl">How it works</h2>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4 max-w-7xl mx-auto px-4">
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">1</x-ts-badge>
                <h3 class="mt-3 font-semibold">Create Your Profile</h3>
                <p class="mt-1 text-sm text-slate-600">Set your specialties, territories, and target accounts.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">2</x-ts-badge>
                <h3 class="mt-3 font-semibold">Get Matched</h3>
                <p class="mt-1 text-sm text-slate-600">Receive invites from manufacturers with lines that fit.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">3</x-ts-badge>
                <h3 class="mt-3 font-semibold">Sell Together</h3>
                <p class="mt-1 text-sm text-slate-600">Share collateral, track pipeline, and align on goals.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">4</x-ts-badge>
                <h3 class="mt-3 font-semibold">Track & Get Paid</h3>
                <p class="mt-1 text-sm text-slate-600">Commissions, stock, and performance—fully transparent.</p>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="relative overflow-hidden py-16">
        <div class="mx-auto max-w-5xl rounded-3xl border bg-white/70 p-8 sm:p-12 text-center shadow-lg backdrop-blur">
            <h3 class="text-2xl font-bold sm:text-3xl">Grow your book with better lines</h3>
            <p class="mt-2 text-slate-600">Join HubConnect free and get matched with manufacturers that fit your territory.</p>
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-center gap-3">
                <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt">
                    Create My Rep Profile
                </x-ts-button>
                <x-ts-button as="a" href="{{ route('contact') }}" size="lg" variant="secondary">
                    Talk to us
                </x-ts-button>
            </div>
            <p class="mt-3 text-xs text-slate-500">No fees to join • Keep your data • Cancel anytime</p>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="border-t bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-slate-900"></div>
                <span class="text-sm font-semibold">HubConnect</span>
            </div>
            <div class="text-sm text-slate-500">© {{ date('Y') }} HubConnect. All rights reserved.</div>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('terms') }}" class="text-slate-600 hover:text-slate-900">Terms</a>
                <a href="{{ route('privacy') }}" class="text-slate-600 hover:text-slate-900">Privacy</a>
            </div>
        </div>
    </footer>
</div>
</x-guest-layout>
