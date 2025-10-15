<x-ts-card class="relative ring-brand overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
    <x-slot name="header" class="font-semibold">Discover HubConnect</x-slot>

    <div class="aspect-video w-full overflow-hidden rounded-lg ring-1 ring-slate-200 bg-black">
        <iframe
            class="w-full h-full"
            src="{{ $videoUrl }}?rel=0&modestbranding=1"
            title="HubConnect overview"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
        ></iframe>
    </div>

    <p class="mt-2 text-xs text-slate-500">
        A quick tour of how manufacturers and distributors connect on HubConnect.
    </p>
</x-ts-card>
