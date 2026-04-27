@extends('layouts.app')
@section('title', 'Device Details')

@section('content')
<div class="row g-4 mb-4">
    <!-- Device Info Card -->
    <div class="col-md-4">
        <div class="card tg-card h-100 border-0">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <x-avatar :url="$device->avatar_url" :size="100" class="mb-3" />
                    <h5 class="fw-bold mb-1">{{ $device->name }}</h5>
                    <p class="text-muted small mb-3">SN: {{ $device->serial_number }}</p>
                    <x-status-badge :status="$device->status" />
                </div>
                
                <hr class="my-4">
                
                <div class="text-start">
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="{{ $device->type->icon ?? 'box' }}"></i></div>
                        <div>
                            <div class="small text-muted">Type</div>
                            <div class="fw-medium">{{ $device->type->name }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="map-pin"></i></div>
                        <div>
                            <div class="small text-muted">Location</div>
                            <div class="fw-medium">{{ $device->location ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="wifi"></i></div>
                        <div>
                            <div class="small text-muted">IP Address</div>
                            <div class="fw-medium">{{ $device->ip_address ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="user"></i></div>
                        <div>
                            <div class="small text-muted">Assigned To</div>
                            <div class="fw-medium">{{ $device->user ? $device->user->name : 'Unassigned' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @can('sendCommand', $device)
            <div class="card-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#sendCommandModal">
                    <i data-feather="terminal" class="me-2" style="width: 16px;"></i> Send Command
                </button>
            </div>
            @endcan
        </div>
    </div>
    
    <!-- Data Chart -->
    <div class="col-md-8">
        <div class="card tg-card h-100 border-0">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Recent Telemetry Data</h6>
                @if($device->last_seen_at)
                    <span class="badge bg-light text-dark border">Last seen: {{ $device->last_seen_at->diffForHumans() }}</span>
                @else
                    <span class="badge bg-light text-dark border">Never seen</span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($chartData->count() > 0)
                    <canvas id="telemetryChart" style="min-height: 300px; max-height: 400px;"></canvas>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                        <i data-feather="bar-chart-2" style="width: 48px; height: 48px; opacity: 0.2;" class="mb-3"></i>
                        <p>No telemetry data available for this device.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Commands -->
<div class="row">
    <div class="col-md-12">
        <div class="tg-table-container">
            <div class="p-4 border-bottom">
                <h6 class="mb-0 fw-bold">Command History (Last 10)</h6>
            </div>
            <div class="table-responsive">
                <table class="table tg-table mb-0">
                    <thead>
                        <tr>
                            <th>Sent By</th>
                            <th>Payload</th>
                            <th>Status</th>
                            <th>Response</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCommands as $command)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$command->user->avatar_url" :size="32" class="me-2" />
                                    <span>{{ $command->user->name }}</span>
                                </div>
                            </td>
                            <td><code class="text-secondary">{{ json_encode($command->payload) }}</code></td>
                            <td>{!! $command->status_badge !!}</td>
                            <td>
                                @if($command->response)
                                    <code class="text-secondary">{{ Str::limit($command->response, 30) }}</code>
                                    <button class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="tooltip" title="{{ $command->response }}">
                                        <i data-feather="info" style="width: 14px;"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $command->created_at->format('M d, H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No commands have been sent to this device.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@can('sendCommand', $device)
<!-- Send Command Modal -->
<div class="modal fade" id="sendCommandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Send Command</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-4">Send a JSON payload to <strong>{{ $device->name }}</strong>. The device must be online to process the command.</p>
                
                @if(auth()->user()->hasRole('admin'))
                    <form action="{{ route('admin.commands.store', ['device' => $device]) }}" method="POST" id="commandForm">
                @else
                    <form action="{{ route('operator.commands.store', ['device' => $device]) }}" method="POST" id="commandForm">
                @endif
                    @csrf
                    <input type="hidden" name="device_id" value="{{ $device->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">JSON Payload</label>
                        <textarea name="payload" class="form-control font-monospace @error('payload') is-invalid @enderror" rows="5" required>{{ old('payload', "{\n  \"action\": \"ping\"\n}") }}</textarea>
                        @error('payload')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="commandForm" class="btn btn-primary d-flex align-items-center">
                    <i data-feather="send" class="me-2" style="width: 16px;"></i> Send Now
                </button>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
@if($chartData->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('telemetryChart').getContext('2d');
        
        // Group by metric
        const metrics = {};
        const labels = [];
        
        @foreach($chartData as $data)
            if(!labels.includes('{{ $data->recorded_at->format('H:i') }}')) {
                labels.push('{{ $data->recorded_at->format('H:i') }}');
            }
            
            if(!metrics['{{ $data->metric }}']) {
                metrics['{{ $data->metric }}'] = [];
            }
            
            metrics['{{ $data->metric }}'].push({{ $data->value }});
        @endforeach
        
        const datasets = Object.keys(metrics).map((metric, index) => {
            const colors = ['#1A6FBF', '#198754', '#ffc107', '#dc3545'];
            const color = colors[index % colors.length];
            
            return {
                label: metric.charAt(0).toUpperCase() + metric.slice(1),
                data: metrics[metric],
                borderColor: color,
                backgroundColor: color + '33', // 20% opacity
                borderWidth: 2,
                tension: 0.4,
                fill: true
            };
        });
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
