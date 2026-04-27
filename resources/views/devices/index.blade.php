@extends('layouts.app')
@section('title', 'Devices Management')

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom bg-white">
        <form action="{{ request()->url() }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name or serial..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Device Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($deviceTypes as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    @role('admin')
    <div class="p-3 bg-light border-bottom d-flex justify-content-end">
        <a href="{{ route('admin.devices.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
            <i data-feather="plus" class="me-2" style="width: 16px;"></i> Add Device
        </a>
    </div>
    @endrole

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Type</th>
                    <th>Status</th>
                    @role('admin')
                    <th>Assigned To</th>
                    @endrole
                    <th>Last Seen</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <x-avatar :url="$device->avatar_url" :size="40" class="me-3" />
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $device->name }}</h6>
                                <span class="text-muted small">SN: {{ $device->serial_number }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i data-feather="{{ $device->type->icon ?? 'box' }}" class="text-muted me-2" style="width: 14px;"></i>
                            {{ $device->type->name }}
                        </div>
                    </td>
                    <td>
                        <x-status-badge :status="$device->status" />
                    </td>
                    @role('admin')
                    <td>
                        {{ $device->user ? $device->user->name : 'Unassigned' }}
                    </td>
                    @endrole
                    <td>
                        @if($device->last_seen_at)
                            <div class="small">
                                <div>{{ $device->last_seen_at->format('M d, Y') }}</div>
                                <div class="text-muted">{{ $device->last_seen_at->format('H:i:s') }}</div>
                            </div>
                        @else
                            <span class="text-muted small">Never</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.devices.show', $device) : route('operator.devices.show', $device) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="View Details">
                                <i data-feather="eye" style="width: 16px;"></i>
                            </a>
                            
                            @can('update', $device)
                            <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                <i data-feather="edit-2" style="width: 16px;"></i>
                            </a>
                            @endcan
                            
                            @can('delete', $device)
                            <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete">
                                    <i data-feather="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole('admin') ? 6 : 5 }}" class="text-center py-5 text-muted">No devices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-top bg-white">
        {{ $devices->links() }}
    </div>
</div>
@endsection
