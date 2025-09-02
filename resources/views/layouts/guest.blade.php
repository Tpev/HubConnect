<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <tallstackui:script />

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <x-banner />

        {{-- Global toasts --}}
        <x-ts-toast />

        <div class="min-h-screen">
            {{-- Navbar partial --}}
            @include('layouts.partials.navbar')

            {{-- Optional page header slot (kept for pages that use it) --}}
            @if (isset($header))
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Page content --}}
            <main>
                {{ $slot }}
            </main>

            {{-- Footer partial --}}
            @include('layouts.partials.footer')
        </div>

        @stack('modals')

        @livewireScripts
        @stack('scripts')
    </body>
</html>
