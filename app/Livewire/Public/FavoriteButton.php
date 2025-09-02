<?php

namespace App\Livewire\Public;

use App\Models\FavoriteDevice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public int $deviceId;
    public bool $favorited = false;

    public function mount(int $deviceId)
    {
        $this->deviceId = $deviceId;
        $teamId = Auth::user()?->currentTeam?->id;
        $this->favorited = $teamId
            ? FavoriteDevice::where('device_id',$deviceId)->where('company_id',$teamId)->exists()
            : false;
    }

    public function toggle()
    {
        $team = Auth::user()?->currentTeam;
        abort_unless($team && ($team->company_type ?? null) === 'distributor', 403);

        if ($this->favorited) {
            FavoriteDevice::where('device_id',$this->deviceId)->where('company_id',$team->id)->delete();
            $this->favorited = false;
        } else {
            FavoriteDevice::firstOrCreate(['device_id'=>$this->deviceId,'company_id'=>$team->id]);
            $this->favorited = true;
        }
    }

    public function render() { return view('livewire.public.favorite-button'); }
}
