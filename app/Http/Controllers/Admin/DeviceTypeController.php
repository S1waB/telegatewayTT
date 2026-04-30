<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use App\Http\Requests\StoreDeviceTypeRequest;
use App\Http\Requests\UpdateDeviceTypeRequest;

class DeviceTypeController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = DeviceType::query()
            ->withCount('devices')
            ->withCount(['devices as active_devices_count' => function ($q) {
                $q->where('status', 'active');
            }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $deviceTypes = $query->latest()->paginate(10)->withQueryString();
        $icons = ['router', 'thermometer', 'toggle-on', 'wifi', 'cpu', 'database', 'server', 'activity', 'battery', 'broadcast', 'camera', 'cloud', 'hard-drive', 'phone', 'speaker'];

        return view('admin.device-types.index', compact('deviceTypes', 'icons'));
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
        
        if ($request->hasFile('custom_icon')) {
            $data['image_path'] = $request->file('custom_icon')->store('device_types', 'public');
            $data['icon'] = null; // Clear preset icon if custom is uploaded
        }
        
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
        $data = $request->validated();

        if ($request->hasFile('custom_icon')) {
            if ($deviceType->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($deviceType->image_path);
            }
            $data['image_path'] = $request->file('custom_icon')->store('device_types', 'public');
            $data['icon'] = null;
        } elseif ($request->filled('icon')) {
            // If they switched back to a preset icon
            if ($deviceType->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($deviceType->image_path);
            }
            $data['image_path'] = null;
        }

        $deviceType->update($data);

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

    public function analytics()
    {
        $deviceTypes = DeviceType::withCount('devices')
            ->withCount(['devices as active_count' => function($q) {
                $q->where('status', 'active');
            }])->get();
            
        return view('admin.device-types.analytics', compact('deviceTypes'));
    }

    public function export()
    {
        $fileName = 'device_types_export_' . date('Y-m-d') . '.csv';
        $types = DeviceType::withCount('devices')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Description', 'Devices Count', 'Created At');

        $callback = function() use($types, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($types as $type) {
                fputcsv($file, array(
                    $type->id,
                    $type->name,
                    $type->description,
                    $type->devices_count,
                    $type->created_at->format('Y-m-d')
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
