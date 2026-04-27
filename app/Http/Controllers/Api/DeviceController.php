<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Device::class, 'device');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(100, max(1, (int)$perPage));
        
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

        return DeviceResource::collection($query->paginate($perPage));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $device = Device::create($data);

        return new DeviceResource($device->load(['type', 'user']));
    }

    public function show(Device $device)
    {
        return new DeviceResource($device->load(['type', 'user']));
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

        return new DeviceResource($device->load(['type', 'user']));
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return response()->json(null, 204);
    }
}
