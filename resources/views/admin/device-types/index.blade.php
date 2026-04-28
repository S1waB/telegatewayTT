@extends('layouts.app')
@section('title', 'Device Types')

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h6 class="mb-0 fw-bold">Device Types Definition</h6>
        <a href="{{ route('admin.device-types.create') }}" class="btn btn-primary d-flex align-items-center">
            <i data-feather="plus" class="me-2" style="width: 18px;"></i> Add Type
        </a>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Devices Count</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deviceTypes as $type)
                <tr>
                    <td>
                        <div class="icon-circle bg-light d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; color: var(--tg-primary);">
                            <i data-feather="{{ $type->icon ?? 'box' }}" style="width: 20px;"></i>
                        </div>
                    </td>
                    <td class="fw-bold">{{ $type->name }}</td>
                    <td class="text-muted">{{ $type->description ?? 'No description' }}</td>
                    <td>
                        <span class="badge bg-secondary rounded-pill">{{ $type->devices_count }} Devices</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.device-types.edit', $type) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                <i data-feather="edit-2" style="width: 16px;"></i>
                            </a>
                            <form action="{{ route('admin.device-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this device type?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" {{ $type->devices_count > 0 ? 'disabled' : '' }}>
                                    <i data-feather="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No device types found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($deviceTypes->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Showing {{ $deviceTypes->firstItem() }}–{{ $deviceTypes->lastItem() }} of {{ $deviceTypes->total() }} types
        </span>
        {{ $deviceTypes->links() }}
    </div>
    @endif
</div>
@endsection
