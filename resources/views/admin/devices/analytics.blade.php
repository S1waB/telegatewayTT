@extends('layouts.app')
@section('title', 'Device Fleet Intelligence')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-light border">
        <i class="bi bi-arrow-left me-1"></i> Back to Fleet
    </a>
    <a href="{{ route('admin.devices.export') }}" class="btn btn-sm btn-dark shadow-sm">
        <i class="bi bi-download me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card tg-card border-0 h-100">
            <div class="card-header bg-white p-4 border-bottom">
                <h6 class="fw-bold mb-0">Connectivity Status</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="statusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card tg-card border-0 h-100">
            <div class="card-header bg-white p-4 border-bottom">
                <h6 class="fw-bold mb-0">Hardware Composition</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="typeChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="tg-table-container">
            <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Fleet Real-time Overview</h6>
                <div class="small text-muted">Total Hardware: {{ $totalDevices }} Assets</div>
            </div>
            <div class="table-responsive">
                <table class="table tg-table mb-0">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Category</th>
                            <th>Operator</th>
                            <th>Health Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentDevices as $device)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$device->avatar_url" :size="32" class="me-2" />
                                    <div>
                                        <div class="fw-medium">{{ $device->name }}</div>
                                        <div class="small text-muted">SN: {{ $device->serial_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $device->type->name }}</span>
                            </td>
                            <td>
                                @if($device->user)
                                    <div class="small fw-medium">{{ $device->user->name }}</div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>{!! $device->status_badge !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: @json($statusDistribution->pluck('status')),
                datasets: [{
                    data: @json($statusDistribution->pluck('total')),
                    backgroundColor: ['#198754', '#dc3545', '#ffc107', '#0dcaf0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: @json($typeDistribution->pluck('name')),
                datasets: [{
                    label: 'Device Count',
                    data: @json($typeDistribution->pluck('total')),
                    backgroundColor: '#1A6FBF',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
@endsection
