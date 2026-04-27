@extends('layouts.app')
@section('title', 'Users Management')

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex" style="width: 300px;">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary bg-white" type="submit"><i data-feather="search" style="width: 16px;"></i></button>
            </div>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center">
            <i data-feather="plus" class="me-2" style="width: 18px;"></i> Add User
        </a>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <x-avatar :url="$user->avatar_url" :size="40" class="me-3" />
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                                <span class="text-muted small">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary text-white bg-opacity-75">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </td>
                    <td>
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                <i data-feather="edit-2" style="width: 16px;"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                    <i data-feather="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-top bg-white">
        {{ $users->links() }}
    </div>
</div>
@endsection
