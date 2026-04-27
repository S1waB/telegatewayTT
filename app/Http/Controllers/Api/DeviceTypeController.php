<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDeviceTypeRequest;
use App\Http\Requests\UpdateDeviceTypeRequest;
use App\Http\Resources\DeviceTypeResource;

class DeviceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-device-types')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(100, max(1, (int)$perPage));
        
        return DeviceTypeResource::collection(DeviceType::with('creator')->withCount('devices')->paginate($perPage));
    }

    public function store(StoreDeviceTypeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        
        $deviceType = DeviceType::create($data);

        return new DeviceTypeResource($deviceType->load('creator'));
    }

    public function show(DeviceType $deviceType)
    {
        return new DeviceTypeResource($deviceType->load('creator')->loadCount('devices'));
    }

    public function update(UpdateDeviceTypeRequest $request, DeviceType $deviceType)
    {
        $deviceType->update($request->validated());

        return new DeviceTypeResource($deviceType->load('creator'));
    }

    public function destroy(DeviceType $deviceType)
    {
        if ($deviceType->devices()->exists()) {
            return response()->json(['error' => 'Cannot delete device type. It is assigned to devices.'], 422);
        }
        
        $deviceType->delete();
        return response()->json(null, 204);
    }
}
