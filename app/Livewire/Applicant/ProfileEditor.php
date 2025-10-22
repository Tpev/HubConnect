<?php

namespace App\Livewire\Applicant;

use App\Models\IndividualProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ⬅ add this
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Applicant Profile')]
class ProfileEditor extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1
    public string $headline = '';
    public string $location = '';
    public string $visibility = 'private'; // private|discoverable

    // Step 2
    public ?int $years_experience = null;
    public string $bio = '';
    /** @var array<int,string> */
    public array $skills = [];
    /** @var array<int,array{label?:string,url?:string}> */
    public array $links = [];
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|string|null */
    public $cv = null; // Livewire temp file
    public ?IndividualProfile $profile = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'isIndividual') && $user->isIndividual(), 403);

        $this->profile = IndividualProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'headline'         => '',
                'location'         => '',
                'visibility'       => 'private',
                'years_experience' => null,
                'bio'              => '',
                'skills'           => [],
                'links'            => [],
                'cv_path'          => null,
            ]
        );

        // Prefill
        $this->headline         = (string) ($this->profile->headline ?? '');
        $this->location         = (string) ($this->profile->location ?? '');
        $this->visibility       = (string) ($this->profile->visibility ?? 'private');
        $this->years_experience = $this->profile->years_experience;
        $this->bio              = (string) ($this->profile->bio ?? '');
        $this->skills           = is_array($this->profile->skills) ? $this->profile->skills : [];
        $this->links            = is_array($this->profile->links) ? $this->profile->links : [];
    }

    public function addSkill(): void { $this->skills[] = ''; }
    public function removeSkill(int $i): void {
        if (isset($this->skills[$i])) { unset($this->skills[$i]); $this->skills = array_values($this->skills); }
    }
    public function addLink(): void { $this->links[] = ['label'=>'', 'url'=>'']; }
    public function removeLink(int $i): void {
        if (isset($this->links[$i])) { unset($this->links[$i]); $this->links = array_values($this->links); }
    }

    public function back(): void { $this->step = max(1, $this->step - 1); }

    public function saveStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'headline'   => ['required','string','max:120'],
                'location'   => ['required','string','max:120'],
                'visibility' => ['required', Rule::in(['private','discoverable'])],
            ]);
            $this->persist();
            $this->step = 2;
            session()->flash('success', 'Basics saved.');
            return;
        }

        if ($this->step === 2) {
            $rules = [
                'years_experience' => ['nullable','integer','min:0','max:60'],
                'bio'              => ['nullable','string','max:2000'],
                'skills'           => ['array','max:30'],
                'skills.*'         => ['nullable','string','max:60'],
                'links'            => ['array','max:20'],
                'links.*.label'    => ['nullable','string','max:40'],
                'links.*.url'      => ['nullable','url','max:255'],
            ];

            if ($this->cv) {
                $rules['cv'] = ['file','mimes:pdf,doc,docx','max:8192']; // 8MB
            }

            $this->validate($rules);
            $this->persist(handleCv: true);
            $this->step = 3;
            session()->flash('success', 'Experience and skills saved.');
            return;
        }
    }

    public function finish()
    {
        $this->persist();
        session()->flash('success', 'Profile updated.');
        return redirect()->route('dashboard.individual');
    }

    protected function persist(bool $handleCv = false): void
    {
        $this->profile->headline         = $this->headline;
        $this->profile->location         = $this->location;
        $this->profile->visibility       = $this->visibility;
        $this->profile->years_experience = $this->years_experience;
        $this->profile->bio              = $this->bio;
        $this->profile->skills           = array_values(array_filter($this->skills, fn($v) => filled($v)));

        // Normalize links (remove empties)
        $this->profile->links = array_values(array_filter($this->links, function ($lnk) {
            $label = trim((string)($lnk['label'] ?? ''));
            $url   = trim((string)($lnk['url'] ?? ''));
            return $label !== '' || $url !== '';
        }));

        if ($handleCv && $this->cv) {
            // Delete previous file if present
            if ($this->profile->cv_path && Storage::disk('public')->exists($this->profile->cv_path)) {
                Storage::disk('public')->delete($this->profile->cv_path);
            }

            // Store new file with a stable path
            $path = $this->cv->store('applicant_cv', 'public');
            $this->profile->cv_path = $path;

            // Reset the temp upload so the input clears visually
            $this->cv = null;
        }

        $this->profile->save();
    }

    public function render()
    {
        return view('livewire.applicant.profile-editor');
    }
}
