<?php

namespace App\Livewire;

use App\Models\IndividualProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')] // or your app layout; change if needed
class ProfileSetup extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Bound fields
    public ?string $headline = null;
    public ?string $bio = null;
    public ?string $location = null;
    public ?int $years_experience = null;
    public array $skills = [];  // array of strings
    public array $links = [];   // [['label'=>'LinkedIn','url'=>'...']]
    public $cv;                 // TemporaryUploadedFile

    public string $visibility = 'private';

    public ?IndividualProfile $profile = null;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->isIndividual(), 403, 'This setup is for individual accounts.');

        $this->profile = IndividualProfile::firstOrCreate(
            ['user_id' => $user->id],
            // defaults
            []
        );

        // Hydrate fields if existing
        $this->headline         = $this->profile->headline;
        $this->bio              = $this->profile->bio;
        $this->location         = $this->profile->location;
        $this->years_experience = $this->profile->years_experience;
        $this->skills           = $this->profile->skills ?? [];
        $this->links            = $this->profile->links ?? [];
        $this->visibility       = $this->profile->visibility ?? 'private';
    }

    public function rules(): array
    {
        if ($this->step === 1) {
            return [
                'headline' => ['required','string','max:140'],
                'location' => ['required','string','max:120'],
                'visibility' => ['required', Rule::in(['private','discoverable'])],
            ];
        }

        if ($this->step === 2) {
            return [
                'bio' => ['nullable','string','max:2000'],
                'years_experience' => ['nullable','integer','min:0','max:60'],
                'skills' => ['array','max:30'],
                'skills.*' => ['string','max:40'],
                'links' => ['array','max:10'],
                'links.*.label' => ['nullable','string','max:40'],
                'links.*.url'   => ['nullable','url','max:255'],
                'cv' => [
                    'nullable',
                    File::types(['pdf','doc','docx'])->max(8 * 1024), // 8MB
                ],
            ];
        }

        // Step 3 is review; no extra fields
        return [];
    }

    public function saveStep(): void
    {
        $this->validate();

        // Handle CV upload if present
        $cvPath = $this->profile->cv_path;
        if ($this->cv) {
            $cvPath = $this->cv->store('cv', 'public'); // storage/app/public/cv/...
        }

        $this->profile->fill([
            'headline'         => $this->headline,
            'bio'              => $this->bio,
            'location'         => $this->location,
            'years_experience' => $this->years_experience,
            'skills'           => array_values(array_filter($this->skills, fn($s) => filled($s))),
            'links'            => $this->normalizeLinks($this->links),
            'cv_path'          => $cvPath,
            'visibility'       => $this->visibility,
        ])->save();

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function back(): void
    {
        if ($this->step > 1) $this->step--;
    }

public function finish()
{
    // Ensure required data is valid (step 1 rules)
    $this->step = 1;
    $this->validate();

    $this->profile->completed_at = now();
    $this->profile->save();

    // Livewire 3-friendly redirect (returns Redirector)
    return $this->redirect('/kyc/individual', navigate: true);
    // Alternatively:
    // return redirect()->to('/kyc/individual');
}


    private function normalizeLinks(array $links): array
    {
        $out = [];
        foreach ($links as $link) {
            $label = trim((string)($link['label'] ?? ''));
            $url   = trim((string)($link['url'] ?? ''));
            if ($label || $url) {
                $out[] = ['label' => $label ?: 'Link', 'url' => $url];
            }
        }
        return $out;
    }

    public function addSkill(): void
    {
        $this->skills[] = '';
    }

    public function removeSkill(int $i): void
    {
        unset($this->skills[$i]);
        $this->skills = array_values($this->skills);
    }

    public function addLink(): void
    {
        $this->links[] = ['label' => '', 'url' => ''];
    }

    public function removeLink(int $i): void
    {
        unset($this->links[$i]);
        $this->links = array_values($this->links);
    }

    public function render()
    {
        return view('livewire.profile-setup');
    }
}
