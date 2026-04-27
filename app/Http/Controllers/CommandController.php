<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Device;
use App\Http\Requests\StoreCommandRequest;
use App\Jobs\SendCommandJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function index()
    {
        // Handled via history method instead
        return redirect()->route('commands.history');
    }

    public function store(StoreCommandRequest $request)
    {
        $device = Device::findOrFail($request->device_id);
        
        $this->authorize('sendCommand', $device);

        $payload = json_decode($request->payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            session()->flash('error', 'Invalid JSON payload.');
            return back()->withInput();
        }

        $command = Command::create([
            'device_id' => $device->id,
            'user_id' => auth()->id(),
            'payload' => $payload,
            'status' => 'pending',
        ]);

        Log::info("Command created, dispatching job", ['command_id' => $command->id]);
        
        SendCommandJob::dispatch($command);

        session()->flash('success', 'Command sent to device queue.');
        return back();
    }

    public function history(Request $request)
    {
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

        if ($request->filled('from') && $request->filled('to')) {
            $query->dateRange($request->from, $request->to);
        }

        $commands = $query->paginate(20)->withQueryString();
        
        $devices = auth()->user()->hasRole('admin') 
            ? Device::all() 
            : Device::assignedTo(auth()->id())->get();

        return view('commands.index', compact('commands', 'devices'));
    }
}
