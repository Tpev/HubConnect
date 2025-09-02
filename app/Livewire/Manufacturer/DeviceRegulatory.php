<?php
namespace App\Livewire\Manufacturer;

use App\Models\RegulatoryClearance;
use Livewire\Component;
use TallStackUi\Traits\Interactions; // ✅

class DeviceRegulatory extends Component
{
    use Interactions;

    public int $deviceId;

    public string $clearance_type = 'exempt';
    public ?string $number = null;
    public ?string $issue_date = null;
    public ?string $link = null;

    public function mount(int $deviceId): void
    {
        $this->deviceId = $deviceId;

        if ($c = RegulatoryClearance::firstWhere('device_id', $deviceId)) {
            $this->fill($c->only(['clearance_type','number','issue_date','link']));
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'clearance_type' => 'required|in:510k,pma,exempt',
            'number'         => 'nullable|string|max:255',
            'issue_date'     => 'nullable|date',
            'link'           => 'nullable|url|max:500',
        ]);

        RegulatoryClearance::updateOrCreate(
            ['device_id' => $this->deviceId],
            $data + ['device_id' => $this->deviceId]
        );

        // keep local inputs aligned (useful if DB mutators/casts adjust formats)
        $this->fill($data);

        // explicitly refresh UI (usually not needed, but harmless)
        $this->dispatch('$refresh');

        $this->toast()->success('Regulatory info saved.')->send();
    }

    public function render()
    {
        $clearance = RegulatoryClearance::firstWhere('device_id', $this->deviceId);

        return view('livewire.manufacturer.device-regulatory', compact('clearance'));
    }
}