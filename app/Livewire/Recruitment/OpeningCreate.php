<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        $opening->team_id                    = $teamId;
        $opening->slug                       = Str::slug($this->title) . '-' . Str::random(6);
        $opening->title                      = $this->title;
        $opening->description                = $this->description;
        $opening->company_type               = $this->company_type;
        $opening->specialty_ids              = array_values($this->specialty_ids);
        $opening->territory_ids              = array_values($this->territory_ids);
        $opening->compensation               = $this->compensation;
        $opening->visibility_until           = $this->visibility_until
            ? Carbon::parse($this->visibility_until)->startOfDay()
            : null;
        $opening->status                     = $this->status;
        $opening->roleplay_policy            = $this->roleplay_policy;
        $opening->roleplay_scenario_pack_id  = $this->roleplay_scenario_pack_id;
        $opening->roleplay_pass_threshold    = $this->roleplay_pass_threshold;
        $opening->save();

        $this->dispatch('toast', type: 'success', message: 'Opening created.');

        if ($mode === 'index') {
            $this->redirectRoute('employer.openings', navigate: true);
        } else {
            $this->redirectRoute('employer.openings.edit', ['opening' => $opening], navigate: true);
        }
    }
}
