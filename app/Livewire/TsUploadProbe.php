<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class TsUploadProbe extends Component
{
    use WithFileUploads;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $device_documents = [];

    public function save(): void
    {
        $this->validate([
            'device_documents'   => 'required|array|max:10',
            'device_documents.*' => 'file|max:20480|mimes:pdf,doc,docx,png,jpg,jpeg',
        ]);

        // No persistence for the probe; just prove binding/validation works
        $names = collect($this->device_documents)
            ->map(fn($f) => $f->getClientOriginalName())
            ->implode(', ');

        $this->reset('device_documents');

        session()->flash('ok', 'Validated: ' . $names);
    }

    public function render()
    {
        return view('livewire.ts-upload-probe')->layout('layouts.probe');
    }
}
