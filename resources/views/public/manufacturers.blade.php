{{-- resources/views/public/manufacturers.blade.php --}}
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
                <x-ts-badge class="mb-3">For Medical Device Manufacturers</x-ts-badge>
                <h1 class="text-4xl/tight sm:text-5xl/tight font-extrabold tracking-tight">
                    Expand your sales network <br>
                    <span class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 bg-clip-text text-transparent">
                        without the risk or guesswork
                    </span>
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    HubConnect helps manufacturers reach qualified distributors and sales reps, manage commissions 
                    and stock in one place, and scale market coverage with full visibility. 
                    <span class="font-semibold text-slate-900">Faster launches. Lower risk. More sales.</span>
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt">
                        Start Free
                    </x-ts-button>
                    <x-ts-button as="a" href="#features" size="lg" variant="secondary">
                        See Features
                    </x-ts-button>
                </div>
            </div>
            <div class="lg:col-span-5">
                <x-ts-card class="p-6 shadow-xl ring-1 ring-slate-200/60">
                    <h3 class="text-base font-semibold mb-4">Your dashboard, simplified</h3>
                    <img src="https://via.placeholder.com/400x260" alt="Manufacturer dashboard preview" class="rounded-lg border">
                </x-ts-card>
            </div>
        </div>
    </section>

    {{-- ================= PAIN / SOLUTION ================= --}}
    <section class="py-16 lg:py-24 border-t bg-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold sm:text-4xl">The challenge manufacturers face</h2>
            <p class="mt-3 text-lg text-slate-600">Finding reliable distributors and reps is slow, risky, and hard to track. HubConnect changes that.</p>
        </div>
        <div class="mt-12 grid sm:grid-cols-2 gap-8 max-w-6xl mx-auto px-4">
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Old Way</h3>
                <ul class="mt-2 text-sm text-slate-600 space-y-2">
                    <li>✗ Endless calls and emails to find partners</li>
                    <li>✗ No visibility on rep activity or coverage</li>
                    <li>✗ Manual spreadsheets for commissions</li>
                    <li>✗ Risky contracts without proof of performance</li>
                </ul>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">With HubConnect</h3>
                <ul class="mt-2 text-sm text-slate-600 space-y-2">
                    <li>✓ Instantly match with qualified reps & distributors</li>
                    <li>✓ Real-time visibility on territories & pipeline</li>
                    <li>✓ Built-in commission & stock tracking</li>
                    <li>✓ Scale sales with lower risk, faster</li>
                </ul>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= FEATURES ================= --}}
    <section id="features" class="py-16 lg:py-24">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold sm:text-4xl">Features built for manufacturers</h2>
            <p class="mt-3 text-lg text-slate-600">Everything you need to launch faster, scale smarter, and stay in control.</p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto px-4">
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Smart Matching</h3>
                <p class="mt-2 text-sm text-slate-600">Find reps and distributors by specialty and territory fit.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Commission Tracking</h3>
                <p class="mt-2 text-sm text-slate-600">Automate payouts, tiers, and splits — no spreadsheets needed.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Territory Coverage</h3>
                <p class="mt-2 text-sm text-slate-600">Visual dashboards show exactly where you’re covered — and where you’re not.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Stock Monitoring</h3>
                <p class="mt-2 text-sm text-slate-600">Track units by rep and distributor in real time.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Performance Reports</h3>
                <p class="mt-2 text-sm text-slate-600">Get clarity on who’s delivering results and where to invest more.</p>
            </x-ts-card>
            <x-ts-card class="p-6">
                <h3 class="font-semibold">Scalable Onboarding</h3>
                <p class="mt-2 text-sm text-slate-600">Bring new reps and distributors on quickly with zero friction.</p>
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
                <h3 class="mt-3 font-semibold">List Your Device</h3>
                <p class="mt-1 text-sm text-slate-600">Add products, define territories, and set commission rules.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">2</x-ts-badge>
                <h3 class="mt-3 font-semibold">Match with Reps</h3>
                <p class="mt-1 text-sm text-slate-600">Get introduced to verified sales partners who fit your needs.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">3</x-ts-badge>
                <h3 class="mt-3 font-semibold">Track Activity</h3>
                <p class="mt-1 text-sm text-slate-600">Stay in control with real-time dashboards on coverage and pipeline.</p>
            </x-ts-card>
            <x-ts-card class="p-5 text-center">
                <x-ts-badge variant="primary">4</x-ts-badge>
                <h3 class="mt-3 font-semibold">Scale Sales</h3>
                <p class="mt-1 text-sm text-slate-600">Grow your sales network faster, with less risk and more insight.</p>
            </x-ts-card>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="relative overflow-hidden py-16">
        <div class="mx-auto max-w-5xl rounded-3xl border bg-white/70 p-8 sm:p-12 text-center shadow-lg backdrop-blur">
            <h3 class="text-2xl font-bold sm:text-3xl">Ready to expand your sales network?</h3>
            <p class="mt-2 text-slate-600">Join HubConnect today and start connecting with verified distributors & reps.</p>
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-center gap-3">
                <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt">
                    Start Free
                </x-ts-button>
                <x-ts-button as="a" href="{{ route('contact') }}" size="lg" variant="secondary">
                    Talk to us
                </x-ts-button>
            </div>
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
