@extends('layouts.app')
@section('title', __('messages.device_fleet_intelligence'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-light border">
        <i class="bi bi-arrow-left me-1"></i> {{ __('messages.back_to_fleet') }}
    </a>
    <a href="{{ route('admin.devices.export') }}" class="btn btn-sm btn-dark shadow-sm">
        <i class="bi bi-download me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card tg-card border-0 h-100">
            <div class="card-header bg-body p-4 border-bottom">
                <h6 class="fw-bold mb-0">{{ __('messages.connectivity_status') }}</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="statusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card tg-card border-0 h-100">
            <div class="card-header bg-body p-4 border-bottom">
                <h6 class="fw-bold mb-0">{{ __('messages.hardware_composition') }}</h6>
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
            <div class="p-4 border-bottom bg-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">{{ __('messages.fleet_realtime_overview') }}</h6>
                <div class="small text-muted">{{ __('messages.total_hardware_assets', ['count' => $totalDevices]) }}</div>
            </div>
            <div class="table-responsive">
                <table class="table tg-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.asset') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.operator') }}</th>
                            <th>{{ __('messages.health_status') }}</th>
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
                                <span class="badge bg-body-tertiary text-body border">{{ $device->type->name }}</span>
                            </td>
                            <td>
                                @if($device->user)
                                    <div class="small fw-medium">{{ $device->user->name }}</div>
                                @else
                                    <span class="text-muted small">{{ __('messages.unassigned') }}</span>
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
                labels: [
                    '{{ __('messages.active') }}', 
                    '{{ __('messages.inactive') }}', 
                    '{{ __('messages.maintenance') }}',
                    '{{ __('messages.other') }}'
                ],
                datasets: [{
                    data: [
                        {{ $statusDistribution->where('status', 'active')->first()->total ?? 0 }},
                        {{ $statusDistribution->where('status', 'inactive')->first()->total ?? 0 }},
                        {{ $statusDistribution->where('status', 'maintenance')->first()->total ?? 0 }},
                        {{ $statusDistribution->whereNotIn('status', ['active', 'inactive', 'maintenance'])->sum('total') }}
                    ],
                    backgroundColor: ['#198754', '#6c757d', '#ffc107', '#0dcaf0'],
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
                    label: '{{ __('messages.devices_count') }}',
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
