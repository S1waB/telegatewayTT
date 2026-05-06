@extends('layouts.app')
@section('title', __('messages.admin_dashboard'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <x-stat-card :title="__('messages.total_users')" :value="$userCount" icon="users" color="primary" />
    </div>
    <div class="col-md-3">
        <x-stat-card :title="__('messages.total_devices')" :value="$deviceCount" icon="cpu" color="info" />
    </div>
    <div class="col-md-3">
        <x-stat-card :title="__('messages.active_devices')" :value="$activeDevices" icon="activity" color="success" />
    </div>
    <div class="col-md-3">
        <x-stat-card :title="__('messages.pending_commands')" :value="$pendingCommands" icon="clock" color="warning" />
    </div>
</div>

<!-- AI Fleet Health Insights -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #0d4a8a 0%, #1a1a2e 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-body bg-opacity-25 p-2 rounded-3 me-3">
                                <i class="fas fa-brain fa-2x"></i>
                            </div>
                            <h4 class="mb-0 fw-bold">{{ __('messages.ai_fleet_health') }}</h4>
                        </div>
                        <p class="text-white text-opacity-75 mb-4">{{ __('messages.analyzing_devices', ['count' => $aiFleetInsights['total_devices']]) }}</p>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="bg-body bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                    <div class="text-white text-opacity-50 small mb-1">{{ __('messages.fleet_health_score') }}</div>
                                    <div class="h3 mb-0">{{ $aiFleetInsights['average_health'] }}%</div>
                                    <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar bg-success" style="width: {{ $aiFleetInsights['average_health'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-body bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                    <div class="text-white text-opacity-50 small mb-1">{{ __('messages.critical_risk_alerts') }}</div>
                                    <div class="h3 mb-0 text-{{ $aiFleetInsights['critical_count'] > 0 ? 'danger' : 'white' }}">
                                        {{ $aiFleetInsights['critical_count'] }}
                                    </div>
                                    <div class="small mt-1 {{ $aiFleetInsights['critical_count'] > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $aiFleetInsights['critical_count'] > 0 ? __('messages.immediate_action') : __('messages.systems_stable') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-body bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                    <div class="text-white text-opacity-50 small mb-1">{{ __('messages.predicted_maintenance') }}</div>
                                    <div class="h3 mb-0">{{ $aiFleetInsights['status_distribution']['maintenance'] }}</div>
                                    <div class="small mt-1 text-warning">{{ __('messages.upcoming_interventions') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <div class="position-relative d-inline-block">
                            <canvas id="aiPredictionGauge" width="180" height="180"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <div class="h2 mb-0 fw-bold">{{ $aiFleetInsights['average_health'] }}%</div>
                                <div class="small text-white text-opacity-50">Global</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Strategic Advisor -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card tg-card border-0 shadow-sm">
            <div class="card-header bg-body p-4 border-bottom d-flex align-items-center">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                <h6 class="mb-0 fw-bold">{{ __('messages.ai_strategic_advisor') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($adminStrategicAdvice as $advice)
                    <div class="col-md-4">
                        <div class="d-flex align-items-start p-3 rounded-3 bg-{{ $advice['level'] }} bg-opacity-10 border border-{{ $advice['level'] }} border-opacity-25 h-100">
                            <div class="text-{{ $advice['level'] }} me-3">
                                <i class="fas fa-{{ $advice['icon'] }} fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $advice['title'] }}</h6>
                                <p class="small text-muted mb-0">{{ $advice['message'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">{{ __('messages.operator_activity') }}</h6>
                        <div style="height: 250px;">
                            <canvas id="operatorActivityChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">{{ __('messages.alert_response_trend') }}</h6>
                        <div style="height: 250px;">
                            <canvas id="alertResponseTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="tg-table-container">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">{{ __('messages.recent_commands') }}</h6>
                <a href="{{ route('admin.commands.history') }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table tg-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.device') }}</th>
                            <th>{{ __('messages.sent_by') }}</th>
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
                            <td>{{ $command->user->name }}</td>
                            <td><code class="text-secondary">{{ Str::limit(json_encode($command->payload), 30) }}</code></td>
                            <td>{!! $command->status_badge !!}</td>
                            <td class="text-muted small">{{ $command->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">{{ __('messages.no_commands_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card tg-card h-100">
            <div class="card-header bg-body p-4 border-bottom">
                <h6 class="mb-0 fw-bold">{{ __('messages.device_status') }}</h6>
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
        // Device Status Chart
        const ctx = document.getElementById('deviceStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    '{{ __('messages.active') }}', 
                    '{{ __('messages.inactive') }}', 
                    '{{ __('messages.maintenance') }}'
                ],
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
                    cutout: '75%'
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

        // AI Gauge Chart
        const aiCtx = document.getElementById('aiPredictionGauge').getContext('2d');
        new Chart(aiCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $aiFleetInsights['average_health'] }}, {{ 100 - $aiFleetInsights['average_health'] }}],
                    backgroundColor: ['#22c55e', 'rgba(255, 255, 255, 0.1)'],
                    borderWidth: 0,
                    circumference: 270,
                    rotation: 225,
                    cutout: '85%',
                    borderRadius: 10
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });

        // Operator Activity Chart
        const opCtx = document.getElementById('operatorActivityChart').getContext('2d');
        new Chart(opCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($adminStrategicChartData['userPerformance']['labels']) !!},
                datasets: [{
                    label: 'Commands',
                    data: {!! json_encode($adminStrategicChartData['userPerformance']['data']) !!},
                    backgroundColor: '#0d4a8a',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Alert Response Trend Chart
        const respCtx = document.getElementById('alertResponseTrendChart').getContext('2d');
        new Chart(respCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($adminStrategicChartData['responseTrend']['labels']) !!},
                datasets: [{
                    label: 'Avg Response Time (min)',
                    data: {!! json_encode($adminStrategicChartData['responseTrend']['data']) !!},
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
