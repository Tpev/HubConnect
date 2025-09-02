<?php

namespace App\Livewire\Public;

use App\Models\Device;
use Livewire\Component;

class DeviceShow extends Component
{
    public string $slug;
    public ?Device $device = null;

    public function mount(string $slug) {
        $this->slug = $slug;
        $this->device = Device::query()
            ->with(['company','category','specialties','territories','documents','clearance','reimbursementCodes'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        if ($this->device->visibility === 'invite_only') {
            abort(403); // or redirect to a "request access" page
        }
    }

    public function render() {
        return view('livewire.public.device-show')->layout('layouts.app');
    }
}
