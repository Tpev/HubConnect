<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OpeningCreate extends OpeningFormBase
{
    public function mount(): void
    {
        $this->loadOptions();
    }

    public function save(string $mode = 'stay'): void
    {
        $this->validate();

        $teamId = Auth::user()?->currentTeam?->id;

        $opening = new Opening();
        $opening->team_id          = $teamId;
        $opening->slug             = Str::slug($this->title) . '-' . Str::random(6);
        $opening->title            = $this->title;
        $opening->description      = $this->description;
        $opening->company_type     = $this->company_type;
        $opening->status           = $this->status;
        $opening->specialty_ids    = array_values($this->specialty_ids ?? []);
        $opening->territory_ids    = array_values($this->territory_ids ?? []);
        $opening->compensation     = $this->compensation;
        $opening->visibility_until = $this->visibility_until ? Carbon::parse($this->visibility_until)->startOfDay() : null;

        $opening->comp_structure   = $this->comp_structure ?: null;
        $opening->opening_type     = $this->opening_type   ?: null;

        $opening->roleplay_policy           = $this->roleplay_policy;
        $opening->roleplay_scenario_pack_id = is_numeric($this->roleplay_scenario_pack_id) ? (int)$this->roleplay_scenario_pack_id : null;
        $opening->roleplay_pass_threshold   = is_numeric($this->roleplay_pass_threshold) ? (float)$this->roleplay_pass_threshold : null;

        $opening->screening_policy = $this->screening_policy ?: 'off';
        $opening->screening_rules  = $this->normalizedScreeningRules();

        $opening->save();

        $this->dispatch('toast', type: 'success', message: 'Opening created.');

        if ($mode === 'index') {
            $this->redirectRoute('employer.openings', navigate: true);
        } else {
            $this->redirectRoute('employer.openings.edit', ['opening' => $opening], navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.recruitment.opening-form');
    }
}
