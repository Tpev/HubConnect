<?php
namespace App\Livewire\Onboarding;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CompanyProfile extends Component
{
    public string $website = '';
    public string $phone = '';
    public string $about = '';
    public string $hq_state = '';
    public string $hq_country = 'US';

    public function mount()
    {
        $t = auth()->user()->currentTeam;
        $this->website = (string)($t->website ?? '');
        $this->phone   = (string)($t->phone ?? '');
        $this->about   = (string)($t->about ?? '');
        $this->hq_state   = (string)($t->hq_state ?? '');
        $this->hq_country = (string)($t->hq_country ?? 'US');
    }

    public function save()
    {
        $data = $this->validate([
            'website'     => 'nullable|url|max:255',
            'phone'       => 'nullable|string|max:50',
            'about'       => 'nullable|string|max:2000',
            'hq_state'    => 'nullable|string|max:100',
            'hq_country'  => 'required|string|size:2',
        ]);

        auth()->user()->currentTeam->forceFill($data)->save();

        // Redirect to your role-based start page soon; for now, dashboard works
        return redirect()->route('dashboard');
    }

    public function render() { return view('livewire.onboarding.company-profile'); }
}
