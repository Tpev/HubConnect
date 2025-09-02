{{-- Public Footer --}}
<footer class="border-t border-emerald-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-8 sm:gap-10 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-2">
                    <div class="h-7 w-7 rounded-lg bg-gradient-to-br from-emerald-500 to-orange-500 ring-1 ring-emerald-200/60"></div>
                    <span class="text-sm font-semibold text-slate-900">HubConnect</span>
                </div>
                <p class="mt-3 text-sm text-slate-600">
                    Matchmaking, targeting & deal rooms for medical device manufacturers and healthcare distributors.
                </p>
            </div>

            <div class="md:mx-auto">
                <h4 class="text-sm font-semibold text-slate-900">Company</h4>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('pricing') }}" class="text-slate-600 hover:text-emerald-700">Pricing</a></li>
                    <li><a href="{{ route('contact') }}" class="text-slate-600 hover:text-emerald-700">Contact</a></li>
                    <li><a href="{{ route('security') }}" class="text-slate-600 hover:text-emerald-700">Security</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-slate-900">Legal</h4>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('terms') }}" class="text-slate-600 hover:text-emerald-700">Terms</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-slate-600 hover:text-emerald-700">Privacy</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col-reverse items-center justify-between gap-3 sm:flex-row">
            <p class="text-xs text-slate-500">© {{ date('Y') }} HubConnect. All rights reserved.</p>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <x-ts-icon name="shield-check" class="h-4 w-4 text-emerald-600"/>
                <span>Role-based access • Version history • Audit trail</span>
            </div>
        </div>
    </div>
</footer>
