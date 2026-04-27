<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $query->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($data);
        $user->assignRole($request->role);

        session()->flash('success', 'User created successfully.');
        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
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
}
