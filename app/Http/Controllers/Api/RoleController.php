<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-roles');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(100, max(1, (int)$perPage));
        
        return RoleResource::collection(Role::with('permissions')->withCount('users')->paginate($perPage));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return new RoleResource($role->load('permissions'));
    }

    public function show(Role $role)
    {
        return new RoleResource($role->load('permissions')->loadCount('users'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return new RoleResource($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return response()->json(['error' => 'Cannot delete the admin role.'], 403);
        }
        
        $role->delete();
        return response()->json(null, 204);
    }
}
