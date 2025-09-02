<?php

namespace App\Livewire\Manufacturer;

use App\Models\DeviceMatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MatchInbox extends Component
{
    use WithPagination;

    public string $tab = 'pending'; // pending|accepted|rejected

    public function approve(int $id)
    {
        $team = Auth::user()?->currentTeam;
        $match = DeviceMatch::where('manufacturer_id', $team->id)->findOrFail($id);
        $match->update([
            'status' => 'accepted',
            'approved_at' => now(),
            'rejected_at' => null,
        ]);
        $this->dispatch('toast', ['type'=>'success','title'=>'Accepted']);
    }

    public function reject(int $id)
    {
        $team = Auth::user()?->currentTeam;
        $match = DeviceMatch::where('manufacturer_id', $team->id)->findOrFail($id);
        $match->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
        $this->dispatch('toast', ['type'=>'info','title'=>'Rejected']);
    }

    public function render()
    {
        $team = Auth::user()?->currentTeam;

        $q = DeviceMatch::with(['device:id,name,slug','distributor:id,name'])
            ->where('manufacturer_id', $team->id)
            ->when($this->tab === 'pending', fn($w) => $w->where('status','pending'))
            ->when($this->tab === 'accepted', fn($w) => $w->where('status','accepted'))
            ->when($this->tab === 'rejected', fn($w) => $w->where('status','rejected'))
            ->latest();

        return view('livewire.manufacturer.match-inbox', ['rows' => $q->paginate(15)]);
    }
}
