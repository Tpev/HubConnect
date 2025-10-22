<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use App\Models\OpeningLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OpeningCreate extends OpeningFormBase
{
    public function mount(): void
    {
        // Just ensure options are ready at first render; hydrate() keeps them fresh
        $this->loadOptions();
    }

    public function save(string $mode = 'stay'): void
    {
        $payload = $this->preparedForPersist();

        // Team scope
        $teamId = Auth::user()?->currentTeam?->id;
        if ($teamId) {
            $payload['team_id'] = $teamId;
        }

        // Slug on create
        $payload['slug'] = Str::slug($this->title) . '-' . Str::random(6);

        /** @var \App\Models\Opening $opening */
        $opening = DB::transaction(function () use ($payload) {
            $opening = Opening::create($payload);

            // Persist related locations if you keep that table
            OpeningLocation::where('opening_id', $opening->id)->delete();
            foreach (($this->location_chips ?? []) as $chip) {
                $type = $chip['type'] ?? null;
                $id   = $chip['id']   ?? null;
                if (!$type || !$id) continue;

                OpeningLocation::create([
                    'opening_id' => $opening->id,
                    'entity_type'=> (string) $type,
                    'entity_id'  => (int) $id,
                ]);
            }

            return $opening;
        });

        $this->dispatch('toast', type: 'success', message: 'Opening created.');

        if ($mode === 'index') {
            $this->redirectRoute('employer.openings', navigate: true);
            return;
        }

        $this->redirectRoute('employer.openings.edit', ['opening' => $opening], navigate: true);
    }

    public function render()
    {
        return view('livewire.recruitment.opening-form');
    }
}
