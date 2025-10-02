<x-slot name="header">
    <h2 class="font-semibold text-xl">Admin — Company: {{ $company->name }}</h2>
</x-slot>

<x-container class="space-y-6">
    <x-admin.nav />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <ts-card class="lg:col-span-1">
            <ts-card.header class="font-semibold">Profile</ts-card.header>
            <ts-card.content class="space-y-3">
                <div><span class="text-sm text-slate-500">Slug:</span> {{ $company->slug }}</div>
                <div><span class="text-sm text-slate-500">Type:</span> {{ $company->company_type ?? '—' }}</div>
                <div><span class="text-sm text-slate-500">Country:</span> {{ $company->hq_country ?? '—' }}</div>
                <div><span class="text-sm text-slate-500">Website:</span>
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="underline">{{ $company->website }}</a>
                    @else
                        —
                    @endif
                </div>
                <div><span class="text-sm text-slate-500">Founded:</span> {{ $company->year_founded ?? '—' }}</div>
                <div><span class="text-sm text-slate-500">Headcount:</span> {{ $company->headcount ?? '—' }}</div>
                <div><span class="text-sm text-slate-500">Stage:</span> {{ $company->stage ?? '—' }}</div>
            </ts-card.content>
        </ts-card>

        <ts-card class="lg:col-span-2">
            <ts-card.header class="font-semibold">Summary</ts-card.header>
            <ts-card.content>
                <div class="prose max-w-none">
                    {{ $company->summary ?? '—' }}
                </div>
            </ts-card.content>
        </ts-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <ts-card>
            <ts-card.header class="font-semibold">Members ({{ $company->members->count() }})</ts-card.header>
            <ts-card.content>
                @if($company->members->count())
                    <ul class="divide-y">
                        @foreach($company->members as $m)
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $m->profile_photo_url }}" class="h-8 w-8 rounded-full" alt="">
                                    <div>
                                        <div class="font-medium">{{ $m->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $m->email }}</div>
                                    </div>
                                </div>
                                <ts-button href="{{ route('admin.users.show',$m->id) }}" size="sm" variant="soft">View</ts-button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-sm text-slate-500">No members.</div>
                @endif
            </ts-card.content>
        </ts-card>

        <ts-card>
            <ts-card.header class="font-semibold">Specialties</ts-card.header>
            <ts-card.content class="flex flex-wrap gap-2">
                @forelse($company->specialties as $sp)
                    <ts-badge>{{ $sp->name }} @if($sp->pivot?->depth) <span class="ml-1 text-[10px] opacity-70">({{ $sp->pivot->depth }})</span> @endif</ts-badge>
                @empty
                    <div class="text-sm text-slate-500">—</div>
                @endforelse
            </ts-card.content>
        </ts-card>

        <ts-card>
            <ts-card.header class="font-semibold">Certifications</ts-card.header>
            <ts-card.content class="space-y-2">
                @forelse($company->certifications as $cert)
                    <div class="flex items-center justify-between">
                        <div>{{ $cert->name }}</div>
                        @if($cert->pivot?->verified_at)
                            <ts-badge color="success">Verified</ts-badge>
                        @else
                            <ts-badge>Unverified</ts-badge>
                        @endif
                    </div>
                @empty
                    <div class="text-sm text-slate-500">—</div>
                @endforelse
            </ts-card.content>
        </ts-card>

        <ts-card>
            <ts-card.header class="font-semibold">Intents</ts-card.header>
            <ts-card.content>
                @forelse($company->intents as $intent)
                    <div class="py-2 border-b last:border-0">
                        <div class="flex items-center justify-between">
                            <div class="font-medium">{{ $intent->type ?? 'Intent' }}</div>
                            <ts-badge @class([
                                'success' => $intent->status === 'active',
                                'warning' => $intent->status === 'pending',
                                'danger'  => $intent->status === 'blocked',
                            ])>{{ $intent->status }}</ts-badge>
                        </div>
                        @if($intent->notes)
                            <div class="text-sm text-slate-600 mt-1">{{ $intent->notes }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-sm text-slate-500">—</div>
                @endforelse
            </ts-card.content>
        </ts-card>
    </div>
</x-container>
