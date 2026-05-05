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

        // Stats calculation
        $totalDevices = (clone $query)->count();
        $activeDevices = (clone $query)->where('status', 'active')->count();
        $activePercentage = $totalDevices > 0 ? round(($activeDevices / $totalDevices) * 100) : 0;

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
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
        $users = User::role('operator')->get();

        return view('devices.index', compact('devices', 'deviceTypes', 'users', 'activePercentage', 'totalDevices', 'activeDevices'));
    }

    public function create()
    {
        $deviceTypes = DeviceType::all();
        $users = User::role('operator')->get();
        return view('devices.create', compact('deviceTypes', 'users'));
    }

    public function store(StoreDeviceRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $device = Device::create($data);

            if ($device->user_id) {
                $user = User::find($device->user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\DeviceAssignedNotification($device, auth()->user()));
                }
            }

            session()->flash('success', 'Device created successfully.');
            return redirect()->route('admin.devices.index');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Device Creation Failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            session()->flash('error', 'Error creating device: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(Device $device, Request $request)
    {
        $device->load(['type', 'user']);
        
        $query = $device->commands()->with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('sent_at', '>=', $request->from);
        }

        $commands = $query->paginate(10)->withQueryString();
        
        // Simple data aggregation for charts
        $chartData = $device->data()
            ->latest('recorded_at')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $aiService = new \App\Services\AIService();
        $telemetryAnalysis = $aiService->analyzeTelemetry($device);

        return view('devices.show', compact('device', 'commands', 'chartData', 'telemetryAnalysis'));
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
        $oldUserId = $device->user_id;

        if ($request->hasFile('avatar')) {
            if ($device->avatar) {
                Storage::disk('public')->delete($device->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $device->update($data);

        if ($device->user_id && $device->user_id !== $oldUserId) {
            $user = User::find($device->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\DeviceAssignedNotification($device, auth()->user()));
            }
        }

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

        $oldUserId = $device->user_id;
        $device->update(['user_id' => $request->user_id]);
        
        if ($device->user_id && $device->user_id !== $oldUserId) {
            $user = User::find($device->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\DeviceAssignedNotification($device, auth()->user()));
            }
        }

        session()->flash('success', 'Device assignment updated.');
        return back();
    }

    public function toggleStatus(Device $device)
    {
        $this->authorize('update', $device);
        
        $newStatus = $device->status === 'active' ? 'inactive' : 'active';
        $device->update(['status' => $newStatus]);
        
        session()->flash('success', "Device " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully.");
        return back();
    }

    public function analytics()
    {
        $totalDevices = Device::count();
        $statusDistribution = Device::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        $typeDistribution = Device::join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->select('device_types.name', \DB::raw('count(*) as total'))
            ->groupBy('device_types.name')
            ->get();
            
        $recentDevices = Device::with(['type', 'user'])->latest()->take(10)->get();

        return view('admin.devices.analytics', compact('totalDevices', 'statusDistribution', 'typeDistribution', 'recentDevices'));
    }

    public function export()
    {
        $fileName = 'fleet_export_' . date('Y-m-d') . '.csv';
        $devices = Device::with(['type', 'user'])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Serial Number', 'Type', 'Status', 'Operator', 'Created At');

        $callback = function() use($devices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($devices as $device) {
                fputcsv($file, array(
                    $device->id,
                    $device->name,
                    $device->serial_number,
                    $device->type->name,
                    ucfirst($device->status),
                    $device->user ? $device->user->name : 'Unassigned',
                    $device->created_at->format('Y-m-d')
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
