<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(100, max(1, (int)$perPage));
        
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return UserResource::collection($query->paginate($perPage));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->input('is_active', true);
        
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($data);
        $user->assignRole($request->role);

        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user->load('roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'You cannot delete yourself.'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
