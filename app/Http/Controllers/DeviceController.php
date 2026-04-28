<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceType;
use App\Models\User;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Device::class, 'device');
    }

    public function index(Request $request)
    {
        $query = Device::with(['type', 'user']);

        if (!auth()->user()->hasRole('admin')) {
            $query->assignedTo(auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('device_type_id', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->paginate(10)->withQueryString();
        $deviceTypes = DeviceType::all();

        return view('devices.index', compact('devices', 'deviceTypes'));
    }

    public function create()
    {
        $deviceTypes = DeviceType::all();
        $users = User::role('operator')->get();
        return view('devices.create', compact('deviceTypes', 'users'));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Device::create($data);

        session()->flash('success', 'Device created successfully.');
        return redirect()->route('admin.devices.index');
    }

    public function show(Device $device)
    {
        $device->load(['type', 'user']);
        $recentCommands = $device->commands()->with('user')->latest()->take(10)->get();
        
        // Simple data aggregation for charts
        $chartData = $device->data()
            ->latest('recorded_at')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        return view('devices.show', compact('device', 'recentCommands', 'chartData'));
    }

    public function edit(Device $device)
    {
        $deviceTypes = DeviceType::all();
        $users = User::role('operator')->get();
        return view('devices.edit', compact('device', 'deviceTypes', 'users'));
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($device->avatar) {
                Storage::disk('public')->delete($device->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $device->update($data);

        session()->flash('success', 'Device updated successfully.');
        return redirect()->route('admin.devices.index');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        session()->flash('success', 'Device deleted successfully.');
        return redirect()->route('admin.devices.index');
    }

    public function assignUser(Request $request, Device $device)
    {
        $this->authorize('update', $device);
        
        $request->validate([
            'user_id' => 'nullable|exists:users,id'
        ]);

        $device->update(['user_id' => $request->user_id]);
        
        session()->flash('success', 'Device assignment updated.');
        return back();
    }
}
