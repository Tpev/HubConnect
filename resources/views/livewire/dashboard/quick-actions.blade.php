<x-ts-card class="relative ring-brand overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
    <x-slot name="header" class="font-semibold">Quick actions</x-slot>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('companies.profile.edit', $team) }}"
           class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
            Edit company profile
        </a>

        <a href="{{ route('companies.intent.edit', $team) }}"
           class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700">
            Update what we’re looking for
        </a>

        <a href="{{ route('companies.index') }}"
           class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
            Explore companies
        </a>

        <a href="{{ route('employer.openings') }}"
           class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
            Manage openings
        </a>

        <a href="{{ route('employer.openings.create') }}"
           class="text-xs rounded-md px-2.5 py-1.5 ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700">
            Post a job
        </a>
    </div>
</x-ts-card>
