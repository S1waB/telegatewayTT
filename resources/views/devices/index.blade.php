@extends('layouts.app')
@section('title', 'Devices Management')

@section('content')
{{-- Stats Section --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">Active Devices</h6>
                    <i class="bi bi-broadcast fs-4"></i>
                </div>
                <h2 class="fw-bold mb-1">{{ $activePercentage }}%</h2>
                <p class="small mb-0 opacity-75">{{ $activeDevices }} of {{ $totalDevices }} online</p>
                <div class="progress mt-3 bg-white bg-opacity-25" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: {{ $activePercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">Platform Overview</h6>
                    @role('admin')
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4" data-bs-toggle="modal" data-bs-target="#createDeviceModal">
                        <i class="bi bi-plus-lg"></i> Add New Device
                    </button>
                    @endrole
                </div>
                <form action="{{ request()->url() }}" method="GET" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search devices..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select bg-light border-0">
                            <option value="">All Types</option>
                            @foreach($deviceTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select bg-light border-0">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="tg-table-container">
    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Device Details</th>
                    <th>Type</th>
                    <th>Status</th>
                    @role('admin')
                    <th>Assigned Operator</th>
                    @endrole
                    <th>Last Activity</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="position-relative">
                                <x-avatar :url="$device->avatar_url" :size="40" class="rounded-3 shadow-sm" />
                                <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $device->status === 'active' ? 'success' : ($device->status === 'maintenance' ? 'warning' : 'danger') }} border border-white rounded-circle"></span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">{{ $device->name }}</h6>
                                <span class="text-muted small">SN: {{ $device->serial_number }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded-2 me-2">
                                <i class="bi bi-{{ $device->type->icon ?? 'cpu' }} text-primary"></i>
                            </div>
                            {{ $device->type->name }}
                        </div>
                    </td>
                    <td>
                        <x-status-badge :status="$device->status" />
                    </td>
                    @role('admin')
                    <td>
                        @if($device->user)
                            <div class="d-flex align-items-center">
                                <x-avatar :url="$device->user->avatar_url" :size="24" class="rounded-circle me-2 border shadow-sm" />
                                <div class="badge bg-light text-primary border px-2 py-1 fw-semibold" style="font-size: 11px;">
                                    {{ $device->user->name }}
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center text-muted opacity-50">
                                <i class="bi bi-person-dash me-2"></i>
                                <span class="small">Unassigned</span>
                            </div>
                        @endif
                    </td>
                    @endrole
                    <td>
                        @if($device->last_seen_at)
                            <div class="small">
                                <div class="fw-bold">{{ $device->last_seen_at->diffForHumans() }}</div>
                                <div class="text-muted">{{ $device->last_seen_at->format('M d, H:i') }}</div>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.devices.show', $device) : route('operator.devices.show', $device) }}" class="btn btn-sm btn-outline-info" title="Intelligence Dashboard">
                                <i class="bi bi-graph-up"></i>
                            </a>
                            
                            @can('update', $device)
                            <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            
                            @can('delete', $device)
                            <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this device permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole('admin') ? 6 : 5 }}" class="text-center py-5 text-muted">
                        <i class="bi bi-broadcast-pin display-4 opacity-25 mb-3 d-block"></i>
                        No devices match your current filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($devices->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Monitoring {{ $devices->firstItem() }}–{{ $devices->lastItem() }} of {{ $devices->total() }} devices
        </span>
        {{ $devices->links() }}
    </div>
    @endif
</div>

{{-- ── Create Device Modal ── --}}
@role('admin')
<div class="modal fade" id="createDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.devices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-primary">Provision New Device</h5>
                        <p class="text-muted small mb-0">Register a new IoT asset to the gateway</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Device Identity</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Gateway #01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="e.g. TG-XXXX-XXXX" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Model/Type</label>
                            <select name="device_type_id" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($deviceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assign Operator</label>
                            <select name="user_id" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Initial Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Device Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Internal Notes</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Additional deployment details..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 mt-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">Register Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endrole
@endsection
