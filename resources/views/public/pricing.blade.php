{{-- resources/views/public/pricing.blade.php --}}
<x-guest-layout>
<div class="relative min-h-screen bg-slate-50" x-data="pricingToggle()">

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
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center">
            <x-ts-badge class="mb-3">Pricing</x-ts-badge>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                Simple pricing that scales with your sales
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Start free. Upgrade when you need advanced analytics and team controls.
                No setup fees. Cancel anytime.
            </p>

            {{-- Billing toggle (functional) --}}
            <div class="mt-6 inline-flex items-center gap-3 rounded-full border bg-white px-3 py-2 text-sm shadow-sm">
                <span :class="yearly ? 'text-slate-900 font-medium' : 'text-slate-500'">Yearly</span>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="peer sr-only" x-model="yearly">
                    <span class="h-5 w-10 rounded-full bg-slate-200 peer-checked:bg-indigo-500 relative transition">
                        <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                    </span>
                </label>
                <span :class="yearly ? 'text-slate-500' : 'text-slate-900 font-medium'">Monthly</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                <template x-if="yearly">
                    <span>Save about 17% with yearly billing.</span>
                </template>
                <template x-if="!yearly">
                    <span>Switch to yearly to save ~17%.</span>
                </template>
            </p>
        </div>
    </section>

    {{-- ================= PLANS ================= --}}
    <section class="pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-2">
                {{-- Column: Manufacturers --}}
                <div>
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">For Manufacturers</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        {{-- Free --}}
                        <x-ts-card class="relative overflow-hidden p-6">
                            <x-ts-badge class="absolute right-3 top-3" variant="success">Free</x-ts-badge>
                            <h3 class="text-lg font-semibold">Free</h3>
                            <div class="mt-1 text-3xl font-extrabold">$0</div>
                            <ul class="mt-5 space-y-3 text-sm text-slate-700">
                                <li>✓ List up to 3 devices</li>
                                <li>✓ Territory & specialty matching</li>
                                <li>✓ Basic pipeline & coverage view</li>
                                <li>✓ Standard support</li>
                            </ul>
                            <x-ts-button as="a" href="{{ route('register') }}" class="mt-6 w-full" icon="rocket-launch">
                                Start Free
                            </x-ts-button>
                        </x-ts-card>

                        {{-- Premium --}}
                        <x-ts-card class="relative overflow-hidden p-6 ring-1 ring-indigo-200">
                            <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-indigo-500/10"></div>
                            <x-ts-badge class="absolute right-3 top-3" variant="primary">
                                <template x-if="yearly"><span>Yearly</span></template>
                                <template x-if="!yearly"><span>Monthly</span></template>
                            </x-ts-badge>
                            <h3 class="text-lg font-semibold">Premium</h3>

                            <div class="mt-1 flex items-end gap-2">
                                <div class="text-3xl font-extrabold" x-text="displayPrice('manufacturerPremium')"></div>
                                <div class="pb-1 text-slate-500 text-sm" x-text="displaySuffix()"></div>
                            </div>
                            <template x-if="yearly">
                                <p class="mt-1 text-xs font-medium text-emerald-600">~17% savings vs monthly</p>
                            </template>

                            <ul class="mt-5 space-y-3 text-sm text-slate-700">
                                <li>✓ Unlimited devices</li>
                                <li>✓ Commission & stock tracking</li>
                                <li>✓ Advanced coverage & performance analytics</li>
                                <li>✓ Team roles, SSO & audit logs</li>
                                <li>✓ Priority matching & support</li>
                            </ul>
                            <x-ts-button as="a" href="{{ route('register') }}" class="mt-6 w-full shadow-lg shadow-indigo-500/20" icon="bolt">
                                Upgrade to Premium
                            </x-ts-button>
                            <p class="mt-3 text-xs text-slate-500">
                                Need custom terms? <a href="{{ route('contact') }}" class="underline">Contact sales</a>.
                            </p>
                        </x-ts-card>
                    </div>
                </div>

                {{-- Column: Distributors / Reps --}}
                <div>
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">For Distributors & Sales Reps</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        {{-- Free --}}
                        <x-ts-card class="relative overflow-hidden p-6">
                            <x-ts-badge class="absolute right-3 top-3" variant="success">Free</x-ts-badge>
                            <h3 class="text-lg font-semibold">Free</h3>
                            <div class="mt-1 text-3xl font-extrabold">$0</div>
                            <ul class="mt-5 space-y-3 text-sm text-slate-700">
                                <li>✓ Access manufacturer listings</li>
                                <li>✓ Territory‑based matching</li>
                                <li>✓ Personal pipeline & tasks</li>
                                <li>✓ Standard support</li>
                            </ul>
                            <x-ts-button as="a" href="{{ route('register') }}" class="mt-6 w-full" variant="secondary">
                                Join Free
                            </x-ts-button>
                        </x-ts-card>

                        {{-- Pro (most popular) --}}
                        <x-ts-card class="relative overflow-hidden p-6 ring-1 ring-fuchsia-200">
                            <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-fuchsia-500/10"></div>
                            <x-ts-badge class="absolute right-3 top-3" variant="warning">
                                <template x-if="yearly"><span>Yearly • Save ~17%</span></template>
                                <template x-if="!yearly"><span>Most popular</span></template>
                            </x-ts-badge>
                            <h3 class="text-lg font-semibold">Pro</h3>

                            <div class="mt-1 flex items-end gap-2">
                                <div class="text-3xl font-extrabold" x-text="displayPrice('repPro')"></div>
                                <div class="pb-1 text-slate-500 text-sm" x-text="displaySuffix()"></div>
                            </div>

                            <ul class="mt-5 space-y-3 text-sm text-slate-700">
                                <li>✓ Priority access to new device lines</li>
                                <li>✓ Commission tracking & exports</li>
                                <li>✓ Territory insights & coverage proof</li>
                                <li>✓ Collaboration with manufacturers</li>
                                <li>✓ Premium support</li>
                            </ul>
                            <x-ts-button as="a" href="{{ route('register') }}" class="mt-6 w-full shadow-lg shadow-fuchsia-500/20" icon="arrow-right">
                                Go Pro
                            </x-ts-button>
                        </x-ts-card>
                    </div>
                </div>
            </div>

            {{-- Reassurance row --}}
            <div class="mt-8 text-center text-xs text-slate-500">
                Prices shown are examples; configure billing in your admin. No setup fees. Cancel anytime.
            </div>
        </div>
    </section>

    {{-- ================= COMPARE (what’s included) ================= --}}
    <section class="bg-white py-16 lg:py-24 border-t">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl sm:text-3xl font-bold">What’s included</h2>
                <p class="mt-3 text-slate-600">Everything you need to match, sell, and measure performance.</p>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="adjustments-horizontal" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Smart Matching</h3>
                    <p class="mt-1 text-sm text-slate-600">Specialty + territory routing for high‑fit partnerships.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="banknotes" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Commission Tracking</h3>
                    <p class="mt-1 text-sm text-slate-600">Tiers, splits, approvals, and exports to payroll.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="cube" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Stock & Consignment</h3>
                    <p class="mt-1 text-sm text-slate-600">Track units by rep, lot, and location with alerts.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="map" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Coverage Analytics</h3>
                    <p class="mt-1 text-sm text-slate-600">Spot gaps & overlaps, redeploy with confidence.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="chart-bar" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Performance Reports</h3>
                    <p class="mt-1 text-sm text-slate-600">Dashboards by rep, device, account, and territory.</p>
                </x-ts-card>
                <x-ts-card class="p-6">
                    <div class="mb-2 text-indigo-600"><x-ts-icon name="shield-check" class="h-6 w-6"/></div>
                    <h3 class="font-semibold">Enterprise Controls</h3>
                    <p class="mt-1 text-sm text-slate-600">Roles, SSO, audit logs—ready when you scale.</p>
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
                    ['q' => 'Is HubConnect really free to start?', 'a' => 'Yes. Both Manufacturers and Distributors/Sales Reps can start on the Free plan. Upgrade only if you need advanced analytics, commission & stock tracking at scale, and enterprise controls.'],
                    ['q' => 'Can I cancel anytime?', 'a' => 'Absolutely. There are no long‑term contracts on self‑serve plans. You can cancel from your billing settings.'],
                    ['q' => 'Do you charge setup fees?', 'a' => 'No setup fees. Premium/Pro plans are billed monthly or yearly.'],
                    ['q' => 'What payment methods are accepted?', 'a' => 'Major credit/debit cards. Invoicing is available for annual Enterprise agreements—contact sales.'],
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

            <div class="mt-8 text-center">
                <x-ts-button as="a" href="{{ route('contact') }}" variant="secondary" icon="chat-bubble-left-right">
                    Still have questions? Talk to us
                </x-ts-button>
            </div>
        </div>
    </section>

    {{-- ================= FINAL CTA ================= --}}
    <section class="relative overflow-hidden py-16">
        <div class="mx-auto max-w-5xl rounded-3xl border bg-white/70 p-8 sm:p-12 text-center shadow-lg backdrop-blur">
            <h3 class="text-2xl font-bold sm:text-3xl">Start free. Upgrade when it pays off.</h3>
            <p class="mt-2 text-slate-600">Create your account and see qualified matches in minutes.</p>
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-ts-button as="a" href="{{ route('manufacturers') }}" size="lg" icon="bolt">
                    I’m a Manufacturer
                </x-ts-button>
                <x-ts-button as="a" href="{{ route('reps') }}" size="lg" variant="secondary">
                    I’m a Distributor / Rep
                </x-ts-button>
            </div>
            <p class="mt-3 text-xs text-slate-500">No credit card required • Cancel anytime</p>
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

{{-- ================= PRICING TOGGLE SCRIPT ================= --}}
<script>
function pricingToggle () {
    return {
        yearly: true, // default to yearly (shows savings)
        prices: {
            // raw numbers you control (USD)
            manufacturerPremium: { monthly: 499, yearly: 4990 }, // ~$415.83/mo equiv
            repPro:              { monthly: 59,  yearly: 590  }, // ~$49.17/mo equiv
        },
        formatUSD (n) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(n);
        },
        displayPrice (plan) {
            const val = this.yearly ? this.prices[plan].yearly : this.prices[plan].monthly;
            return this.formatUSD(val);
        },
        displaySuffix () {
            return this.yearly ? '/year' : '/month';
        }
    }
}
</script>
</x-guest-layout>
