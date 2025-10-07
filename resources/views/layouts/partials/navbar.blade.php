{{-- resources/views/partials/public-navbar.blade.php --}}
<div x-data="{ mobileOpen:false }">
    <header class="sticky top-0 z-50 border-b bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60"
            style="border-color:var(--border)">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Brand --}}
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    {{-- Same logo as the app navbar --}}
                    <x-application-mark class="block h-9 w-auto" />
                    <span class="text-lg font-semibold tracking-tight" style="color:var(--ink)">HubConnect</span>
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="{{ route('landing') }}" class="text-slate-600 hover:text-[color:var(--brand-700)]">
                        Home
                    </a>
                    <a href="{{ route('how-it-works') }}" class="text-slate-600 hover:text-[color:var(--brand-700)]">
                        How it works
                    </a>
                    @if(Route::has('openings.index'))
                        <a href="{{ route('openings.index') }}" class="text-slate-600 hover:text-[color:var(--brand-700)]">
                            Jobs Board
                        </a>
                    @endif
                    <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-[color:var(--brand-700)]">
                        Pricing
                    </a>
                </nav>

                {{-- Desktop CTAs --}}
                <div class="hidden md:flex items-center gap-3">
                    <x-ts-button as="a" href="{{ route('login') }}" class="btn-accent outline">
                        Sign in
                    </x-ts-button>
                    <x-ts-button as="a" href="{{ route('register') }}" class="btn-brand shadow-lg">
                        Get started free
                    </x-ts-button>
                </div>

                {{-- Mobile trigger --}}
                <button
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden inline-flex items-center justify-center rounded-lg p-2 hover:bg-[var(--brand-50)] ring-1 ring-transparent hover:ring-[var(--brand-200)]"
                    aria-label="Open menu"
                >
                    <x-ts-icon name="bars-3" class="h-6 w-6 text-slate-700"/>
                </button>
            </div>
        </div>

        {{-- Mobile sheet --}}
        <div x-show="mobileOpen" x-transition.origin.top.left class="md:hidden border-t bg-white"
             style="border-color:var(--border)">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 space-y-2">
                <a href="{{ route('landing') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-[var(--brand-50)]">
                    Home
                </a>
                <a href="{{ route('how-it-works') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-[var(--brand-50)]">
                    How it works
                </a>
                @if(Route::has('openings.index'))
                    <a href="{{ route('openings.index') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-[var(--brand-50)]">
                        Jobs Board
                    </a>
                @endif
                <a href="{{ route('pricing') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-[var(--brand-50)]">
                    Pricing
                </a>

                <div class="flex gap-2 pt-2">
                    <x-ts-button as="a" href="{{ route('login') }}" class="flex-1 btn-accent outline">
                        Sign in
                    </x-ts-button>
                    <x-ts-button as="a" href="{{ route('register') }}" class="flex-1 btn-brand">
                        Start free
                    </x-ts-button>
                </div>
            </nav>
        </div>
    </header>
</div>
