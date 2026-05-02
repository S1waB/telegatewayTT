@extends('layouts.app')
@section('title', __('messages.device_details'))

@section('content')
<div class="mb-4">
    <a href="{{ auth()->user()->hasRole('admin') ? route('admin.devices.index') : route('operator.devices.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
        <i class="bi bi-arrow-left me-1"></i> {{ __('messages.back_to_fleet') }}
    </a>
</div>
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
                            <div class="small text-muted">{{ __('messages.type') }}</div>
                            <div class="fw-medium">{{ $device->type->name }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="map-pin"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.location') }}</div>
                            <div class="fw-medium">{{ $device->location ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="wifi"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.ip_address') }}</div>
                            <div class="fw-medium">{{ $device->ip_address ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-primary"><i data-feather="user"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.assigned_to') }}</div>
                            <div class="fw-medium">{{ $device->user ? $device->user->name : __('messages.unassigned') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @can('sendCommand', $device)
            <div class="card-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#sendCommandModal">
                    <i data-feather="terminal" class="me-2" style="width: 16px;"></i> {{ __('messages.send_command') }}
                </button>
            </div>
            @endcan
        </div>
    </div>
    
    <!-- Data Chart -->
    <div class="col-md-8">
        <div class="card tg-card h-100 border-0">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">{{ __('messages.recent_telemetry_data') }}</h6>
                @if($device->last_seen_at)
                    <span class="badge bg-light text-dark border">{{ __('messages.last_seen') }}: {{ $device->last_seen_at->diffForHumans() }}</span>
                @else
                    <span class="badge bg-light text-dark border">{{ __('messages.never_seen') }}</span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($chartData->count() > 0)
                    <canvas id="telemetryChart" style="min-height: 300px; max-height: 400px;"></canvas>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                        <i data-feather="bar-chart-2" style="width: 48px; height: 48px; opacity: 0.2;" class="mb-3"></i>
                        <p>{{ __('messages.no_telemetry_data') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Command History -->
<div class="row">
    <div class="col-md-12">
        <div class="tg-table-container shadow-sm border-0">
            <div class="p-4 border-bottom bg-white rounded-top-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">{{ __('messages.command_history') }}</h6>
                    <div class="text-muted small">
                        {{ __('messages.total_operations', ['count' => $commands->total()]) }}
                    </div>
                </div>
                
                <form action="{{ request()->url() }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('messages.sent') }}</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>{{ __('messages.success') }}</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('messages.failed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">{{ __('messages.since') }}</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-dark btn-sm px-3 w-100">
                                <i class="bi bi-funnel me-1"></i> {{ __('messages.filter') }}
                            </button>
                            @if(request()->anyFilled(['status', 'from']))
                                <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm px-2"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table tg-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ __('messages.id') }}</th>
                            <th>{{ __('messages.sent_by') }}</th>
                            <th>{{ __('messages.instruction_payload') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.execution_time') }}</th>
                            <th class="text-end pe-4">{{ __('messages.response') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commands as $command)
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted small">#{{ $command->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$command->user->avatar_url" :size="32" class="me-2" />
                                    <span class="small fw-medium">{{ $command->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light border px-2 py-1 rounded small d-flex align-items-center overflow-hidden" style="max-width: 180px;">
                                        <i class="bi bi-code-slash text-muted me-2"></i>
                                        <code class="text-dark text-truncate" style="font-size: 0.75rem;">
                                            {{ $payloadJson = json_encode($command->payload) }}
                                        </code>
                                    </div>
                                    @if(strlen($payloadJson) > 25)
                                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 ms-2 small" data-bs-toggle="modal" data-bs-target="#payloadModal{{ $command->id }}">
                                            {{ __('messages.view') }}
                                        </button>
                                        
                                        <!-- Payload Modal -->
                                        <div class="modal fade" id="payloadModal{{ $command->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                    <div class="modal-header border-bottom-0 p-4 pb-0">
                                                        <h5 class="modal-title fw-bold">{{ __('messages.full_instruction_payload') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="bg-light text-dark p-3 rounded-3 font-monospace small mb-0" style="white-space: pre-wrap; word-break: break-all;">
                                                            {{ json_encode($command->payload, JSON_PRETTY_PRINT) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{!! $command->status_badge !!}</td>
                            <td class="small">
                                @if($command->sent_at)
                                    <div class="fw-medium">{{ $command->sent_at->diffForHumans() }}</div>
                                    <div class="text-muted opacity-75" style="font-size: 0.7rem;">{{ $command->sent_at->format('M d, H:i:s') }}</div>
                                @else
                                    <span class="text-muted">{{ __('messages.in_queue') }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($command->response)
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-none border-opacity-25 d-flex align-items-center gap-1 ms-auto" data-bs-toggle="modal" data-bs-target="#responseModal{{ $command->id }}">
                                        <i class="bi bi-terminal"></i> {{ __('messages.view_result') }}
                                    </button>
                                    
                                    <!-- Response Modal -->
                                    <div class="modal fade" id="responseModal{{ $command->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
                                                <div class="modal-header bg-white border-bottom p-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-terminal-fill fs-4 text-primary me-3"></i>
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-0 text-dark">{{ __('messages.execution_output') }}</h5>
                                                            <span class="text-muted small">Asset: {{ $device->name }} | ID: #{{ $command->id }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-light bg-opacity-50">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge bg-{{ $command->status === 'success' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $command->status === 'success' ? 'success' : 'danger' }} border border-{{ $command->status === 'success' ? 'success' : 'danger' }} border-opacity-25 px-3">
                                                            <i class="bi bi-{{ $command->status === 'success' ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                                                            {{ ucfirst($command->status) }}
                                                        </span>
                                                        <span class="text-muted small">
                                                            <i class="bi bi-clock me-1"></i> {{ $command->response_at ? $command->response_at->format('M d, Y H:i:s') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="bg-white rounded-3 p-3 border shadow-sm">
                                                        <pre class="text-dark mb-0 font-monospace small text-start" style="white-space: pre-wrap; word-break: break-all; min-height: 100px;">{{ $command->response }}</pre>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                                                    <div class="text-muted small">
                                                        <i class="bi bi-shield-check me-1"></i> {{ __('messages.verified_iot_response') }}
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText(`{{ addslashes($command->response) }}`).then(() => alert('Copied to clipboard!'))">
                                                            <i class="bi bi-clipboard me-1"></i> {{ __('messages.copy') }}
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted fw-normal border">{{ __('messages.in_flight') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x display-6 d-block mb-3 opacity-25"></i>
                                {{ __('messages.no_commands_to_device') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($commands->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top rounded-bottom-4">
                <div class="text-muted small">
                    {{ __('messages.showing_count', ['first' => $commands->firstItem(), 'last' => $commands->lastItem(), 'total' => $commands->total()]) }}
                </div>
                {{ $commands->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@can('sendCommand', $device)
<!-- Send Command Modal -->
<div class="modal fade" id="sendCommandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('messages.send_command') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-4">{{ __('messages.payload_notice', ['name' => $device->name]) }}</p>
                
                @if(auth()->user()->hasRole('admin'))
                    <form action="{{ route('admin.commands.store', ['device' => $device]) }}" method="POST" id="commandForm">
                @else
                    <form action="{{ route('operator.commands.store', ['device' => $device]) }}" method="POST" id="commandForm">
                @endif
                    @csrf
                    <input type="hidden" name="device_id" value="{{ $device->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">{{ __('messages.json_payload') }}</label>
                        <textarea name="payload" class="form-control font-monospace @error('payload') is-invalid @enderror" rows="5" required>{{ old('payload', "{\n  \"action\": \"ping\"\n}") }}</textarea>
                        @error('payload')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" form="commandForm" class="btn btn-primary d-flex align-items-center">
                    <i data-feather="send" class="me-2" style="width: 16px;"></i> {{ __('messages.send_now') }}
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
