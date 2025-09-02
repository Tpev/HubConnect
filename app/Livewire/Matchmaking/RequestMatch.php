<?php

namespace App\Livewire\Matchmaking;

use App\Models\Device;
use App\Models\DeviceMatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RequestMatch extends Component
{
    public int $deviceId;
    public bool $open = false;

    public array $territoryIds = [];
    public bool $exclusivity = false;
    public ?string $message = null;
    public ?float $proposedCommissionPercent = null;

    public function mount(int $deviceId) { $this->deviceId = $deviceId; }

    public function submit()
    {
        $user = Auth::user();
        $team = $user?->currentTeam;

        abort_unless($team && ($team->company_type ?? null) === 'distributor', 403);

        $device = Device::with('company')->findOrFail($this->deviceId);

        DeviceMatch::updateOrCreate(
            ['device_id' => $device->id, 'distributor_id' => $team->id],
            [
                'manufacturer_id' => $device->company_id,
                'initiator' => 'distributor',
                'status' => 'pending',
                'requested_territory_ids' => array_values($this->territoryIds),
                'exclusivity' => $this->exclusivity,
                'proposed_commission_percent' => $this->proposedCommissionPercent,
                'message' => $this->message,
            ]
        );

        $this->open = false;
        // TallStack UI event (assumes a global listener)
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Request sent',
            'message' => 'The manufacturer has been notified.',
        ]);
    }

    public function render()
    {
        $device = Device::with('territories:id,name')->find($this->deviceId);
        return view('livewire.matchmaking.request-match', [
            'device' => $device,
            'openTerritories' => $device?->territories ?? collect(),
        ]);
    }
}
