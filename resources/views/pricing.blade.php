{{-- resources/views/pricing.blade.php --}}
<x-guest-layout>
    <div class="bg-[var(--panel-soft)] text-[color:var(--ink)]" x-data="pricingUI()">

        {{-- ================= HERO ================= --}}
        <section class="py-14 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="badge-brand">Pricing</span>
                    <h1 class="mt-3 text-3xl sm:text-4xl font-bold">Simple plans. Built for both sides.</h1>
                    <p class="mt-3 text-base sm:text-lg text-[color:var(--ink-2)]">
                        Manufacturers grow targeted coverage. Distributors & reps win new device lines. Start free and upgrade anytime.
                    </p>

                    {{-- Billing toggle --}}
                    <div class="mt-6 inline-flex items-center gap-2 rounded-2xl ring-brand bg-[var(--panel)] p-1">
                        <button @click="setPeriod('monthly')"
                                :class="period==='monthly' ? 'btn-brand outline !py-2 !px-3' : 'chip-brand !py-2 !px-3'">
                            Monthly
                        </button>
                        <button @click="setPeriod('annual')"
                                :class="period==='annual' ? 'btn-brand outline !py-2 !px-3' : 'chip-brand !py-2 !px-3'">
                            Annual <span class="ml-2 badge-accent">Save 15%</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-[color:var(--ink-2)]">
                        <span x-show="period==='monthly'">Billed monthly. Cancel anytime.</span>
                        <span x-show="period==='annual'">Billed annually. 15% discount applied.</span>
                    </p>
                </div>
            </div>
        </section>

        {{-- ================= PRICING GRID (Uniform Cards) ================= --}}
        <section class="border-t" style="border-color:var(--border)">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

                {{-- Row title chips --}}
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <span class="chip-brand">Manufacturers</span>
                    <span class="chip-accent">Distributors & Reps</span>
                </div>

                {{-- Cards: perfectly aligned heights via flex layout --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    {{-- ========== Manufacturer — Starter ========== --}}
                    <div class="rounded-3xl bg-[var(--panel)] ring-brand shadow-sm p-6 sm:p-7 flex flex-col">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="badge-brand">Manufacturers</span>
                                <h3 class="mt-2 text-xl font-semibold">Starter</h3>
                                <p class="mt-1 text-sm text-[color:var(--ink-2)]">For early market entry</p>
                            </div>
                        </div>

                        {{-- Price block --}}
                        <div class="mt-5 rounded-2xl ring-brand p-4 bg-[var(--panel-soft)]">
                            <div class="text-4xl font-extrabold leading-none">
                                <span x-text="money('mfg_starter')"></span>
                            </div>
                            <div class="mt-1 text-xs text-[color:var(--ink-2)]">
                                <span x-show="period==='monthly'">per month</span>
                                <span x-show="period==='annual'">per month (annual)</span>
                            </div>
                            <div class="mt-1 text-[10px] text-[color:var(--ink-2)]" x-show="period==='annual'">
                                <span x-text="compare('mfg_starter')"></span> if paid monthly
                            </div>
                        </div>

                        <ul class="mt-5 text-sm text-[color:var(--ink-2)] space-y-2 list-disc ml-4">
                            <li>Up to 2 devices</li>
                            <li>Territory & specialty filters</li>
                            <li>Deal room + file sharing</li>
                            <li>Standard e-sign templates</li>
                        </ul>

                        <div class="mt-auto pt-6">
                            <x-ts-button as="a" href="{{ route('register') }}" class="btn-brand w-full" icon="bolt">
                                Get started
                            </x-ts-button>
                        </div>
                    </div>

                    {{-- ========== Manufacturer — Growth (Featured) ========== --}}
                    <div class="relative rounded-3xl bg-[var(--panel)] ring-brand shadow-sm p-6 sm:p-7 flex flex-col">
                        <div class="absolute -top-3 right-6">
                            <span class="badge-accent">Most popular</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="badge-brand">Manufacturers</span>
                                <h3 class="mt-2 text-xl font-semibold">Growth</h3>
                                <p class="mt-1 text-sm text-[color:var(--ink-2)]">Scale with control</p>
                            </div>
                        </div>

                        {{-- Price block --}}
                        <div class="mt-5 rounded-2xl ring-brand p-4 bg-[var(--panel-soft)]">
                            <div class="text-4xl font-extrabold leading-none">
                                <span x-text="money('mfg_growth')"></span>
                            </div>
                            <div class="mt-1 text-xs text-[color:var(--ink-2)]">
                                <span x-show="period==='monthly'">per month</span>
                                <span x-show="period==='annual'">per month (annual)</span>
                            </div>
                            <div class="mt-1 text-[10px] text-[color:var(--ink-2)]" x-show="period==='annual'">
                                <span x-text="compare('mfg_growth')"></span> if paid monthly
                            </div>
                        </div>

                        <ul class="mt-5 text-sm text-[color:var(--ink-2)] space-y-2 list-disc ml-4">
                            <li>Unlimited devices</li>
                            <li>Account alignment & coverage map</li>
                            <li>Custom e-sign templates</li>
                            <li>Commission rules & approvals</li>
                            <li>Consignment & lot visibility</li>
                        </ul>

                        <div class="mt-auto pt-6">
                            <x-ts-button as="a" href="{{ route('register') }}" class="btn-accent w-full" icon="bolt">
                                Choose Growth
                            </x-ts-button>
                        </div>
                    </div>

                    {{-- ========== Distributors — Free ========== --}}
                    <div class="rounded-3xl bg-[var(--panel)] ring-brand shadow-sm p-6 sm:p-7 flex flex-col">
                        <div>
                            <span class="badge-accent">Distributors & Reps</span>
                            <h3 class="mt-2 text-xl font-semibold">Free</h3>
                            <p class="mt-1 text-sm text-[color:var(--ink-2)]">Discover opportunities</p>
                        </div>

                        {{-- Price block --}}
                        <div class="mt-5 rounded-2xl ring-brand p-4 bg-[var(--panel-soft)]">
                            <div class="text-4xl font-extrabold leading-none">$0</div>
                            <div class="mt-1 text-xs text-[color:var(--ink-2)]">always free</div>
                        </div>

                        <ul class="mt-5 text-sm text-[color:var(--ink-2)] space-y-2 list-disc ml-4">
                            <li>Browse manufacturers</li>
                            <li>Specialty & territory filters</li>
                            <li>Limited deal rooms</li>
                            <li>Basic profile</li>
                        </ul>

                        <div class="mt-auto pt-6">
                            <x-ts-button as="a" href="{{ route('register') }}" class="btn-brand w-full" icon="user-plus">
                                Join free
                            </x-ts-button>
                        </div>
                    </div>

                    {{-- ========== Distributors — Pro ========== --}}
                    <div class="rounded-3xl bg-[var(--panel)] ring-brand shadow-sm p-6 sm:p-7 flex flex-col">
                        <div>
                            <span class="badge-accent">Distributors & Reps</span>
                            <h3 class="mt-2 text-xl font-semibold">Pro</h3>
                            <p class="mt-1 text-sm text-[color:var(--ink-2)]">Win more lines, faster</p>
                        </div>

                        {{-- Price block --}}
                        <div class="mt-5 rounded-2xl ring-brand p-4 bg-[var(--panel-soft)]">
                            <div class="text-4xl font-extrabold leading-none">
                                <span x-text="money('dist_pro')"></span>
                            </div>
                            <div class="mt-1 text-xs text-[color:var(--ink-2)]">
                                <span x-show="period==='monthly'">per month</span>
                                <span x-show="period==='annual'">per month (annual)</span>
                            </div>
                            <div class="mt-1 text-[10px] text-[color:var(--ink-2)]" x-show="period==='annual'">
                                <span x-text="compare('dist_pro')"></span> if paid monthly
                            </div>
                        </div>

                        <ul class="mt-5 text-sm text-[color:var(--ink-2)] space-y-2 list-disc ml-4">
                            <li>Unlimited deal rooms</li>
                            <li>Account alignment insights</li>
                            <li>Priority intro requests</li>
                            <li>Advanced profile & references</li>
                        </ul>

                        <div class="mt-auto pt-6">
                            <x-ts-button as="a" href="{{ route('register') }}" class="btn-accent w-full" icon="star">
                                Upgrade to Pro
                            </x-ts-button>
                        </div>
                    </div>
                </div>

                {{-- ================= Comparison (concise) ================= --}}
                <div class="mt-12 rounded-3xl bg-[var(--panel)] ring-brand p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg sm:text-xl font-semibold">What’s included</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[color:var(--ink-2)]">
                                    <th class="py-3 pr-4">Feature</th>
                                    <th class="py-3 pr-4">Manufacturers</th>
                                    <th class="py-3">Distributors & Reps</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:var(--border)">
                                <tr>
                                    <td class="py-3 pr-4">Specialty & territory filters</td>
                                    <td class="py-3 pr-4">✓</td>
                                    <td class="py-3">✓</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">Account alignment insights</td>
                                    <td class="py-3 pr-4">Growth</td>
                                    <td class="py-3">Pro</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">Deal rooms (messaging + files)</td>
                                    <td class="py-3 pr-4">Unlimited</td>
                                    <td class="py-3">Free: Limited · Pro: Unlimited</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">Commission rules & approvals</td>
                                    <td class="py-3 pr-4">Growth</td>
                                    <td class="py-3">—</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">Consignment & lot visibility</td>
                                    <td class="py-3 pr-4">Growth</td>
                                    <td class="py-3">—</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">E-sign templates</td>
                                    <td class="py-3 pr-4">Starter: Standard · Growth: Custom</td>
                                    <td class="py-3">Pro: Standard</td>
                                </tr>
                                <tr>
                                    <td class="py-3 pr-4">Support</td>
                                    <td class="py-3 pr-4">Email · Priority for Growth</td>
                                    <td class="py-3">Email</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-xs text-[color:var(--ink-2)]">
                        Need something bigger? <a href="{{ route('contact') ?? '#' }}" class="underline underline-offset-2">Contact us for Enterprise</a>.
                    </div>
                </div>

                {{-- ================= FAQ + Notes ================= --}}
                <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 rounded-3xl bg-[var(--panel)] ring-brand p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg sm:text-xl font-semibold">Frequently asked questions</h3>
                        <div class="mt-4 space-y-3">
                            <details class="group rounded-2xl ring-brand p-4 bg-[var(--panel)]">
                                <summary class="flex cursor-pointer list-none items-center justify-between">
                                    <span class="font-medium">Can I switch plans anytime?</span>
                                    <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                                </summary>
                                <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                    Yes. Upgrades are prorated; downgrades take effect on the next billing cycle.
                                </p>
                            </details>

                            <details class="group rounded-2xl ring-brand p-4 bg-[var(--panel)]">
                                <summary class="flex cursor-pointer list-none items-center justify-between">
                                    <span class="font-medium">Is there a free option?</span>
                                    <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                                </summary>
                                <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                    Yes. Distributors & reps can join for free and upgrade to Pro for more deal rooms and insights.
                                </p>
                            </details>

                            <details class="group rounded-2xl ring-brand p-4 bg-[var(--panel)]">
                                <summary class="flex cursor-pointer list-none items-center justify-between">
                                    <span class="font-medium">How do you handle security?</span>
                                    <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                                </summary>
                                <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                    Data is encrypted in transit & at rest, with role-based access and audit logs on contracts & settings.
                                </p>
                            </details>

                            <details class="group rounded-2xl ring-brand p-4 bg-[var(--panel)]">
                                <summary class="flex cursor-pointer list-none items-center justify-between">
                                    <span class="font-medium">What’s your refund policy?</span>
                                    <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                                </summary>
                                <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                    You can cancel anytime. We don’t offer refunds for partial billing periods.
                                </p>
                            </details>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-[var(--panel)] ring-brand p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg sm:text-xl font-semibold">Notes & Limits</h3>
                        <ul class="mt-3 text-sm text-[color:var(--ink-2)] space-y-2 list-disc ml-4">
                            <li>Annual pricing reflects a 15% discount vs monthly.</li>
                            <li>Reasonable-use limits apply to prevent abuse.</li>
                            <li>Enterprise options available on request (SSO, custom terms, security reviews).</li>
                        </ul>
                        <div class="mt-4">
                            <x-ts-button as="a" href="{{ route('register') }}" class="btn-brand w-full" icon="bolt">
                                Start free
                            </x-ts-button>
                        </div>
                    </div>
                </div>

                {{-- ================= Final CTA ================= --}}
                <div class="mt-12 text-center">
                    <p class="text-sm text-[color:var(--ink-2)]">Questions about pricing?</p>
                    <div class="mt-3 flex items-center justify-center gap-3">
                        <x-ts-button as="a" href="{{ route('register') }}" class="btn-brand" icon="bolt">
                            Create your free account
                        </x-ts-button>
                        <x-ts-button as="a" href="{{ route('contact') ?? '#' }}" class="btn-accent outline">
                            Contact sales
                        </x-ts-button>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- ================= Alpine Helpers ================= --}}
    <script>
        function pricingUI(){
            return {
                period: 'monthly',
                setPeriod(p){ this.period = p; },

                // Base MONTHLY prices (USD, excl. tax)
                baseMonthly: {
                    mfg_starter: 499.00,
                    mfg_growth:  899.00,
                    dist_pro:     49.99,
                },
                annualDiscount: 0.15, // 15% off annual

                // Money formatter (USD)
                fmt(n){
                    try {
                        return n.toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            minimumFractionDigits: (Number.isInteger(n) ? 0 : 2),
                            maximumFractionDigits: (Number.isInteger(n) ? 0 : 2),
                        });
                    } catch (_) {
                        // Fallback
                        const fixed = Number.isInteger(n) ? n.toFixed(0) : n.toFixed(2);
                        return '$' + fixed;
                    }
                },

                // Return formatted /mo price for current period
                money(plan){
                    const m = this.baseMonthly[plan];
                    if(this.period === 'monthly') return this.fmt(m);
                    const perMonthAnnual = m * (1 - this.annualDiscount);
                    return this.fmt(perMonthAnnual);
                },

                // Tiny compare line for annual view
                compare(plan){
                    const m = this.baseMonthly[plan];
                    return this.fmt(m) + '/mo';
                }
            }
        }
    </script>
</x-guest-layout>
