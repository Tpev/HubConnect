<div class="max-w-3xl mx-auto p-6 space-y-6">
    @if(session()->has('success'))
        <div class="rounded-md bg-emerald-50 text-emerald-800 text-sm px-3 py-2">
            {{ session('success') }}
        </div>
    @endif

    <div>
        <h1 class="text-2xl font-semibold">Create your profile</h1>
        <p class="text-slate-500">This helps companies understand your background. You can edit it later.</p>
    </div>

    {{-- Progress --}}
    <div class="flex items-center gap-2 text-sm">
        <div class="flex-1 h-1 rounded bg-slate-200">
            <div class="h-1 rounded bg-indigo-600"
                 style="width:
                    @if($step === 1) 33%
                    @elseif($step === 2) 66%
                    @else 100%
                    @endif
                 "></div>
        </div>
        <span class="text-slate-600">Step {{ $step }} of 3</span>
    </div>

    {{-- Step 1: Basics --}}
    @if ($step === 1)
        <div class="bg-white rounded-xl shadow p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium">Headline <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="headline" class="mt-1 w-full rounded-md border-slate-300"
                       placeholder="e.g., Medical Sales Rep — Orthopedics & Spine">
                @error('headline') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Location <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="location" class="mt-1 w-full rounded-md border-slate-300"
                       placeholder="City, State / Region">
                @error('location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Profile Visibility</label>
                <select wire:model="visibility" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="private">Private (only visible on applications)</option>
                    <option value="discoverable">Discoverable by recruiters</option>
                </select>
                @error('visibility') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button wire:click="saveStep" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Save & Continue</button>
            </div>
        </div>
    @endif

    {{-- Step 2: Experience & Skills --}}
    @if ($step === 2)
        <div class="bg-white rounded-xl shadow p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium">Years of experience</label>
                <input type="number" min="0" max="60" wire:model.defer="years_experience"
                       class="mt-1 w-full rounded-md border-slate-300" placeholder="e.g., 5">
                @error('years_experience') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Short Bio</label>
                <textarea wire:model.defer="bio" rows="5" class="mt-1 w-full rounded-md border-slate-300"
                          placeholder="Brief summary of your background, specialties, and achievements."></textarea>
                @error('bio') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Skills</label>
                    <button type="button" wire:click="addSkill" class="text-sm text-indigo-600 hover:underline">+ Add skill</button>
                </div>
                <div class="space-y-2">
                    @forelse ($skills as $i => $skill)
                        <div class="flex gap-2">
                            <input type="text" wire:model.defer="skills.{{ $i }}" class="flex-1 rounded-md border-slate-300"
                                   placeholder="e.g., Territory management">
                            <button type="button" wire:click="removeSkill({{ $i }})" class="text-slate-600 hover:text-red-600">Remove</button>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No skills yet.</p>
                    @endforelse
                </div>
                @error('skills') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('skills.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Links</label>
                    <button type="button" wire:click="addLink" class="text-sm text-indigo-600 hover:underline">+ Add link</button>
                </div>
                <div class="space-y-2">
                    @forelse ($links as $i => $link)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <input type="text" wire:model.defer="links.{{ $i }}.label" class="rounded-md border-slate-300" placeholder="Label (e.g., LinkedIn)">
                            <input type="url" wire:model.defer="links.{{ $i }}.url" class="rounded-md border-slate-300" placeholder="https://...">
                            <div class="md:col-span-2">
                                <button type="button" wire:click="removeLink({{ $i }})" class="text-slate-600 hover:text-red-600 text-sm">Remove</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No links yet.</p>
                    @endforelse
                </div>
                @error('links') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('links.*.url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

<div>
    <label class="block text-sm font-medium">Upload CV (PDF, DOC, DOCX — max 8MB)</label>

    {{-- File input --}}
    <input type="file" wire:model="cv" class="mt-1 block w-full text-sm" accept=".pdf,.doc,.docx">

    {{-- Live upload feedback --}}
    <div class="mt-2 text-xs text-slate-600" wire:loading wire:target="cv">
        Uploading…
    </div>
    @error('cv') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

    {{-- After selection (before save) show chosen file name/size --}}
    @if ($cv)
        <p class="text-sm text-slate-600 mt-2">
            Selected: <span class="font-medium">{{ $cv->getClientOriginalName() }}</span>
            <span class="text-slate-500">({{ number_format($cv->getSize() / 1024, 0) }} KB)</span>
        </p>
    @endif

    {{-- After save show current file --}}
    @if ($profile?->cv_path)
        <p class="text-sm text-slate-600 mt-2">
            Current file:
            <a class="text-indigo-600 underline" href="{{ Storage::disk('public')->url($profile->cv_path) }}" target="_blank" rel="noopener">Open</a>
        </p>
    @endif
</div>

            </div>

            <div class="flex justify-between">
                <button type="button" wire:click="back" class="px-4 py-2 rounded-md border border-slate-300 text-slate-700">Back</button>
                <button type="button" wire:click="saveStep" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Save & Continue</button>
            </div>
        </div>
    @endif

    {{-- Step 3: Review & Confirm --}}
    @if ($step === 3)
        <div class="bg-white rounded-xl shadow p-5 space-y-4">
            <h2 class="text-lg font-semibold">Review</h2>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-medium text-slate-600">Headline</dt>
                    <dd class="text-slate-900">{{ $headline }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-600">Location</dt>
                    <dd class="text-slate-900">{{ $location }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-600">Visibility</dt>
                    <dd class="text-slate-900 capitalize">{{ $visibility }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-600">Years of experience</dt>
                    <dd class="text-slate-900">{{ $years_experience ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-medium text-slate-600">Bio</dt>
                    <dd class="text-slate-900 whitespace-pre-line">{{ $bio }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-medium text-slate-600">Skills</dt>
                    <dd class="text-slate-900">{{ implode(', ', array_filter($skills)) ?: '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-medium text-slate-600">Links</dt>
                    <dd class="space-y-1">
                        @forelse ($links as $lnk)
                            <div>
                                <span class="text-slate-700">{{ $lnk['label'] ?? 'Link' }}:</span>
                                <a class="text-indigo-600 underline" target="_blank" href="{{ $lnk['url'] ?? '#' }}">{{ $lnk['url'] ?? '' }}</a>
                            </div>
                        @empty
                            —
                        @endforelse
                    </dd>
                </div>
            </dl>

            <div class="flex justify-between">
                <button type="button" wire:click="back" class="px-4 py-2 rounded-md border border-slate-300 text-slate-700">Back</button>
                <button type="button" wire:click="finish" class="px-4 py-2 rounded-md bg-emerald-600 text-white">Confirm & Continue</button>
            </div>
        </div>
    @endif
</div>
