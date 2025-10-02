<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OpeningEdit extends OpeningFormBase
{
    public function mount(Opening $opening): void
    {
        $teamId = Auth::user()?->currentTeam?->id;
        abort_unless($opening->team_id === $teamId, 403);

        $this->fillFromModel($opening);
        $this->loadOptions();
    }

    public function save(string $mode = 'stay'): void
    {
        $this->validate();

        $this->opening->title            = $this->title;
        $this->opening->description      = $this->description;
        $this->opening->company_type     = $this->company_type;
        $this->opening->status           = $this->status;
        $this->opening->specialty_ids    = array_values($this->specialty_ids ?? []);
        $this->opening->territory_ids    = array_values($this->territory_ids ?? []);
        $this->opening->compensation     = $this->compensation;
        $this->opening->visibility_until = $this->visibility_until ? Carbon::parse($this->visibility_until)->startOfDay() : null;

        $this->opening->comp_structure   = $this->comp_structure ?: null;
        $this->opening->opening_type     = $this->opening_type   ?: null;

        $this->opening->roleplay_policy           = $this->roleplay_policy;
        $this->opening->roleplay_scenario_pack_id = $this->roleplay_scenario_pack_id;
        $this->opening->roleplay_pass_threshold   = $this->roleplay_pass_threshold;

        // Screening
        $this->opening->screening_policy = $this->screening_policy ?: 'off';
        $this->opening->screening_rules  = $this->normalizedScreeningRules();

        $this->opening->save();

        $this->dispatch('toast', type: 'success', message: 'Opening updated.');

        if ($mode === 'index') {
            $this->redirectRoute('employer.openings', navigate: true);
        } else {
            $this->redirectRoute('employer.openings.edit', ['opening' => $this->opening], navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.recruitment.opening-form');
    }
}
