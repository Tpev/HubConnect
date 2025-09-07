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

        $this->opening = $opening->only([
            'id','slug','title','company_type','compensation','specialty_ids','territory_ids',
            'description','roleplay_policy','roleplay_pass_threshold','visibility_until','created_at'
        ]) ? $opening : $opening; // keep model instance for date casting
    }

    public function render()
    {
        return view('livewire.recruitment.opening-show-public', [
            'opening' => $this->opening,
        ]);
    }
}
