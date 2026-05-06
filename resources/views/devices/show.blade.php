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
                        <div class="bg-body-tertiary p-2 rounded me-3 text-primary"><i data-feather="{{ $device->type->icon ?? 'box' }}"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.type') }}</div>
                            <div class="fw-medium">{{ $device->type->name }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-body-tertiary p-2 rounded me-3 text-primary"><i data-feather="map-pin"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.location') }}</div>
                            <div class="fw-medium">{{ $device->location ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-body-tertiary p-2 rounded me-3 text-primary"><i data-feather="wifi"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.ip_address') }}</div>
                            <div class="fw-medium">{{ $device->ip_address ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-body-tertiary p-2 rounded me-3 text-primary"><i data-feather="user"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('messages.assigned_to') }}</div>
                            <div class="fw-medium">{{ $device->user ? $device->user->name : __('messages.unassigned') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @can('sendCommand', $device)
            <div class="card-footer bg-body p-3 border-top">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#sendCommandModal">
                    <i data-feather="terminal" class="me-2" style="width: 16px;"></i> {{ __('messages.send_command') }}
                </button>
            </div>
            @endcan
        </div>
    </div>
    
    <!-- Data Chart & AI Analysis -->
    <div class="col-md-8">
        @if(isset($telemetryAnalysis))
        <div class="card tg-card border-0 shadow-sm mb-4 bg-{{ $telemetryAnalysis['status'] }} bg-opacity-10 border border-{{ $telemetryAnalysis['status'] }} border-opacity-25">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-{{ $telemetryAnalysis['status'] }} p-2 rounded-circle me-3">
                        <i class="fas fa-brain text-white"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-{{ $telemetryAnalysis['status'] }}">{{ __('messages.ai_telemetry_interpretation') }}</h6>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-1">{{ $telemetryAnalysis['title'] }}</h6>
                        <p class="small text-muted mb-0">{{ $telemetryAnalysis['message'] }}</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="small fw-bold text-uppercase text-muted mb-1">{{ __('messages.ai_advice') }}</div>
                        <div class="badge bg-{{ $telemetryAnalysis['status'] }}">{{ $telemetryAnalysis['advice'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card tg-card h-100 border-0">
            <div class="card-header bg-body p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">{{ __('messages.recent_telemetry_data') }}</h6>
                @if($device->last_seen_at)
                    <span class="badge bg-body-tertiary text-body border">{{ __('messages.last_seen') }}: {{ $device->last_seen_at->diffForHumans() }}</span>
                @else
                    <span class="badge bg-body-tertiary text-body border">{{ __('messages.never_seen') }}</span>
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
            <div class="p-4 border-bottom bg-body rounded-top-4">
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
                    <tbody id="commandTableBody">
                        @forelse($commands as $command)
                        <tr id="command-row-{{ $command->id }}">
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
                                    <div class="bg-body-tertiary border px-2 py-1 rounded small d-flex align-items-center overflow-hidden" style="max-width: 180px;">
                                        <i class="bi bi-code-slash text-muted me-2"></i>
                                        <code class="text-body text-truncate" style="font-size: 0.75rem;">
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
                                                        <div class="bg-body-tertiary text-body p-3 rounded-3 font-monospace small mb-0" style="white-space: pre-wrap; word-break: break-all;">
                                                            {{ json_encode($command->payload, JSON_PRETTY_PRINT) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="status-cell">{!! $command->status_badge !!}</td>
                            <td class="small">
                                @if($command->sent_at)
                                    <div class="fw-medium">{{ $command->sent_at->diffForHumans() }}</div>
                                    <div class="text-muted opacity-75" style="font-size: 0.7rem;">{{ $command->sent_at->format('M d, H:i:s') }}</div>
                                @else
                                    <span class="text-muted">{{ __('messages.in_queue') }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 response-cell">
                                @if($command->response)
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-none border-opacity-25 d-flex align-items-center gap-1 ms-auto" data-bs-toggle="modal" data-bs-target="#responseModal{{ $command->id }}">
                                        <i class="bi bi-terminal"></i> {{ __('messages.view_result') }}
                                    </button>
                                    
                                    <!-- Response Modal (Static for initial load) -->
                                    <div class="modal fade" id="responseModal{{ $command->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
                                                <div class="modal-header bg-body border-bottom p-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-terminal-fill fs-4 text-primary me-3"></i>
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-0 text-body">{{ __('messages.execution_output') }}</h5>
                                                            <span class="text-muted small">Asset: {{ $device->name }} | ID: #{{ $command->id }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-body-tertiary bg-opacity-50">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge-status-container">
                                                            <span class="badge bg-{{ $command->status === 'success' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $command->status === 'success' ? 'success' : 'danger' }} border border-{{ $command->status === 'success' ? 'success' : 'danger' }} border-opacity-25 px-3">
                                                                <i class="bi bi-{{ $command->status === 'success' ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                                                                {{ ucfirst($command->status) }}
                                                            </span>
                                                        </span>
                                                        <span class="text-muted small response-time">
                                                            <i class="bi bi-clock me-1"></i> {{ $command->response_at ? $command->response_at->format('M d, Y H:i:s') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="bg-body rounded-3 p-3 border shadow-sm">
                                                        <pre class="text-body mb-0 font-monospace small text-start response-text" style="white-space: pre-wrap; word-break: break-all; min-height: 100px;">{{ $command->response }}</pre>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-body border-top p-3 d-flex justify-content-between align-items-center">
                                                    <div class="text-muted small">
                                                        <i class="bi bi-shield-check me-1"></i> {{ __('messages.verified_iot_response') }}
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 copy-btn" onclick="navigator.clipboard.writeText(`{{ addslashes($command->response) }}`).then(() => alert('Copied to clipboard!'))">
                                                            <i class="bi bi-clipboard me-1"></i> {{ __('messages.copy') }}
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-body-tertiary text-muted fw-normal border in-flight-badge">{{ __('messages.in_flight') }}</span>
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
            <div class="card-footer bg-body d-flex justify-content-between align-items-center py-3 border-top rounded-bottom-4">
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
        // ─── Real-time Chart Logic ───
        const ctx = document.getElementById('telemetryChart').getContext('2d');
        let telemetryChart;
        
        function processData(chartData) {
            const metrics = {};
            const labels = [];
            
            chartData.forEach(data => {
                const time = data.received_at_formatted || new Date(data.received_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                if(!labels.includes(time)) {
                    labels.push(time);
                }
                
                const processed = data.processed_data || {};
                Object.keys(processed).forEach(metric => {
                    const value = processed[metric];
                    if(typeof value === 'number') {
                        if(!metrics[metric]) {
                            metrics[metric] = Array(labels.length - 1).fill(null);
                        }
                        metrics[metric][labels.length - 1] = value;
                    }
                });
            });
            
            return { labels, metrics };
        }

        function getChartColors() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            return {
                text: isDark ? '#94A3B8' : '#64748B',
                grid: isDark ? '#1E293B' : '#E2E8F0'
            };
        }

        function initChart() {
            const initialData = @json($chartData);
            initialData.forEach(d => {
                const date = new Date(d.received_at);
                d.received_at_formatted = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
            });

            const { labels, metrics } = processData(initialData);
            
            const datasets = Object.keys(metrics).map((metric, index) => {
                const colors = ['#1A6FBF', '#198754', '#ffc107', '#dc3545', '#6610f2', '#6f42c1'];
                const color = colors[index % colors.length];
                return {
                    label: metric.charAt(0).toUpperCase() + metric.slice(1),
                    data: metrics[metric],
                    borderColor: color,
                    backgroundColor: color + '20',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3
                };
            });

            const colors = getChartColors();
            telemetryChart = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800 },
                    plugins: { 
                        legend: { 
                            position: 'top',
                            labels: { color: colors.text }
                        } 
                    },
                    scales: { 
                        y: { 
                            beginAtZero: false,
                            ticks: { color: colors.text },
                            grid: { color: colors.grid, borderColor: colors.grid }
                        },
                        x: {
                            ticks: { color: colors.text },
                            grid: { color: colors.grid, borderColor: colors.grid }
                        }
                    }
                }
            });

            // Listen for theme changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "data-bs-theme" && telemetryChart) {
                        const newColors = getChartColors();
                        telemetryChart.options.scales.x.ticks.color = newColors.text;
                        telemetryChart.options.scales.y.ticks.color = newColors.text;
                        telemetryChart.options.scales.x.grid.color = newColors.grid;
                        telemetryChart.options.scales.x.grid.borderColor = newColors.grid;
                        telemetryChart.options.scales.y.grid.color = newColors.grid;
                        telemetryChart.options.scales.y.grid.borderColor = newColors.grid;
                        telemetryChart.options.plugins.legend.labels.color = newColors.text;
                        telemetryChart.update();
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        }

        function updateChart() {
            fetch(`/api/devices/{{ $device->id }}/metrics`)
                .then(response => response.json())
                .then(data => {
                    const { labels, metrics } = processData(data);
                    telemetryChart.data.labels = labels;
                    Object.keys(metrics).forEach(metric => {
                        let dataset = telemetryChart.data.datasets.find(ds => ds.label.toLowerCase() === metric.toLowerCase());
                        if (dataset) {
                            dataset.data = metrics[metric];
                        } else {
                            const colors = ['#1A6FBF', '#198754', '#ffc107', '#dc3545', '#6610f2', '#6f42c1'];
                            const color = colors[telemetryChart.data.datasets.length % colors.length];
                            telemetryChart.data.datasets.push({
                                label: metric.charAt(0).toUpperCase() + metric.slice(1),
                                data: metrics[metric],
                                borderColor: color,
                                backgroundColor: color + '20',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3
                            });
                        }
                    });
                    telemetryChart.update('none');
                });
        }

        // ─── Real-time Command History Logic ───
        function updateCommands() {
            fetch(`/api/commands?device_id={{ $device->id }}`)
                .then(response => response.json())
                .then(response => {
                    const commands = response.data || [];
                    commands.forEach(cmd => {
                        const row = document.getElementById(`command-row-${cmd.id}`);
                        if (!row) return;

                        // Update Status Badge
                        const statusCell = row.querySelector('.status-cell');
                        if (statusCell && !statusCell.innerHTML.includes(cmd.status)) {
                            statusCell.innerHTML = cmd.status_badge;
                        }

                        // Update Response Button
                        const responseCell = row.querySelector('.response-cell');
                        if (responseCell && cmd.response && responseCell.querySelector('.in-flight-badge')) {
                            // Command just finished! Refresh the cell with a "View Result" button
                            const modalId = `responseModal${cmd.id}`;
                            responseCell.innerHTML = `
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-none border-opacity-25 d-flex align-items-center gap-1 ms-auto" data-bs-toggle="modal" data-bs-target="#${modalId}">
                                    <i class="bi bi-terminal"></i> {{ __('messages.view_result') }}
                                </button>
                                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
                                            <div class="modal-header bg-body border-bottom p-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-terminal-fill fs-4 text-primary me-3"></i>
                                                    <div>
                                                        <h5 class="modal-title fw-bold mb-0 text-body">{{ __('messages.execution_output') }}</h5>
                                                        <span class="text-muted small">Asset: {{ $device->name }} | ID: #${cmd.id}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 bg-body-tertiary bg-opacity-50">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="badge bg-${cmd.status === 'success' ? 'success' : 'danger'} bg-opacity-10 text-${cmd.status === 'success' ? 'success' : 'danger'} border border-${cmd.status === 'success' ? 'success' : 'danger'} border-opacity-25 px-3">
                                                        <i class="bi bi-${cmd.status === 'success' ? 'check-circle' : 'exclamation-circle'} me-1"></i>
                                                        ${cmd.status.charAt(0).toUpperCase() + cmd.status.slice(1)}
                                                    </span>
                                                    <span class="text-muted small">
                                                        <i class="bi bi-clock me-1"></i> ${new Date(cmd.response_at).toLocaleString()}
                                                    </span>
                                                </div>
                                                <div class="bg-body rounded-3 p-3 border shadow-sm">
                                                    <pre class="text-body mb-0 font-monospace small text-start" style="white-space: pre-wrap; word-break: break-all; min-height: 100px;">${cmd.response}</pre>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-body border-top p-3 d-flex justify-content-between align-items-center">
                                                <div class="text-muted small">
                                                    <i class="bi bi-shield-check me-1"></i> {{ __('messages.verified_iot_response') }}
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText(\`${cmd.response.replace(/`/g, '\\`')}\`).then(() => alert('Copied to clipboard!'))">
                                                        <i class="bi bi-clipboard me-1"></i> {{ __('messages.copy') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                });
        }

        initChart();
        setInterval(updateChart, 5000);
        setInterval(updateCommands, 3000);
    });
</script>
@endif
@endpush
