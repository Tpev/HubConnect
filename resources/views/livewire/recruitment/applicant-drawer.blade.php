<div
    class="fixed inset-0 z-50"
    wire:key="drawer-{{$app->id}}"
    wire:keydown.escape.window="close"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" wire:click="close"></div>

    {{-- Panel --}}
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-xl ring-1 ring-[var(--border)] flex flex-col">
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-[var(--border)] flex items-center justify-between">
            <div class="min-w-0">
                <div class="text-xs font-semibold text-slate-500">Applicant</div>
                <div class="text-lg font-semibold text-[var(--ink)] truncate">{{ $app->candidate_name }}</div>
            </div>
            <button class="btn-brand outline text-sm" wire:click="close">Close</button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-5 overflow-y-auto">

            {{-- Contact --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] space-y-1">
                <div class="text-sm"><strong>Email:</strong> {{ $app->email }}</div>
                @if($app->phone)
                    <div class="text-sm"><strong>Phone:</strong> {{ $app->phone }}</div>
                @endif
                @if($app->location)
                    <div class="text-sm"><strong>Location:</strong> {{ $app->location }}</div>
                @endif
                <div class="text-xs text-slate-500">Applied {{ $app->created_at?->toDayDateTimeString() }}</div>
            </div>

            {{-- Resume --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] flex items-center justify-between">
                <div class="text-sm font-medium">Resume / CV</div>
                @if($cvUrl)
                    <a href="{{ $cvUrl }}" class="btn-brand outline text-sm">Download</a>
                @else
                    <span class="text-sm text-slate-500">Not provided</span>
                @endif
            </div>

            {{-- Status & Score --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-lg border-slate-300">
                            <option value="new">New</option>
                            <option value="shortlisted">Shortlisted</option>
                            <option value="rejected">Rejected</option>
                            <option value="hired">Hired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Score</label>
                        <input type="number" step="0.01" wire:model="score" class="w-full rounded-lg border-slate-300" placeholder="e.g. 85.5" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button class="btn-brand" wire:click="updateStatus">Save changes</button>
                </div>
            </div>

            {{-- Roleplay invite --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] space-y-3">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium">Roleplay invite</div>
                    @if($app->invite_token)
                        <span class="chip-accent">Invited {{ $app->invited_at?->diffForHumans() }}</span>
                    @else
                        <span class="text-xs text-slate-500">Not invited</span>
                    @endif
                </div>

                @if($app->invite_token && $inviteUrl)
                    <div class="text-xs break-all ring-1 ring-[var(--border)] rounded-lg p-2 bg-[var(--panel-soft)]">
                        {{ $inviteUrl }}
                    </div>
                @endif

                <div class="flex flex-wrap gap-2">
                    @if(!$app->invite_token)
                        <button class="btn-accent" wire:click="sendInvite">Send invite</button>
                    @else
                        <button class="btn-brand outline" wire:click="regenerateInvite">Regenerate</button>
                        <button class="btn-accent outline" wire:click="removeInvite">Remove</button>
                    @endif
                </div>
            </div>

            {{-- Cover letter --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)]">
                <div class="text-sm font-semibold mb-2">Cover letter</div>
                @if($app->cover_letter)
                    <div class="whitespace-pre-line text-sm text-slate-700">{{ $app->cover_letter }}</div>
                @else
                    <div class="text-sm text-slate-500">No cover letter provided.</div>
                @endif
            </div>

        </div>
    </div>
</div>
