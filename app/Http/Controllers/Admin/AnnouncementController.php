<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AnnouncementMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $roles = Role::all();
        
        $query = \App\Models\Announcement::with('sender')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $announcements = $query->paginate(10)->withQueryString();
        
        return view('admin.announcements.index', compact('users', 'roles', 'announcements'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:user,role',
            'user_ids' => 'required_if:target_type,user|array',
            'user_ids.*' => 'exists:users,id',
            'role_ids' => 'required_if:target_type,role|array',
            'role_ids.*' => 'exists:roles,id',
            'subject' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,scheduled,sent',
            'scheduled_at' => 'nullable|date',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max
        ]);

        $recipients = [];
        $targetIds = [];

        if ($request->target_type === 'user') {
            $users = User::whereIn('id', $request->user_ids)->get();
            $recipients = $users->pluck('email')->toArray();
            $targetIds = $request->user_ids;
        } else {
            $roles = Role::whereIn('id', $request->role_ids)->get();
            $roleNames = $roles->pluck('name')->toArray();
            $recipients = User::role($roleNames)->pluck('email')->toArray();
            $targetIds = $request->role_ids;
        }

        // Remove duplicates
        $recipients = array_unique($recipients);

        if (empty($recipients)) {
            return back()->with('error', 'No recipients found for the selected criteria.');
        }

        // Handle Attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements/attachments', 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Save to Database
        \App\Models\Announcement::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'target_type' => $request->target_type,
            'target_ids' => $targetIds,
            'status' => $request->status,
            'category' => $request->category,
            'scheduled_at' => $request->status === 'scheduled' ? $request->scheduled_at : null,
            'attachments' => count($attachmentPaths) > 0 ? $attachmentPaths : null,
            'sent_by' => auth()->id() ?? 1, 
        ]);

        // Send Email if status is 'sent'
        if ($request->status === 'sent') {
            foreach ($recipients as $email) {
                Mail::to($email)->send(new AnnouncementMail($request->subject, $request->message));
                // Note: to send attachments via email, the AnnouncementMail mailable needs to be updated.
            }
            return back()->with('success', 'Announcement sent successfully to ' . count($recipients) . ' recipient(s).');
        }

        $message = $request->status === 'draft' ? 'saved as draft' : 'scheduled';
        return back()->with('success', "Announcement $message successfully.");
    }
}
