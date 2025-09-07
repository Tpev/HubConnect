<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        // Parent view controls the overall page layout/design.
        // Livewire handles data, search, pagination inside its own component.
        return view('manufacturer.devices.index');
    }

    // keep your existing methods:
    public function studio(Device $device)
    {
        return view('manufacturer.devices.studio', compact('device'));
    }

    // optional:
    public function create()
    {
        return view('manufacturer.devices.create');
    }

    public function edit(Device $device)
    {
        return view('manufacturer.devices.edit', compact('device'));
    }
}
