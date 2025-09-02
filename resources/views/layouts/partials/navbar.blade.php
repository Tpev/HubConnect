{{-- Public Navbar --}}
<div x-data="{ mobileOpen:false }">
<header class="sticky top-0 z-50 border-b border-emerald-100 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-orange-500 ring-1 ring-emerald-200/60"></div>
                <span class="text-lg font-semibold tracking-tight text-slate-900">HubConnect</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#why" class="text-slate-600 hover:text-emerald-700">Why</a>
                <a href="#features" class="text-slate-600 hover:text-emerald-700">Features</a>
                <a href="#spotlight" class="text-slate-600 hover:text-emerald-700">Targeting & Deal Room</a>
                <a href="#how" class="text-slate-600 hover:text-emerald-700">How it works</a>
                <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-emerald-700">Pricing</a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <x-ts-button as="a" href="{{ route('login') }}" class="btn-accent outline">
                    Sign in
                </x-ts-button>
                <x-ts-button as="a" href="{{ route('register') }}" icon="rocket-launch" class="btn-brand shadow-lg shadow-emerald-500/20">
                    Get started free
                </x-ts-button>
            </div>

            {{-- Mobile trigger --}}
            <button
                @click="mobileOpen = !mobileOpen"
                class="md:hidden inline-flex items-center justify-center rounded-lg p-2 hover:bg-emerald-50 ring-1 ring-transparent hover:ring-emerald-100"
                aria-label="Open menu"
            >
                <x-ts-icon name="bars-3" class="h-6 w-6 text-slate-700"/>
            </button>
        </div>
    </div>

    {{-- Mobile sheet --}}
    <div x-show="mobileOpen" x-transition.origin.top.left class="md:hidden border-t border-emerald-100 bg-white">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 space-y-2">
            <a href="#why" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-emerald-50">Why</a>
            <a href="#features" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-emerald-50">Features</a>
            <a href="#spotlight" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-emerald-50">Targeting & Deal Room</a>
            <a href="#how" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-emerald-50">How it works</a>
            <a href="{{ route('pricing') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-emerald-50">Pricing</a>
            <div class="flex gap-2 pt-2">
                <x-ts-button as="a" href="{{ route('login') }}" class="flex-1 btn-accent outline">Sign in</x-ts-button>
                <x-ts-button as="a" href="{{ route('register') }}" class="flex-1 btn-brand" icon="rocket-launch">Start free</x-ts-button>
            </div>
        </nav>
    </div>
</header>
</div>
