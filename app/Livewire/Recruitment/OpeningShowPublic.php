<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')] // public/guest layout
class OpeningShowPublic extends Component
{
    public Opening $opening;

    public function mount(Opening $opening): void
    {
        // Only show published & still visible
        abort_unless(
            $opening->status === 'published' &&
            (is_null($opening->visibility_until) || $opening->visibility_until->gte(now())),
            404
        );

        // Keep full model instance so casts/enums work and new fields are present
        $this->opening = $opening;
    }

    public function render()
    {
        return view('livewire.recruitment.opening-show-public', [
            'opening' => $this->opening,
        ]);
    }
}
