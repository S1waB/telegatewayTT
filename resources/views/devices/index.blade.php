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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-gear-wide-connected me-2 text-primary"></i>Platform Overview</h6>
                    @role('admin')
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.devices.analytics') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 rounded-pill shadow-sm">
                            <i class="bi bi-pie-chart"></i> Fleet Analytics
                        </a>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createDeviceModal">
                            <i class="bi bi-plus-lg"></i> Add New Device
                        </button>
                    </div>
                    @endrole
                </div>
                <form action="{{ request()->url() }}" method="GET" class="row g-2 align-items-center mt-2">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by name, serial, or operator..." value="{{ request('search') }}" style="font-size: 0.9rem; height: 42px;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select form-select-sm border-secondary-subtle" style="height: 42px; font-size: 0.85rem;">
                            <option value="">All Types</option>
                            @foreach($deviceTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm border-secondary-subtle" style="height: 42px; font-size: 0.85rem;">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center px-3" style="height: 42px; min-width: 100px;">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                            @if(request()->anyFilled(['search', 'type', 'status']))
                                <a href="{{ request()->url() }}" class="btn btn-light border d-flex align-items-center justify-content-center px-3" style="height: 42px;" title="Reset">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
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
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.devices.show', $device) : route('operator.devices.show', $device) }}" class="btn btn-sm btn-outline-info shadow-sm" title="Intelligence Dashboard">
                                <i class="bi bi-graph-up"></i>
                            </a>
                            
                            @can('update', $device)
                            {{-- Activation Toggle --}}
                            <form action="{{ route('admin.devices.toggle-status', $device) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $device->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} shadow-sm" title="{{ $device->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-{{ $device->status === 'active' ? 'power' : 'lightning-charge' }}"></i>
                                </button>
                            </form>

                            {{-- Edit Modal Trigger --}}
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary shadow-sm edit-device-btn" 
                                    title="Modify Device"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editDeviceModal"
                                    data-device="{{ json_encode([
                                        'id' => $device->id,
                                        'name' => $device->name,
                                        'serial_number' => $device->serial_number,
                                        'device_type_id' => $device->device_type_id,
                                        'user_id' => $device->user_id,
                                        'status' => $device->status,
                                        'description' => $device->description,
                                        'avatar_url' => $device->avatar_url
                                    ]) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endcan
                            
                            @can('delete', $device)
                            <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this device permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
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
{{-- ── Edit Device Modal ── --}}
@role('admin')
<div class="modal fade" id="editDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="editDeviceForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-primary">Modify Device Configuration</h5>
                        <p class="text-muted small mb-0">Update the parameters for this IoT asset</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="row g-4">
                        <div class="col-md-12 text-center mb-2">
                            <div class="position-relative d-inline-block">
                                <img id="editAvatarPreview" src="" class="rounded-3 shadow-sm" width="80" height="80" style="object-fit: cover; border: 3px solid #E8F1FA;">
                                <label for="editAvatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 cursor-pointer shadow" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="bi bi-camera-fill" style="font-size: 12px;"></i>
                                </label>
                                <input type="file" id="editAvatar" name="avatar" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Device Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Serial Number</label>
                            <input type="text" name="serial_number" id="edit_serial" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Model/Type</label>
                            <select name="device_type_id" id="edit_type" class="form-select" required>
                                @foreach($deviceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assigned Operator</label>
                            <select name="user_id" id="edit_user" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Current Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Internal Notes</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 mt-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">Update Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endrole

@push('scripts')
<script>
document.querySelectorAll('.edit-device-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.device);
        const form = document.getElementById('editDeviceForm');
        
        // Set action URL
        form.action = `/admin/devices/${d.id}`;
        
        // Populate fields
        document.getElementById('edit_name').value = d.name;
        document.getElementById('edit_serial').value = d.serial_number;
        document.getElementById('edit_type').value = d.device_type_id;
        document.getElementById('edit_user').value = d.user_id || '';
        document.getElementById('edit_status').value = d.status;
        document.getElementById('edit_description').value = d.description || '';
        
        // Preview avatar
        document.getElementById('editAvatarPreview').src = d.avatar_url;
    });
});

function previewEditImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editAvatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection
