<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Models\Specialty;
use App\Models\Certification;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Notifications\KycSubmitted;

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
    public $logo = null; // optional: if you want to let them update team_profile_photo_path

    public function mount(Company $company)
    {
        $this->authorize('update', $company);
        $this->company = $company;

        // hydrate fields
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

public function saveBasic()
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
        // keep your logo validation/handling here
    ]);

    $team = Auth::user()->currentTeam;

    // Persist basics to team (map your props)
    $team->name         = $this->name;
    $team->website      = $this->website;
    $team->company_type = $this->company_type;
    $team->hq_country   = $this->hq_country;
    $team->year_founded = $this->year_founded ?: null; // if you have this column
    $team->headcount    = $this->headcount ?: null;    // if you have this column
    $team->stage        = $this->stage ?: null;        // if you have this column
    $team->summary      = $this->summary ?: null;      // if you have this column

    // TODO: handle $this->logo upload the way you already do

    $team->save();

    // If not approved and basics complete, move to pending_review (once)
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

    session()->flash('saved', $justSubmitted
        ? 'Saved. Your company has been submitted for manual verification.'
        : 'Saved.');
}
    public function saveSpecialties()
    {
        $ids = array_values(array_filter($this->selectedSpecialties));
        $this->company->specialties()->sync($ids);
        session()->flash('saved', 'Specialties updated.');
    }

    public function saveCertifications()
    {
        $ids = array_values(array_filter($this->selectedCerts));
        $syncData = [];
        foreach ($ids as $id) {
            $syncData[$id] = []; // room for verified_at later
        }
        $this->company->certifications()->sync($syncData);
        session()->flash('saved', 'Certifications updated.');
    }

    public function render()
    {
        return view('livewire.companies.profile-wizard', [
            'allSpecialties' => Specialty::orderBy('name')->get(),
            'allCerts'       => Certification::orderBy('name')->get(),
        ])->title('Edit Company Profile')->layout('layouts.app');
    }
}
