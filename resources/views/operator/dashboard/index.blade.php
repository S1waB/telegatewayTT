@extends('layouts.app')
@section('title', __('messages.operator_dashboard'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <x-stat-card :title="__('messages.my_devices')" :value="$myDevicesCount" icon="cpu" color="primary" />
    </div>
    <div class="col-md-4">
        <x-stat-card :title="__('messages.commands_sent')" :value="$myCommandsCount" icon="activity" color="info" />
    </div>
    <div class="col-md-4">
        <x-stat-card :title="__('messages.success_rate')" :value="$successRate . '%'" icon="check-circle" color="success" />
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Health Overview & Advice -->
    <div class="col-md-4 order-md-2">
        <div class="card tg-card border-0 shadow-sm h-100">
            <div class="card-header bg-body p-4 border-bottom">
                <h6 class="mb-0 fw-bold">{{ __('messages.health_distribution') }}</h6>
            </div>
            <div class="card-body">
                <div style="height: 200px;">
                    <canvas id="healthDistributionChart"></canvas>
                </div>
                
                <hr class="my-4">
                
                <h6 class="fw-bold mb-3 small text-uppercase text-muted">{{ __('messages.top_maintenance_priority') }}</h6>
                @php 
                    $priorityDevice = $myDevices->sortByDesc('ai_failure_probability')->first();
                @endphp
                @if($priorityDevice && $priorityDevice->ai_failure_probability > 0)
                    <div class="p-3 rounded-3 bg-{{ $priorityDevice->ai_advice['level'] }} bg-opacity-10 border border-{{ $priorityDevice->ai_advice['level'] }} border-opacity-25">
                        <div class="d-flex align-items-center mb-2">
                            <x-avatar :url="$priorityDevice->avatar_url" :size="24" class="me-2" />
                            <span class="fw-bold small">{{ $priorityDevice->name }}</span>
                            <span class="ms-auto badge bg-{{ $priorityDevice->ai_advice['level'] }}">{{ $priorityDevice->ai_failure_probability }}%</span>
                        </div>
                        <div class="small fw-bold text-{{ $priorityDevice->ai_advice['level'] }} mb-1">{{ $priorityDevice->ai_advice['title'] }}</div>
                        <p class="small text-muted mb-0">{{ $priorityDevice->ai_advice['message'] }}</p>
                    </div>
                @else
                    <p class="small text-muted text-center py-4">{{ __('messages.no_critical_issues') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Devices Health Table -->
    <div class="col-md-8 order-md-1">
        <div class="card tg-card border-0 shadow-sm">
            <div class="card-header bg-body p-4 border-bottom d-flex align-items-center">
                <i class="fas fa-magic text-primary me-2"></i>
                <h6 class="mb-0 fw-bold">{{ __('messages.ai_predictive_maintenance') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-body-tertiary">
                            <tr>
                                <th class="ps-4">{{ __('messages.device') }}</th>
                                <th>{{ __('messages.failure_risk') }}</th>
                                <th class="text-end pe-4">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDevices as $device)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <x-avatar :url="$device->avatar_url" :size="32" class="me-2" />
                                        <div>
                                            <div class="fw-bold">{{ $device->name }}</div>
                                            <div class="small text-muted">{{ $device->serial_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-{{ $device->ai_failure_probability > 70 ? 'danger' : ($device->ai_failure_probability > 40 ? 'warning' : 'success') }}" 
                                                 style="width: {{ $device->ai_failure_probability }}%"></div>
                                        </div>
                                        <span class="small fw-bold">{{ $device->ai_failure_probability }}%</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('operator.devices.show', $device) }}" class="btn btn-sm btn-light border">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="tg-table-container">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">{{ __('messages.recent_commands') }}</h6>
                <a href="{{ route('operator.commands.history') }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table tg-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.device') }}</th>
                            <th>{{ __('messages.payload') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCommands as $command)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$command->device?->avatar_url" :size="32" class="me-2" />
                                    <span>{{ $command->device?->name ?? __('messages.unknown_device') }}</span>
                                </div>
                            </td>
                            <td><code class="text-secondary">{{ Str::limit(json_encode($command->payload), 40) }}</code></td>
                            <td>{!! $command->status_badge !!}</td>
                            <td class="text-muted small">{{ $command->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.no_commands_sent') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('healthDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($healthDistribution['labels']) !!},
                datasets: [{
                    data: {!! json_encode($healthDistribution['data']) !!},
                    backgroundColor: {!! json_encode($healthDistribution['colors']) !!},
                    borderWidth: 0,
                    cutout: '80%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, usePointStyle: true, pointStyle: 'circle', font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endpush
