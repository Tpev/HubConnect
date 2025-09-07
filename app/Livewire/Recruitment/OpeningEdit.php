<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $this->opening->title                     = $this->title;
        $this->opening->description               = $this->description;
        $this->opening->company_type              = $this->company_type;
        $this->opening->specialty_ids             = array_values($this->specialty_ids);
        $this->opening->territory_ids             = array_values($this->territory_ids);
        $this->opening->compensation              = $this->compensation;
        $this->opening->visibility_until          = $this->visibility_until
            ? Carbon::parse($this->visibility_until)->startOfDay()
            : null;
        $this->opening->status                    = $this->status;
        $this->opening->roleplay_policy           = $this->roleplay_policy;
        $this->opening->roleplay_scenario_pack_id = $this->roleplay_scenario_pack_id;
        $this->opening->roleplay_pass_threshold   = $this->roleplay_pass_threshold;
        $this->opening->save();

        $this->dispatch('toast', type: 'success', message: 'Opening updated.');

        if ($mode === 'index') {
            $this->redirectRoute('employer.openings', navigate: true);
        } else {
            $this->redirectRoute('employer.openings.edit', ['opening' => $this->opening], navigate: true);
        }
    }
}
