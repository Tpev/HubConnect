<?php

namespace App\Livewire\Companies;

use App\Models\Certification;
use App\Models\Company;
use App\Models\Specialty;
use App\Notifications\KycSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileWizard extends Component
{
    use WithFileUploads;

    public Company $company;

    #[Validate('required|string|min:3')]
    public string $name = '';

    public ?string $website = null;
    public ?string $hq_country = null;
    public ?int $year_founded = null;
    public ?int $headcount = null;
    public ?string $stage = null;
    public ?string $summary = null;
    public ?string $company_type = null; // 'manufacturer','distributor','both'

    public array $selectedSpecialties = [];
    public array $selectedCerts = [];

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $logo = null;

    public function mount(Company $company): void
    {
        Gate::authorize('update', $company);
        $this->company = $company;

        // hydrate
        $this->name         = $company->name ?? '';
        $this->website      = $company->website;
        $this->hq_country   = $company->hq_country;
        $this->year_founded = $company->year_founded;
        $this->headcount    = $company->headcount;
        $this->stage        = $company->stage;
        $this->summary      = $company->summary;
        $this->company_type = $company->company_type;

        $this->selectedSpecialties = $company->specialties()->pluck('specialties.id')->all();
        $this->selectedCerts       = $company->certifications()->pluck('certifications.id')->all();
    }

    public function saveBasic(): void
    {
        $this->validate([
            'name'          => ['required','string','max:255'],
            'website'       => ['nullable','string','max:255'],
            'company_type'  => ['required','in:manufacturer,distributor,both'],
            'hq_country'    => ['required','string','max:120'],
            'year_founded'  => ['nullable','integer','min:1800','max:'.now()->year],
            'headcount'     => ['nullable','integer','min:1'],
            'stage'         => ['nullable','in:startup,growth,established,global'],
            'summary'       => ['nullable','string','max:5000'],
            'logo'          => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], // 2MB
        ]);

        $team = Auth::user()->currentTeam;

        // Persist basics
        $team->name         = $this->name;
        $team->website      = $this->website;
        $team->company_type = $this->company_type;
        $team->hq_country   = $this->hq_country;
        $team->year_founded = $this->year_founded ?: null;
        $team->headcount    = $this->headcount ?: null;
        $team->stage        = $this->stage ?: null;
        $team->summary      = $this->summary ?: null;

        // Handle logo upload (optional)
        if ($this->logo) {
            $old = $team->team_profile_photo_path;

            // Store publicly on the "public" disk
            $path = $this->logo->storePublicly('company-logos', 'public');

            $team->team_profile_photo_path = $path;

            // Clean up old file (if any)
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        $team->save();

        // Submit for KYC review if basics complete & status allows
        $basicsComplete = filled($team->name) && filled($team->company_type) && filled($team->hq_country);
        $justSubmitted = false;

        if ($basicsComplete && in_array($team->kyc_status, ['new','rejected'])) {
            $team->kyc_status = 'pending_review';
            $team->kyc_submitted_at = now();
            $team->kyc_notes = null;
            $team->save();

            Auth::user()->notify(new KycSubmitted($team));
            $justSubmitted = true;
        }

        // clear tmp logo field so the file input resets
        $this->logo = null;

        // Flash + toast
        session()->flash('saved', $justSubmitted
            ? 'Saved. Your company has been submitted for manual verification.'
            : 'Saved.');

        $this->dispatch('toast', message: session('saved'));
    }

    public function saveSpecialties(): void
    {
        $ids = array_values(array_filter($this->selectedSpecialties));
        $this->company->specialties()->sync($ids);

        session()->flash('saved', 'Specialties updated.');
        $this->dispatch('toast', message: session('saved'));
    }

    public function saveCertifications(): void
    {
        $ids = array_values(array_filter($this->selectedCerts));
        $syncData = [];
        foreach ($ids as $id) { $syncData[$id] = []; }
        $this->company->certifications()->sync($syncData);

        session()->flash('saved', 'Certifications updated.');
        $this->dispatch('toast', message: session('saved'));
    }

    public function render()
    {
        return view('livewire.companies.profile-wizard', [
            'allSpecialties' => Specialty::orderBy('name')->get(),
            'allCerts'       => Certification::orderBy('name')->get(),
        ])->title('Edit Company Profile')->layout('layouts.app');
    }
}
