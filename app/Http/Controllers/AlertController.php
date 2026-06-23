<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertAttachment;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Alert::with(['user', 'device']);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type Filter (General vs Device)
        if ($request->filled('type')) {
            if ($request->type === 'general') {
                $query->whereNull('device_id');
            } elseif ($request->type === 'device') {
                $query->whereNotNull('device_id');
            }
        }

        $alerts = $query->latest()->paginate(10)->withQueryString();

        // Statistics Calculation
        $statsQuery = Alert::query();
        if (!$user->hasRole('admin')) {
            $statsQuery->where('user_id', $user->id);
        }

        $totalAlerts = (clone $statsQuery)->count();
        $viewedAlerts = (clone $statsQuery)->whereIn('status', ['pending', 'viewed'])->count();
        $respondedAlerts = (clone $statsQuery)->whereNotNull('resolved_at')->count();
        
        $viewedPercentage = $totalAlerts > 0 ? round(($viewedAlerts / $totalAlerts) * 100) : 0;
        $responseRate = $totalAlerts > 0 ? round(($respondedAlerts / $totalAlerts) * 100) : 0;

        $devices = Device::where('user_id', $user->id)->get();
        if ($user->hasRole('admin')) {
            $devices = Device::all();
        }

        return view('alerts.index', compact(
            'alerts', 
            'devices', 
            'totalAlerts', 
            'viewedAlerts', 
            'respondedAlerts', 
            'viewedPercentage', 
            'responseRate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'device_id' => 'nullable|exists:devices,id',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

<<<<<<< HEAD
        // Resolve numeric device id to the device_id string (FK references devices.device_id)
        $deviceId = null;
        if ($request->filled('device_id')) {
            $device = Device::find($request->device_id);
            $deviceId = $device?->device_id;
        }

        $alert = Alert::create([
            'user_id' => auth()->id(),
            'device_id' => $deviceId,
=======
        $deviceId = $request->input('device_id');
        if ($deviceId === 'null' || $deviceId === '') {
            $deviceId = null;
        }

        $alertData = [
            'user_id' => auth()->id(),
>>>>>>> bbdaf4e (Sprint 3 updates and fixes)
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'not_viewed',
        ];

        if (!is_null($deviceId)) {
            $alertData['device_id'] = $deviceId;
        }

        $alert = Alert::create($alertData);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('alert_attachments', 'public');
                AlertAttachment::create([
                    'alert_id' => $alert->id,
                    'file_path' => $path,
                ]);
            }
        }

        session()->flash('success', 'Alert created successfully. An admin will review it shortly.');
        return back();
    }

    public function show(Alert $alert)
    {
        $this->authorizeView($alert);
        
        $alert->load(['user', 'device', 'messages.user']);
        
        // If admin views a 'not_viewed' alert, mark as 'viewed' or 'pending'?
        // The user asked for "not viewed, pending, viewed".
        if (auth()->user()->hasRole('admin') && $alert->status === 'not_viewed') {
            $alert->update(['status' => 'viewed']);
        }

        return view('alerts.show', compact('alert'));
    }

    public function respond(Request $request, Alert $alert)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'admin_response' => 'required|string',
            'status' => 'required|in:not_viewed,pending,viewed',
        ]);

        $alert->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
        ]);

        session()->flash('success', 'Response submitted and alert status updated.');
        return back();
    }

    public function updateStatus(Request $request, Alert $alert)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:not_viewed,pending,viewed',
        ]);

        $alert->update(['status' => $request->status]);

        session()->flash('success', 'Alert status updated.');
        return back();
    }

    public function storeMessage(Request $request, Alert $alert)
    {
        $this->authorizeView($alert);

        $request->validate([
            'message' => 'required|string',
        ]);

        $alert->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // If it's a user replying, mark as not_viewed or pending?
        // Let's mark as pending if an admin was already involved
        if (!auth()->user()->hasRole('admin') && $alert->status === 'viewed') {
            $alert->update(['status' => 'pending']);
        }

        session()->flash('success', 'Message sent.');
        return back();
    }

    private function authorizeView(Alert $alert)
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) return;
        if ($alert->user_id !== $user->id) abort(403);
    }
}
