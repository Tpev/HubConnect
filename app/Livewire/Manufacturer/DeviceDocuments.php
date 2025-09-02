<?php

namespace App\Livewire\Manufacturer;

use App\Models\Device;
use App\Models\DeviceDocument;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions; // (optional) if you prefer $this->toast()

class DeviceDocuments extends Component
{
    use WithFileUploads;
    use Interactions; // then use $this->toast()->success(...)

    public int $deviceId;
    public string $type = 'brochure';
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $files = [];

    public function mount(int $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    // ⬇️ was "upload" — rename to avoid conflict with $wire.upload JS helper
    public function storeFiles(): void
    {
        $this->validate([
            'type'    => 'required|in:brochure,ifus,training,evidence',
            'files'   => 'required|array|max:10',
            'files.*' => 'file|max:20480', // 20MB each
        ]);

        $device = Device::findOrFail($this->deviceId);

        foreach ($this->files as $file) {
            $path = $file->store("devices/{$device->id}/docs", 'public');

            DeviceDocument::create([
                'device_id'     => $device->id,
                'type'          => $this->type,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $this->reset('files');
        $this->toast()->success('Documents uploaded.')->send();
    }

    public function delete(int $docId): void
    {
        $doc = DeviceDocument::where('device_id', $this->deviceId)->findOrFail($docId);
        Storage::disk('public')->delete($doc->path);
        $doc->delete();

        $this->toast()->success('Document removed.')->send();
    }

    public function getDocsProperty()
    {
        return DeviceDocument::where('device_id', $this->deviceId)->latest()->get();
    }

    public function render()
    {
        return view('livewire.manufacturer.device-documents', ['docs' => $this->docs]);
    }
}
