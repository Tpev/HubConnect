<div
    x-data
    x-show="{{ $show ? 'true' : 'false' }}"
    x-transition.opacity
    class="fixed inset-0 z-50"
    wire:key="drawer-{{ $app->id }}"
    wire:keydown.escape.window="close"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" wire:click="close"></div>

    {{-- Panel --}}
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-xl ring-1 ring-[var(--border)] flex flex-col">
        {{-- Header --}}
        <div class="px-4 sm:px-5 py-4 border-b border-[var(--border)] flex items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="text-[10px] sm:text-xs font-semibold text-slate-500">Applicant</div>
                <div class="flex items-center gap-2">
                    <div class="text-lg sm:text-xl font-semibold text-[var(--ink)] truncate">
                        {{ $snap['name'] ?? $app->candidate_name ?? 'Candidate' }}
                    </div>

                    {{-- KYC badge (if any) --}}
                    @php
                        $kyc = $snap['kyc'] ?? null;
                        $kycMap = [
                            'approved'       => ['Verified','bg-emerald-50 text-emerald-700'],
                            'pending_review' => ['Pending','bg-amber-50 text-amber-700'],
                            'rejected'       => ['Rejected','bg-red-50 text-red-700'],
                            'draft'          => ['Draft','bg-slate-100 text-slate-700'],
                        ];
                        $kycChip = $kyc ? ($kycMap[$kyc] ?? null) : null;
                    @endphp
                    @if($kycChip)
                        <span class="px-2 py-0.5 rounded text-[10px] sm:text-xs {{ $kycChip[1] }}">{{ $kycChip[0] }}</span>
                    @endif
                </div>
            </div>

            <button class="btn-brand outline text-sm shrink-0" wire:click="close">Close</button>
        </div>

        {{-- Body --}}
        <div class="p-4 sm:p-5 space-y-5 overflow-y-auto">
            {{-- Summary / profile highlight --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1 min-w-0">
                    @if(!empty($snap['headline']))
                        <div class="text-sm font-medium text-[var(--ink)] truncate">{{ $snap['headline'] }}</div>
                    @endif
                    <div class="text-sm truncate">
                        <span class="text-slate-500">Email:</span>
                        <a href="mailto:{{ $snap['email'] }}" class="text-[var(--brand-700)] underline break-all">{{ $snap['email'] }}</a>
                    </div>
                    @if($snap['phone'])
                        <div class="text-sm">
                            <span class="text-slate-500">Phone:</span>
                            <a href="tel:{{ $snap['phone'] }}" class="text-[var(--brand-700)] underline">{{ $snap['phone'] }}</a>
                        </div>
                    @endif
                </div>
                <div class="space-y-1">
                    @if($snap['location'])
                        <div class="text-sm">
                            <span class="text-slate-500">Location:</span>
                            <span class="text-[var(--ink)]">{{ $snap['location'] }}</span>
                        </div>
                    @endif
                    @if(!is_null($snap['years']))
                        <div class="text-sm">
                            <span class="text-slate-500">Experience:</span>
                            <span class="text-[var(--ink)]">{{ $snap['years'] }} {{ \Illuminate\Support\Str::plural('year', (int)$snap['years']) }}</span>
                        </div>
                    @endif
                    <div class="text-[11px] sm:text-xs text-slate-500">Applied {{ $app->created_at?->diffForHumans() }}</div>
                </div>

                {{-- Skills --}}
                @if(count($snap['skills']) > 0)
                    <div class="sm:col-span-2">
                        <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1">Skills</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice($snap['skills'], 0, 12) as $skill)
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] sm:text-xs">{{ $skill }}</span>
                            @endforeach
                            @if(count($snap['skills']) > 12)
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[11px] sm:text-xs">
                                    +{{ count($snap['skills']) - 12 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Links --}}
                @if(count($snap['links']) > 0)
                    <div class="sm:col-span-2">
                        <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1">Links</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($snap['links'] as $lnk)
                                @php
                                    $label = $lnk['label'] ?? 'Link';
                                    $url   = $lnk['url'] ?? '#';
                                @endphp
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 text-[11px] sm:text-xs text-[var(--brand-700)] underline truncate max-w-[12rem]">
                                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M12.293 7.293a1 1 0 011.414 0l2 2a1 1 0 010 1.414l-5 5a3 3 0 01-4.243 0l-.586-.586a3 3 0 010-4.243l1.5-1.5a1 1 0 011.414 1.414l-1.5 1.5a1 1 0 000 1.414l.586.586a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-2-2a1 1 0 010-1.414z"/>
                                    </svg>
                                    <span class="truncate">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Resume --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="min-w-0">
                    <div class="text-sm font-medium">Resume / CV</div>
                    @if(!$cvUrl)
                        <div class="text-xs text-slate-500">Not provided</div>
                    @else
                        <div class="text-xs text-slate-500">Latest available CV</div>
                    @endif
                </div>
                <div class="shrink-0">
                    @if($cvUrl)
                        <a href="{{ $cvUrl }}" target="_blank" rel="noopener"
                           class="btn-brand outline text-sm w-full sm:w-auto text-center">Download</a>
                    @else
                        <span class="text-sm text-slate-500">—</span>
                    @endif
                </div>
            </div>

            {{-- Status & Score --}}
            <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-lg border-slate-300">
                            @foreach($statusOptions as $opt)
                                <option value="{{ $opt }}">{{ \Illuminate\Support\Str::headline($opt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] sm:text-xs font-semibold text-slate-500 mb-1">Score (0–100)</label>
                        <input type="number" step="1" min="0" max="100"
                               wire:model.lazy="score"
                               class="w-full rounded-lg border-slate-300" placeholder="e.g. 85" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-end">
                    <button class="btn-brand w-full sm:w-auto" wire:click="updateStatus">Save changes</button>
                </div>
            </div>

            {{-- Screening answers (NEW) --}}
            @if(!empty($answersDisplay))
                <div class="p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] space-y-3">
                    <div class="text-sm font-semibold">Screening answers</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($answersDisplay as $ans)
                            <div class="p-3 rounded-lg bg-white ring-1 ring-[var(--border)]">
                                <div class="text-[11px] sm:text-xs text-slate-500">{{ $ans['label'] }}</div>
                                <div class="text-sm text-[var(--ink)] mt-0.5">
                                    @if($ans['empty'])
                                        <span class="text-slate-400">Not provided</span>
                                    @else
                                        {{ $ans['value'] }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Roleplay invite (blurred & coming soon) --}}
            <div class="relative p-4 rounded-xl ring-1 ring-[var(--border)] bg-[var(--panel)] overflow-hidden">
                {{-- Blurred content (placeholder of future controls) --}}
                <div class="pointer-events-none select-none filter blur-sm opacity-60">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-sm font-medium">Roleplay invite</div>
                        <span class="px-2 py-0.5 rounded text-xs bg-indigo-50 text-indigo-700">Invited 2 days ago</span>
                    </div>
                    <div class="mt-2 text-xs ring-1 ring-[var(--border)] rounded-lg p-2 bg-[var(--panel-soft)] break-words">
                        https://example.com/roleplay/invite/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="btn-accent">Send invite</button>
                        <button class="btn-brand outline">Regenerate</button>
                        <button class="btn-accent outline">Remove</button>
                    </div>
                </div>

                {{-- Overlay --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white/60 backdrop-blur-sm">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-indigo-200 bg-indigo-50 text-indigo-700 text-xs font-semibold uppercase tracking-wide">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 7a1 1 0 012 0v4a1 1 0 01-2 0V7zm1 8a1.25 1.25 0 110-2.5A1.25 1.25 0 0110 15z" clip-rule="evenodd"/>
                        </svg>
                        Coming soon
                    </div>
                    <p class="text-xs text-slate-600 px-6 text-center">
                        Automated roleplay invites and shareable links are on the way.
                    </p>
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
