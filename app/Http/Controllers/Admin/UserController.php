<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->search($request->get('search'))
            ->filterByRole($request->get('role'))
            ->filterByStatus($request->get('status'))
            ->when($request->get('sort') === 'last_active', fn($q) => $q->orderByDesc('last_active_at'))
            ->when($request->get('sort') === 'name',        fn($q) => $q->orderBy('name'))
            ->when($request->get('sort') === 'newest',      fn($q) => $q->orderByDesc('created_at'))
            ->when(!$request->get('sort'),                  fn($q) => $q->orderByDesc('created_at'));

        $users     = $query->paginate(10)->withQueryString();
        $roles     = Role::all();
        $totalUsers   = User::count();
        $activeUsers  = User::where('is_active', true)->count();
        $activePct    = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;

        return view('admin.users.index', compact('users', 'roles', 'totalUsers', 'activeUsers', 'activePct'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $plainPassword = \Illuminate\Support\Str::random(10);
        $validated['password'] = Hash::make($plainPassword);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);
        $user->assignRole($request->role);

        // Send welcome email with credentials (send synchronously for reliable dev feedback)
        try {
            Mail::to($user->email)->queue(new NewUserWelcomeMail($user, $plainPassword));
            session()->flash('success', "User {$user->name} created. Welcome email queued to {$user->email}.");
        } catch (\Throwable $exception) {
            \Log::error('Failed to queue welcome email', [
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
            session()->flash('warning', "User {$user->name} created, but the welcome email could not be queued. Check logs for details.");
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }
        
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);
        $user->syncRoles([$request->role]);

        session()->flash('success', 'User updated successfully.');
        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            session()->flash('error', 'You cannot delete yourself.');
            return back();
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
        return redirect()->route('admin.users.index');
    }

    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            session()->flash('error', 'You cannot deactivate yourself.');
            return back();
        }
        
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'User status toggled.');
        return back();
    }

    public function resetPassword(User $user)
    {
        $newPassword = \Illuminate\Support\Str::random(10);
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        Mail::to($user->email)->queue(new \App\Mail\AdminPasswordResetMail($user, $newPassword));

        session()->flash('success', "A new temporary password has been generated and emailed to {$user->email}.");
        return back();
    }

    public function analytics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $roleDistribution = \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', \DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->get();
            
        $recentLogins = User::whereNotNull('last_active_at')->orderByDesc('last_active_at')->take(10)->get();

        return view('admin.users.analytics', compact('totalUsers', 'activeUsers', 'roleDistribution', 'recentLogins'));
    }

    public function export()
    {
        $fileName = 'users_export_' . date('Y-m-d') . '.csv';
        $users = User::with('roles')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined');

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['ID']    = $user->id;
                $row['Name']  = $user->name;
                $row['Email'] = $user->email;
                $row['Phone'] = $user->phone_number;
                $row['Role']  = $user->roles->pluck('name')->implode(', ');
                $row['Status']= $user->is_active ? 'Active' : 'Inactive';
                $row['Joined']= $user->created_at->format('Y-m-d');

                fputcsv($file, array($row['ID'], $row['Name'], $row['Email'], $row['Phone'], $row['Role'], $row['Status'], $row['Joined']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
