<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Jobs\ProcessDeviceData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller
{
    /**
     * Receive simulated IoT device data envelope.
     */
    public function receive(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'timestamp' => 'required|date',
            'payload' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Find or create the device record
        Device::firstOrCreate(
            ['device_id' => $data['device_id']],
            [
                'name' => 'Device ' . $data['device_id'],
                'serial_number' => $data['device_id'],
                'category' => $data['type'],
                'status' => $data['status'],
                'user_id' => \App\Models\User::role('operator')->first()?->id,
            ]
        );

        // Dispatch background processing job
        ProcessDeviceData::dispatch($data);

        return response()->json(['message' => 'Data accepted for processing'], 202);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'gateway' => 'online',
            'devices_count' => Device::count(),
        ]);
    }

    public function getPendingCommands($device_id): JsonResponse
    {
        $device = Device::where('device_id', $device_id)->first();
        if (!$device) return response()->json([]);

        $commands = \App\Models\Command::where('device_id', $device->id)
            ->whereIn('status', ['pending', 'sent'])
            ->get();

        return response()->json($commands);
    }

    public function updateCommandResponse(Request $request, $id): JsonResponse
    {
        $command = \App\Models\Command::findOrFail($id);
        $command->update([
            'status' => $request->status,
            'response' => $request->response,
            'response_at' => now(),
        ]);

        return response()->json(['message' => 'Response recorded']);
    }
}
