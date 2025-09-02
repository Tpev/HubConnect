<?php
namespace App\Livewire\Onboarding;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChooseRole extends Component
{
    public string $company_type = '';

    public function save()
    {
        $this->validate([
            'company_type' => 'required|in:manufacturer,distributor',
        ]);

        $team = auth()->user()->currentTeam;
        $team->forceFill(['company_type' => $this->company_type])->save();

        return redirect()->route('onboarding.profile');
    }

    public function render() { return view('livewire.onboarding.choose-role'); }
}
