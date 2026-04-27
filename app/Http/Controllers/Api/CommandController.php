<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCommandRequest;
use App\Http\Resources\CommandResource;
use App\Jobs\SendCommandJob;

class CommandController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(100, max(1, (int)$perPage));
        
        $query = Command::with(['device', 'user'])->latest();

        if (!auth()->user()->hasRole('admin')) {
            $query->byUser(auth()->id());
        }

        if ($request->filled('device_id')) {
            $query->byDevice($request->device_id);
        }

        if ($request->filled('user_id') && auth()->user()->hasRole('admin')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return CommandResource::collection($query->paginate($perPage));
    }

    public function store(StoreCommandRequest $request)
    {
        $device = Device::findOrFail($request->device_id);
        
        $this->authorize('sendCommand', $device);

        $payload = json_decode($request->payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON payload.'], 422);
        }

        $command = Command::create([
            'device_id' => $device->id,
            'user_id' => auth()->id(),
            'payload' => $payload,
            'status' => 'pending',
        ]);

        SendCommandJob::dispatch($command);

        return new CommandResource($command->load(['device', 'user']));
    }

    public function show(Command $command)
    {
        // Check authorization (operator can only see their own commands or commands for their devices)
        if (!auth()->user()->hasRole('admin')) {
            if ($command->user_id !== auth()->id() && $command->device->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return new CommandResource($command->load(['device', 'user']));
    }

    public function updateStatus(Request $request, Command $command)
    {
        // This endpoint is for IoT callbacks, requires token but no role check
        $request->validate([
            'status' => 'required|in:sent,success,failed',
            'response' => 'nullable|string'
        ]);

        $command->update([
            'status' => $request->status,
            'response' => $request->response,
            'response_at' => now(),
        ]);

        return new CommandResource($command);
    }
}
