<?php
namespace App\Livewire\Manufacturer;

use App\Models\ReimbursementCode;
use Livewire\Component;
use TallStackUi\Traits\Interactions; // ✅

class DeviceReimbursement extends Component
{
    use Interactions; // ✅

    public int $deviceId;

    public string $code_type = 'CPT';
    public string $code = '';
    public ?string $description = null;

    public function mount(int $deviceId) { $this->deviceId = $deviceId; }

    public function add(): void
    {
        $data = $this->validate([
            'code_type'   => 'required|in:CPT,HCPCS,DRG,ICD10',
            'code'        => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        ReimbursementCode::create($data + ['device_id' => $this->deviceId]);

        $this->reset(['code','description']);
        $this->toast()->success('Code added.')->send(); // ✅
    }

    public function remove(int $id): void
    {
        ReimbursementCode::where('device_id', $this->deviceId)->findOrFail($id)->delete();
        $this->toast()->success('Code removed.')->send(); // ✅
    }

    public function render()
    {
        $codes = ReimbursementCode::where('device_id', $this->deviceId)->latest()->get();
        return view('livewire.manufacturer.device-reimbursement', compact('codes'));
    }
}
