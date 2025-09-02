{{-- resources/views/how-it-works.blade.php --}}
<x-guest-layout>
    <div class="bg-[var(--panel-soft)] text-[color:var(--ink)]">

        {{-- ================= HERO ================= --}}
        <section class="py-14 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="badge-brand">How it works</span>
                    <h1 class="mt-3 text-3xl sm:text-4xl font-bold">
                        From first match to signed contract — fast.
                    </h1>
                    <p class="mt-3 text-base sm:text-lg text-[color:var(--ink-2)]">
                        Target the right partners, collaborate in a deal room, e-sign, and track coverage & commissions —
                        without leaving HubConnect.
                    </p>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <x-ts-button as="a" href="{{ route('register') }}" icon="bolt" size="lg" class="btn-brand">
                            Start free
                        </x-ts-button>
                        <x-ts-button as="a" href="{{ route('manufacturers') }}" icon="play" size="lg" class="btn-accent outline">
                            See a live preview
                        </x-ts-button>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-[color:var(--ink-2)]">
                        <span class="chip-brand">No credit card</span>
                        <span class="chip-accent">Cancel anytime</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= 4-STEP FLOW ================= --}}
        <section class="border-t" style="border-color:var(--border)">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="mx-auto max-w-5xl">
                    <h2 class="text-xl sm:text-2xl font-semibold text-center">Four clear steps</h2>
                    <p class="mt-2 text-center text-[color:var(--ink-2)]">
                        Built for speed, control, and auditability.
                    </p>

                    <div class="mt-10 grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-4">
                        {{-- Step 1 --}}
                        <div class="p-5 rounded-2xl bg-[var(--panel)] ring-brand shadow-sm h-full">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-lg grid place-items-center font-semibold ring-brand">1</div>
                                <div>
                                    <h3 class="font-semibold">Target & Match</h3>
                                    <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                        Filter by specialty, territory/ZIP, and key accounts. Build a shortlist that fits.
                                    </p>
                                    <ul class="mt-3 text-xs text-[color:var(--ink-2)] space-y-1 list-disc ml-4">
                                        <li>Specialty & geography filters</li>
                                        <li>Account alignment & gaps</li>
                                        <li>Blurred results for non-licensed teams</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="p-5 rounded-2xl bg-[var(--panel)] ring-brand shadow-sm h-full">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-lg grid place-items-center font-semibold ring-brand">2</div>
                                <div>
                                    <h3 class="font-semibold">Deal Room</h3>
                                    <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                        Centralize intros, diligence, commission proposals, and territory maps.
                                    </p>
                                    <ul class="mt-3 text-xs text-[color:var(--ink-2)] space-y-1 list-disc ml-4">
                                        <li>Private messaging & file sharing</li>
                                        <li>Commission & exclusivity proposals</li>
                                        <li>Approval checkpoints</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="p-5 rounded-2xl bg-[var(--panel)] ring-brand shadow-sm h-full">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-lg grid place-items-center font-semibold ring-brand">3</div>
                                <div>
                                    <h3 class="font-semibold">Contract & e-Sign</h3>
                                    <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                        Lock in terms and sign securely. Full version history & audit trail.
                                    </p>
                                    <ul class="mt-3 text-xs text-[color:var(--ink-2)] space-y-1 list-disc ml-4">
                                        <li>Standard or custom templates</li>
                                        <li>Legally binding e-sign</li>
                                        <li>Version history & audit trail</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="p-5 rounded-2xl bg-[var(--panel)] ring-brand shadow-sm h-full">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-lg grid place-items-center font-semibold ring-brand">4</div>
                                <div>
                                    <h3 class="font-semibold">Track & Grow</h3>
                                    <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                        Manage coverage, commissions, and consignment. See performance by territory and account.
                                    </p>
                                    <ul class="mt-3 text-xs text-[color:var(--ink-2)] space-y-1 list-disc ml-4">
                                        <li>Coverage maps & gaps</li>
                                        <li>Commission tracking</li>
                                        <li>Consignment & lot visibility</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick persona chips --}}
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                        <span class="chip-brand">Manufacturers</span>
                        <span class="chip-accent">Distributors & Reps</span>
                        <span class="chip-brand">Territories</span>
                        <span class="chip-accent">Key Accounts</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= TRUST / SECURITY ================= --}}
        <section class="border-t" style="border-color:var(--border)">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                <div class="mx-auto max-w-5xl">
                    <div class="p-6 sm:p-8 rounded-2xl bg-[var(--panel)] ring-brand shadow-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <div class="text-sm font-semibold">Security & Compliance</div>
                                <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                    Encryption in transit & at rest, role-based access, detailed audit logs on contracts & changes.
                                </p>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">Operational Control</div>
                                <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                    Territory approvals, exclusivity flags, commission rules — aligned to your operating model.
                                </p>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">Integrations</div>
                                <p class="mt-1 text-sm text-[color:var(--ink-2)]">
                                    Export deals & contacts. Connect to your CRM/BI when you’re ready.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FAQ ================= --}}
        <section class="border-t" style="border-color:var(--border)">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-xl sm:text-2xl font-semibold text-center">Frequently asked questions</h2>
                    <p class="mt-2 text-center text-[color:var(--ink-2)]">Short, practical answers so you can decide fast.</p>

                    <div class="mt-8 space-y-3">
                        <details class="group rounded-2xl bg-[var(--panel)] ring-brand p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between">
                                <span class="font-medium">Who is HubConnect for?</span>
                                <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                            </summary>
                            <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                Medical device <span class="font-medium">manufacturers</span> who want targeted coverage, and
                                <span class="font-medium">distributors/reps</span> seeking new lines & exclusive territories.
                            </p>
                        </details>

                        <details class="group rounded-2xl bg-[var(--panel)] ring-brand p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between">
                                <span class="font-medium">How do matches work?</span>
                                <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                            </summary>
                            <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                Filter by specialty, geography, and key accounts. We surface the best-fit shortlist. Open a deal room to collaborate.
                            </p>
                        </details>

                        <details class="group rounded-2xl bg-[var(--panel)] ring-brand p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between">
                                <span class="font-medium">Is it secure?</span>
                                <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                            </summary>
                            <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                Yes — encryption, RBAC, and full audit trails on contracts & configuration changes.
                            </p>
                        </details>

                        <details class="group rounded-2xl bg-[var(--panel)] ring-brand p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between">
                                <span class="font-medium">What does it cost?</span>
                                <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                            </summary>
                            <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                Distributors/Reps have a free tier. Manufacturers choose a monthly plan — see
                                <a href="{{ route('pricing') }}" class="text-[color:var(--brand-700)] underline-offset-2 hover:underline">pricing</a>.
                            </p>
                        </details>

                        <details class="group rounded-2xl bg-[var(--panel)] ring-brand p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between">
                                <span class="font-medium">Can I cancel anytime?</span>
                                <span class="text-[color:var(--ink-2)] group-open:rotate-180 transition">▾</span>
                            </summary>
                            <p class="mt-3 text-sm text-[color:var(--ink-2)]">
                                Yes. No lock-in. You can export your data before canceling.
                            </p>
                        </details>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FINAL CTA (no gradient) ================= --}}
        <section class="border-t" style="border-color:var(--border)">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
                <div class="mx-auto max-w-5xl text-center p-6 sm:p-10 rounded-3xl bg-[var(--panel)] ring-brand shadow-lg">
                    <h3 class="text-2xl sm:text-3xl font-bold">Start free. Prove value fast.</h3>
                    <p class="mt-2 text-[color:var(--ink-2)]">
                        Create your profile, target precisely, collaborate in a deal room & e-sign — all in one platform.
                    </p>
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ts-button as="a" href="{{ route('register') }}" size="lg" icon="bolt" class="btn-brand">
                            Create your free account
                        </x-ts-button>
                        <x-ts-button as="a" href="{{ route('manufacturers') }}" size="lg" icon="play" class="btn-accent outline">
                            See how it works
                        </x-ts-button>
                    </div>
                    <p class="mt-3 text-xs text-[color:var(--ink-2)]">Risk-free trial • No credit card • Cancel anytime</p>
                </div>
            </div>
        </section>

    </div>
</x-guest-layout>
