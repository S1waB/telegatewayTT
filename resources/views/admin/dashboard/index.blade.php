@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <x-stat-card title="Total Users" :value="$userCount" icon="users" color="primary" />
    </div>
    <div class="col-md-3">
        <x-stat-card title="Total Devices" :value="$deviceCount" icon="cpu" color="info" />
    </div>
    <div class="col-md-3">
        <x-stat-card title="Active Devices" :value="$activeDevices" icon="activity" color="success" />
    </div>
    <div class="col-md-3">
        <x-stat-card title="Pending Commands" :value="$pendingCommands" icon="clock" color="warning" />
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="tg-table-container">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Recent Commands</h6>
                <a href="{{ route('admin.commands.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table tg-table">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Sent By</th>
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
                            <td>{{ $command->user->name }}</td>
                            <td><code class="text-secondary">{{ Str::limit(json_encode($command->payload), 30) }}</code></td>
                            <td>{!! $command->status_badge !!}</td>
                            <td class="text-muted small">{{ $command->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No commands found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card tg-card h-100">
            <div class="card-header bg-white p-4 border-bottom">
                <h6 class="mb-0 fw-bold">Device Status</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center p-4">
                <canvas id="deviceStatusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('deviceStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'Maintenance'],
                datasets: [{
                    data: [
                        {{ $activeDevices }}, 
                        {{ $deviceCount - $activeDevices - \App\Models\Device::where('status', 'maintenance')->count() }},
                        {{ \App\Models\Device::where('status', 'maintenance')->count() }}
                    ],
                    backgroundColor: [
                        '#198754', // success
                        '#6c757d', // secondary
                        '#ffc107'  // warning
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                    }
                }
            }
        });
    });
</script>
@endpush
