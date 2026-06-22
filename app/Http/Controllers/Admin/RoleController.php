<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Role::withCount('users')->with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('permission')) {
            $query->whereHas('permissions', function ($q) use ($request) {
                $q->where('id', $request->permission);
            });
        }

        $roles = $query->paginate(10)->withQueryString();
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
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

        session()->flash('success', 'Role created successfully.');
        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        session()->flash('success', 'Role updated successfully.');
        return redirect()->route('admin.roles.index');
    }


    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            session()->flash('error', 'Cannot delete the admin role.');
            return back();
        }
        
        $role->delete();
        session()->flash('success', 'Role deleted successfully.');
        return redirect()->route('admin.roles.index');
    }
}
