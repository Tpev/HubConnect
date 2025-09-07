<?php

namespace App\Livewire\Recruitment;

use App\Models\Application; // ensure this model exists
use App\Models\Opening;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')] // public/guest layout
class ApplicationStart extends Component
{
    use WithFileUploads;

    public Opening $opening;

    // Form fields
    #[Rule(['required','string','max:120'])]
    public string $candidate_name = '';

    #[Rule(['required','email','max:150'])]
    public string $email = '';

    #[Rule(['nullable','string','max:40'])]
    public ?string $phone = null;

    #[Rule(['nullable','string','max:120'])]
    public ?string $location = null;

    #[Rule(['nullable','string','max:5000'])]
    public ?string $cover_letter = null;

    // Livewire temporary upload
    #[Rule(['nullable','file','mimes:pdf,doc,docx','max:10240'])] // 10MB
    public $cv = null;

    public bool $submitted = false;

    public function mount(Opening $opening): void
    {
        // Only allow apply when opening is published & still visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        $this->opening = $opening;
    }

    public function submit(): void
    {
        $this->validate();

        // Store resume privately (e.g., storage/app/private/cv/...)
        $cvPath = null;
        if ($this->cv) {
            $dir = 'private/cv';
            $original = $this->cv->getClientOriginalName();
            $safeOriginal = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $original);
            $filename = uniqid('cv_') . '_' . $safeOriginal;
            $cvPath = $this->cv->storeAs($dir, $filename, 'local');
        }

        Application::create([
            'team_id'        => $this->opening->team_id,
            'opening_id'     => $this->opening->id,

            // satisfy legacy schema with NOT NULL `name`
            'name'           => $this->candidate_name,

            'candidate_name' => $this->candidate_name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'location'       => $this->location,
            'cover_letter'   => $this->cover_letter,
            'cv_path'        => $cvPath,
            'status'         => 'new',
            'score'          => null,
            'invited_at'     => null,
            'invite_token'   => null,
            'completed_at'   => null,
            'roleplay_score' => null,
        ]);

        // Reset & show confirmation
        $this->reset(['candidate_name','email','phone','location','cover_letter','cv']);
        $this->submitted = true;

        // Optional toast
        $this->dispatch('toast', type: 'success', message: 'Application submitted. Thank you!');
    }

    public function render()
    {
        return view('livewire.recruitment.application-start', [
            'opening' => $this->opening,
        ]);
    }
}
