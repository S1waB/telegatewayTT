<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceData;
use Illuminate\Http\Request;
use App\Http\Resources\DeviceDataResource;

class DeviceDataController extends Controller
{
    public function index(Request $request, Device $device)
    {
        if (!auth()->user()->hasRole('admin') && $device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('per_page', 50);
        $perPage = min(500, max(1, (int)$perPage));
        
        $query = $device->data()->latest('recorded_at');

        if ($request->filled('metric')) {
            $query->where('metric', $request->metric);
        }

        return DeviceDataResource::collection($query->paginate($perPage));
    }

    public function store(Request $request, Device $device)
    {
        // For IoT push data - only token needed, no role check
        $request->validate([
            'metric' => 'required|string|max:255',
            'value' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
            'recorded_at' => 'nullable|date',
        ]);

        $data = DeviceData::create([
            'device_id' => $device->id,
            'metric' => $request->metric,
            'value' => $request->value,
            'unit' => $request->unit,
            'recorded_at' => $request->recorded_at ?? now(),
        ]);

        // Update last seen
        $device->update(['last_seen_at' => now()]);

        return new DeviceDataResource($data);
    }
}
