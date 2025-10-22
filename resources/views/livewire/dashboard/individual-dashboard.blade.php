<div class="max-w-7xl mx-auto p-6 space-y-8">
    {{-- Profile prompt banner (only if critical fields missing) --}}
    @if($needsApplicantProfile && Route::has('applicant.profile.edit'))
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 sm:flex sm:items-start sm:gap-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 text-indigo-600 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 7a1 1 0 012 0v4a1 1 0 01-2 0V7zm1 8a1.25 1.25 0 110-2.5A1.25 1.25 0 0110 15z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <div class="font-medium text-slate-900">Finish your applicant profile</div>
                    <div class="text-sm text-slate-700">Add your headline & location so recruiters understand your background.</div>
                </div>
            </div>
            <div class="mt-3 sm:mt-0 sm:ml-auto">
                <a href="{{ route('applicant.profile.edit') }}" class="btn-brand whitespace-nowrap">Create now</a>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">My Dashboard</h1>
            <p class="text-slate-500">Keep your profile sharp, speed up verification, and track your applications.</p>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @if(Route::has('openings.index'))
                <a href="{{ route('openings.index') }}" class="btn-accent inline-flex items-center gap-2 px-4 py-2 whitespace-nowrap">
                    Browse Jobs
                    <x-ts-icon name="arrow-right" />
                </a>
            @endif

            {{-- Edit Profile now goes to the new Applicant Profile Editor --}}
            @if(Route::has('applicant.profile.edit'))
                <a href="{{ route('applicant.profile.edit') }}" class="btn-brand outline hidden sm:inline-flex px-4 py-2 whitespace-nowrap">
                    Edit Profile
                </a>
            @else
                <a href="{{ route('profile.show') }}" class="btn-brand outline hidden sm:inline-flex px-4 py-2 whitespace-nowrap">
                    Edit Profile
                </a>
            @endif
        </div>
    </div>

    {{-- Key KPIs + Progress --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Profile completion --}}
        <x-ts-card class="p-5 space-y-4 ring-brand">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Profile Completion</h3>
                <span class="text-sm font-semibold text-slate-900">{{ $completion }}%</span>
            </div>
            <div class="h-2 rounded bg-slate-200">
                <div class="h-2 rounded bg-[var(--brand-600)] transition-all" style="width: {{ max(0, min(100, $completion)) }}%"></div>
            </div>

            @if($completion < 100 && !empty($missingFields))
                <div class="text-xs text-slate-600">
                    <span class="font-medium">Missing:</span>
                    <span class="inline-block">
                        {{ implode(', ', $missingFields) }}
                    </span>
                </div>
            @endif

            <div class="flex flex-wrap gap-2">
                @if($completion < 100)
                    @if(Route::has('applicant.profile.edit'))
                        <a href="{{ route('applicant.profile.edit') }}" class="btn-brand text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                            Complete now
                            <x-ts-icon name="arrow-right" />
                        </a>
                    @else
                        <a href="{{ route('profile.show') }}" class="btn-brand text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                            Complete now
                            <x-ts-icon name="arrow-right" />
                        </a>
                    @endif
                @endif

                @if($completion < 70)
                    <span class="inline-flex items-center text-xs px-2 py-1 rounded bg-amber-50 text-amber-700 whitespace-nowrap">
                        Tip: add headline & skills
                    </span>
                @endif
            </div>
        </x-ts-card>

        {{-- Verification (individual) --}}
        <x-ts-card class="p-5 space-y-3">
            <h3 class="text-sm font-semibold text-slate-700">Verification</h3>

            @switch($kycStatus)
                @case('approved')
                    <div class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 rounded px-2 py-1 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879A1 1 0 006.293 10.293l2 2a1 1 0 001.414 0l4-4Z" clip-rule="evenodd"/>
                        </svg>
                        Approved
                    </div>
                    @break

                @case('pending_review')
                    <div class="inline-flex items-center gap-2 text-amber-700 bg-amber-50 rounded px-2 py-1 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm.75-12.5a.75.75 0 00-1.5 0v5c0 .199.079.39.22.53l3 3a.75.75 0 101.06-1.06l-2.78-2.78V5.5Z" clip-rule="evenodd"/>
                        </svg>
                        Pending review
                    </div>
                    @break

                @case('rejected')
                    <div class="inline-flex items-center gap-2 text-red-700 bg-red-50 rounded px-2 py-1 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.593c.75 1.335-.214 2.983-1.742 2.983H3.48c-1.528 0-2.492-1.648-1.742-2.983L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-.25-6.75a.75.75 0 00-1.5 0v3.5a.75.75 0 001.5 0v-3.5z" clip-rule="evenodd"/>
                        </svg>
                        Rejected — please revise
                    </div>
                    @break

                @case('draft')
                    <div class="inline-flex items-center gap-2 text-slate-700 bg-slate-100 rounded px-2 py-1 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.5 8.5a2 2 0 01-.878.506l-3.25.813a.75.75 0 01-.91-.91l.813-3.25a2 2 0 01.506-.878l8.39-8.609z"/>
                        </svg>
                        Draft — not submitted
                    </div>
                    @break

                @default
                    <div class="inline-flex items-center gap-2 text-slate-700 bg-slate-100 rounded px-2 py-1 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-5.5a.75.75 0 011.5 0 .75.75 0 01-1.5 0Zm.75-7a3.25 3.25 0 00-2.627 5.179c.214.296.553.471.913.471h.164a.75.75 0 00.75-.75V9c0-.414.336-.75.75-.75A1.25 1.25 0 1111.25 9a.75.75 0 001.5 0A2.75 2.75 0 0010 5.5Z" clip-rule="evenodd"/>
                        </svg>
                        Not started
                    </div>
            @endswitch

            <div class="text-xs text-slate-600">{{ $kycHint }}</div>

            @if(Route::has('kyc.individual'))
                <div>
                    <a href="{{ route('kyc.individual') }}" class="btn-accent text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                        {{ in_array($kycStatus, ['not_started','draft','rejected']) ? 'Start / Continue' : 'View submission' }}
                        <x-ts-icon name="arrow-right" />
                    </a>
                </div>
            @endif
        </x-ts-card>

        {{-- Next steps (dynamic) --}}
        <x-ts-card class="p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Next Steps</h3>
                @if($newRolesCount > 0)
                    <span class="text-xs text-slate-500">
                        {{ $newRolesCount }} new {{ \Illuminate\Support\Str::plural('role', $newRolesCount) }} this week
                    </span>
                @endif
            </div>

            @if(!empty($nextSteps))
                <ul class="text-sm text-slate-700 space-y-2">
                    @foreach($nextSteps as $step)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L9.586 11 7.293 8.707a1 1 0 011.414-1.414l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <div class="font-medium">{{ $step['title'] }}</div>
                                @if(!empty($step['hint']))
                                    <div class="text-xs text-slate-500">{{ $step['hint'] }}</div>
                                @endif
                                @if(!empty($step['href']))
                                    <a href="{{ $step['href'] }}" class="text-xs text-[var(--brand-700)] underline">Go</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-slate-500">You’re all set. Check today’s openings.</div>
            @endif
        </x-ts-card>
    </div>

    {{-- My Applications --}}
    <x-ts-card class="p-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">My Applications</h3>
            @if(Route::has('applications.index'))
                <a href="{{ route('applications.index') }}" class="text-sm text-[var(--brand-700)] hover:underline whitespace-nowrap">See all</a>
            @endif
        </div>

        @if(count($applications) > 0)
            {{-- Mobile: stacked cards --}}
            <div class="mt-4 space-y-3 md:hidden">
                @foreach($applications as $app)
                    <div class="rounded-lg ring-1 ring-slate-200 p-3 bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                @if($app['opening_url'])
                                    <a href="{{ $app['opening_url'] }}" class="font-medium text-slate-900 hover:underline line-clamp-2">
                                        {{ $app['opening_title'] ?? 'Untitled role' }}
                                    </a>
                                @else
                                    <div class="font-medium text-slate-900 line-clamp-2">
                                        {{ $app['opening_title'] ?? 'Untitled role' }}
                                    </div>
                                @endif

                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                    @if(!empty($app['company_type']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                            {{ ucfirst($app['company_type']) }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M6 2a1 1 0 011 1v1h6V3a1 1 0 112 0v1h1.5A1.5 1.5 0 0118 5.5V16a2 2 0 01-2 2H4a2 2 0 01-2-2V5.5A1.5 1.5 0 013.5 4H5V3a1 1 0 011-1zm10 7H4v7a1 1 0 001 1h10a1 1 0 001-1V9z"/>
                                        </svg>
                                        Applied {{ $app['applied_for_humans'] }}
                                    </span>
                                    @if(!empty($app['compensation']))
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2H2zm18 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8h18zM6 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            {{ $app['compensation'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                @php
                                    $status = $app['status'] ?? 'pending';
                                    $statusMap = [
                                        'pending'        => ['Pending', 'bg-amber-50 text-amber-700'],
                                        'under_review'   => ['Under review', 'bg-blue-50 text-blue-700'],
                                        'interview'      => ['Interview', 'bg-indigo-50 text-indigo-700'],
                                        'offer'          => ['Offer', 'bg-emerald-50 text-emerald-700'],
                                        'rejected'       => ['Rejected', 'bg-red-50 text-red-700'],
                                        'withdrawn'      => ['Withdrawn', 'bg-slate-100 text-slate-700'],
                                        'hired'          => ['Hired', 'bg-emerald-50 text-emerald-700'],
                                    ];
                                    $chip = $statusMap[$status] ?? $statusMap['pending'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $chip[1] }} whitespace-nowrap">{{ $chip[0] }}</span>

                                @if($app['application_url'])
                                    <a href="{{ $app['application_url'] }}" class="btn-muted outline text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                                        View
                                        <x-ts-icon name="arrow-right" />
                                    </a>
                                @elseif($app['opening_url'])
                                    <a href="{{ $app['opening_url'] }}" class="btn-muted outline text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                                        Open role
                                        <x-ts-icon name="arrow-right" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop/tablet: table --}}
            <div class="mt-4 hidden md:block overflow-hidden rounded-lg ring-1 ring-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Applied</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach($applications as $app)
                            @php
                                $status = $app['status'] ?? 'pending';
                                $statusMap = [
                                    'pending'        => ['Pending', 'bg-amber-50 text-amber-700'],
                                    'under_review'   => ['Under review', 'bg-blue-50 text-blue-700'],
                                    'interview'      => ['Interview', 'bg-indigo-50 text-indigo-700'],
                                    'offer'          => ['Offer', 'bg-emerald-50 text-emerald-700'],
                                    'rejected'       => ['Rejected', 'bg-red-50 text-red-700'],
                                    'withdrawn'      => ['Withdrawn', 'bg-slate-100 text-slate-700'],
                                    'hired'          => ['Hired', 'bg-emerald-50 text-emerald-700'],
                                ];
                                $chip = $statusMap[$status] ?? $statusMap['pending'];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-slate-900">
                                        @if($app['opening_url'])
                                            <a href="{{ $app['opening_url'] }}" class="hover:underline">
                                                {{ $app['opening_title'] ?? 'Untitled role' }}
                                            </a>
                                        @else
                                            {{ $app['opening_title'] ?? 'Untitled role' }}
                                        @endif
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                        @if(!empty($app['company_type']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                                {{ ucfirst($app['company_type']) }}
                                            </span>
                                        @endif
                                        @if(!empty($app['compensation']))
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2H2zm18 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8h18zM6 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                </svg>
                                                {{ $app['compensation'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-sm text-slate-600 whitespace-nowrap">
                                    {{ $app['applied_for_humans'] }}
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <span class="px-2 py-1 rounded text-xs {{ $chip[1] }} whitespace-nowrap">{{ $chip[0] }}</span>
                                </td>
                                <td class="px-4 py-3 align-top text-right">
                                    @if($app['application_url'])
                                        <a href="{{ $app['application_url'] }}" class="btn-muted outline text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                                            View
                                            <x-ts-icon name="arrow-right" />
                                        </a>
                                    @elseif($app['opening_url'])
                                        <a href="{{ $app['opening_url'] }}" class="btn-muted outline text-xs inline-flex items-center gap-1.5 whitespace-nowrap">
                                            Open role
                                            <x-ts-icon name="arrow-right" />
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-4 text-sm text-slate-500">
                You haven’t applied to any roles yet.
                @if(Route::has('openings.index'))
                    <a href="{{ route('openings.index') }}" class="underline text-[var(--brand-700)]">Browse jobs</a>
                @endif
            </div>
        @endif
    </x-ts-card>
</div>
