<?php
namespace App\Livewire\Manufacturer;

use App\Models\Device;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeviceForm extends Component
{
    public ?Device $device = null;

    public string $name = '';
    public ?int $category_id = null;
    public string $description = '';
    public string $indications = '';
    public string $fda_pathway = 'none';
    public bool $reimbursable = false;
    public ?float $margin_target = null;
    public string $status = 'draft';

    // ✅ Accept a nullable int from the route, load manually
    public function mount(?int $deviceId = null): void
    {
        if ($deviceId) {
            $this->device = Device::where('company_id', auth()->user()->currentTeam->id)
                ->findOrFail($deviceId);

            $this->fill($this->device->only([
                'name','category_id','description','indications',
                'fda_pathway','reimbursable','margin_target','status'
            ]));
        }
    }

    public function save()
    {
        $data = $this->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'nullable|string',
            'indications'   => 'nullable|string',
            'fda_pathway'   => 'required|in:none,exempt,510k,pma',
            'reimbursable'  => 'boolean',
            'margin_target' => 'nullable|numeric|min:0|max:100',
            'status'        => 'required|in:draft,listed,paused',
        ]);

        $data['company_id'] = auth()->user()->currentTeam->id;
        $data['slug'] = $this->device?->slug ?? Str::slug($this->name) . '-' . Str::random(6);

        $this->device
            ? $this->device->update($data)
            : $this->device = Device::create($data);

        return redirect()->route('m.devices');
    }

    public function render()
    {
        return view('livewire.manufacturer.device-form', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
