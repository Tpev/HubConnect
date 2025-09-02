<?php
namespace App\Livewire\Manufacturer;

use App\Models\Device;
use App\Models\Specialty;
use App\Models\Territory;
use Livewire\Component;
// ❌ remove this import to avoid confusion
// use TallStackUi\Facades\TallStackUi;
use TallStackUi\Traits\Interactions;

class DeviceTargeting extends Component
{
    use Interactions; // ✅ gives you $this->toast()

    public int $deviceId;
    public array $selected_specialties = [];
    public array $selected_territories = [];

    public function mount(int $deviceId): void
    {
        $this->deviceId = $deviceId;
        $device = Device::with(['specialties:id', 'territories:id'])->findOrFail($deviceId);
        $this->selected_specialties = $device->specialties->pluck('id')->all();
        $this->selected_territories = $device->territories->pluck('id')->all();
    }

    public function save(): void
    {
        $device = Device::findOrFail($this->deviceId);

        // (optional) sanity validation that ids exist:
        // $this->validate([
        //     'selected_specialties.*' => 'integer|exists:specialties,id',
        //     'selected_territories.*' => 'integer|exists:territories,id',
        // ]);

        $device->specialties()->sync($this->selected_specialties ?? []);
        $device->territories()->sync($this->selected_territories ?? []);

        $this->toast()->success('Targeting saved.')->send(); // ✅
    }

    public function render()
    {
        $specialties = Specialty::orderBy('name')->get(['id','name']);
        $territories = Territory::orderBy('name')->get(['id','name','state']);

        return view('livewire.manufacturer.device-targeting', compact('specialties','territories'));
    }
}
