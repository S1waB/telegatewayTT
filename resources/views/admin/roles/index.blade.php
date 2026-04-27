@extends('layouts.app')
@section('title', 'Roles Management')

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h6 class="mb-0 fw-bold">System Roles</h6>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-flex align-items-center">
            <i data-feather="plus" class="me-2" style="width: 18px;"></i> Add Role
        </a>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="fw-bold">{{ ucfirst($role->name) }}</td>
                    <td class="text-muted">{{ $role->description ?? 'No description provided' }}</td>
                    <td>
                        <span class="badge bg-secondary rounded-pill">{{ $role->users_count }} Users</span>
                    </td>
                    <td>
                        @if($role->name === 'admin')
                            <span class="badge bg-success bg-opacity-75">All Permissions</span>
                        @else
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <span class="badge bg-light text-dark border">+{{ $role->permissions->count() - 3 }} more</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                <i data-feather="edit-2" style="width: 16px;"></i>
                            </a>
                            @if($role->name !== 'admin')
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete">
                                    <i data-feather="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No roles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-top bg-white">
        {{ $roles->links() }}
    </div>
</div>
@endsection
