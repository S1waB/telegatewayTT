@extends('layouts.app')
@section('title', 'Operator Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <x-stat-card title="My Devices" :value="$myDevicesCount" icon="cpu" color="primary" />
    </div>
    <div class="col-md-4">
        <x-stat-card title="Commands Sent" :value="$myCommandsCount" icon="activity" color="info" />
    </div>
    <div class="col-md-4">
        <x-stat-card title="Success Rate" :value="$successRate . '%'" icon="check-circle" color="success" />
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="tg-table-container">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Recent Commands</h6>
                <a href="{{ route('operator.commands.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table tg-table">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Payload</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCommands as $command)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$command->device->avatar_url" :size="32" class="me-2" />
                                    <span>{{ $command->device->name }}</span>
                                </div>
                            </td>
                            <td><code class="text-secondary">{{ Str::limit(json_encode($command->payload), 40) }}</code></td>
                            <td>{!! $command->status_badge !!}</td>
                            <td class="text-muted small">{{ $command->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No commands sent yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
