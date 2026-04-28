@extends('layouts.app')
@section('title', 'Command History')

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom bg-white">
        <form action="{{ request()->url() }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Device</label>
                <select name="device_id" class="form-select select2">
                    <option value="">All Devices</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>{{ $device->name }} ({{ $device->serial_number }})</option>
                    @endforeach
                </select>
            </div>
            
            @role('admin')
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">User</label>
                <select name="user_id" class="form-select select2">
                    <option value="">All Users</option>
                    @php $users = \App\Models\User::all(); @endphp
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endrole

            <div class="col-md-{{ auth()->user()->hasRole('admin') ? '2' : '3' }}">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th>Command ID</th>
                    <th>Device</th>
                    <th>Sent By</th>
                    <th>Payload</th>
                    <th>Status</th>
                    <th>Sent At</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commands as $command)
                <tr>
                    <td class="text-muted small">#{{ $command->id }}</td>
                    <td>
                        <a href="{{ auth()->user()->hasRole('admin') ? route('admin.devices.show', $command->device) : route('operator.devices.show', $command->device) }}" class="text-decoration-none fw-medium text-dark">
                            {{ $command->device->name }}
                        </a>
                    </td>
                    <td>{{ $command->user->name }}</td>
                    <td><code class="text-secondary">{{ json_encode($command->payload) }}</code></td>
                    <td>{!! $command->status_badge !!}</td>
                    <td class="small">
                        @if($command->sent_at)
                            {{ $command->sent_at->format('M d, H:i:s') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($command->response)
                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#responseModal{{ $command->id }}">
                                View
                            </button>
                            
                            <!-- Response Modal -->
                            <div class="modal fade" id="responseModal{{ $command->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold">Command Response</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pt-0">
                                            <div class="bg-light p-3 rounded font-monospace small" style="white-space: pre-wrap; word-break: break-all;">
                                                {{ $command->response }}
                                            </div>
                                            <div class="text-muted small mt-2">
                                                Received: {{ $command->response_at ? $command->response_at->format('M d, Y H:i:s') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small">No response</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No commands found matching your criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($commands->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Showing {{ $commands->firstItem() }}–{{ $commands->lastItem() }} of {{ $commands->total() }} commands
        </span>
        {{ $commands->links() }}
    </div>
    @endif
</div>
@endsection
