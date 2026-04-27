<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use App\Http\Requests\StoreDeviceTypeRequest;
use App\Http\Requests\UpdateDeviceTypeRequest;

class DeviceTypeController extends Controller
{
    public function index()
    {
        $deviceTypes = DeviceType::withCount('devices')->paginate(15);
        return view('admin.device-types.index', compact('deviceTypes'));
    }

    public function create()
    {
        $icons = ['router', 'thermometer', 'toggle-on', 'wifi', 'cpu', 'database', 'server'];
        return view('admin.device-types.create', compact('icons'));
    }

    public function store(StoreDeviceTypeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        
        DeviceType::create($data);

        session()->flash('success', 'Device type created successfully.');
        return redirect()->route('admin.device-types.index');
    }

    public function edit(DeviceType $deviceType)
    {
        $icons = ['router', 'thermometer', 'toggle-on', 'wifi', 'cpu', 'database', 'server'];
        return view('admin.device-types.edit', compact('deviceType', 'icons'));
    }

    public function update(UpdateDeviceTypeRequest $request, DeviceType $deviceType)
    {
        $deviceType->update($request->validated());

        session()->flash('success', 'Device type updated successfully.');
        return redirect()->route('admin.device-types.index');
    }

    public function destroy(DeviceType $deviceType)
    {
        if ($deviceType->devices()->exists()) {
            session()->flash('error', 'Cannot delete device type. It is assigned to devices.');
            return back();
        }
        
        $deviceType->delete();
        session()->flash('success', 'Device type deleted successfully.');
        return redirect()->route('admin.device-types.index');
    }
}
