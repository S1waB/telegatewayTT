@extends('layouts.app')
@section('title', __('messages.commands_history'))

@section('content')
{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-body opacity-75">{{ __('messages.total_operations_label') }}</h6>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($totalCommands) }}</h2>
                <div class="text-muted small mt-1">{{ __('messages.system_wide_requests') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-body opacity-75">{{ __('messages.success_rate') }}</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 text-success">{{ $successPercentage }}%</h2>
                <div class="progress mt-2 bg-body-tertiary" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $successPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-body opacity-75">{{ __('messages.completed') }}</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                        <i class="bi bi-lightning-charge fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($succeededCommands) }}</h2>
                <div class="text-muted small mt-1 text-nowrap">{{ __('messages.successfully_executed') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-body opacity-75">{{ __('messages.failed') }}</h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2">
                        <i class="bi bi-exclamation-triangle fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 text-danger">{{ number_format($failedCommands) }}</h2>
                <div class="text-muted small mt-1">{{ __('messages.execution_errors') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="tg-table-container shadow-sm border-0">
    <div class="p-4 border-bottom bg-body rounded-top-4">
        <form action="{{ request()->url() }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.device_selection') }}</label>
                <select name="device_id" class="form-select select2">
                    <option value="">{{ __('messages.all_devices') }}</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>{{ $device->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @role('admin')
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.operator') }}</label>
                <select name="user_id" class="form-select select2">
                    <option value="">{{ __('messages.all_users') }}</option>
                    @php $allUsers = \App\Models\User::all(); @endphp
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            @endrole

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.since') }}</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-{{ auth()->user()->hasRole('admin') ? '3' : '3' }}">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4 w-100">
                        <i class="bi bi-funnel me-2"></i>{{ __('messages.filter') }}
                    </button>
                    @if(request()->anyFilled(['device_id', 'user_id', 'status', 'from']))
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary px-3"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>{{ __('messages.asset_operator') }}</th>
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
                            <div class="bg-body-tertiary rounded-3 p-2 me-3">
                                <i class="bi bi-cpu text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-body">{{ $command->device->name }}</div>
                                <div class="text-muted small">{{ __('messages.by') }}: {{ $command->user->name }}</div>
                            </div>
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
                    <td>{!! $command->status_badge !!}</td>
                    <td class="small">
                        @if($command->sent_at)
                            <div class="fw-medium">{{ $command->sent_at->diffForHumans() }}</div>
                            <div class="text-muted opacity-75">{{ $command->sent_at->format('M d, H:i:s') }}</div>
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
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header bg-body border-bottom p-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-terminal-fill fs-4 text-primary me-3"></i>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0 text-body">{{ __('messages.execution_output') }}</h5>
                                                    <span class="text-muted small">{{ __('messages.asset') }}: {{ $command->device->name }} | ID: #{{ $command->id }}</span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-body-tertiary bg-opacity-50">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-{{ $command->status === 'success' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $command->status === 'success' ? 'success' : 'danger' }} border border-{{ $command->status === 'success' ? 'success' : 'danger' }} border-opacity-25 px-3">
                                                    <i class="bi bi-{{ $command->status === 'success' ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                                                    {{ ucfirst($command->status) }}
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="bi bi-clock me-1"></i> {{ $command->response_at ? $command->response_at->format('M d, Y H:i:s') : 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="bg-body rounded-3 p-3 border shadow-sm">
                                                <pre class="text-body mb-0 font-monospace small text-start" style="white-space: pre-wrap; word-break: break-all; min-height: 100px;">{{ $command->response }}</pre>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-body border-top p-3 d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">
                                                <i class="bi bi-shield-check me-1"></i> {{ __('messages.verified_iot_response') }}
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="navigator.clipboard.writeText(`{{ addslashes($command->response) }}`).then(() => alert('Copied to clipboard!'))">
                                                    <i class="bi bi-clipboard me-1"></i> Copy
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="badge bg-body-tertiary text-muted fw-normal border">{{ __('messages.in_flight') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-journal-x display-4 text-muted opacity-25 d-block mb-3"></i>
                        <p class="text-muted mb-0">{{ __('messages.no_instruction_history') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($commands->hasPages())
    <div class="card-footer bg-body d-flex justify-content-between align-items-center py-3 border-top rounded-bottom-4">
        <div class="text-muted small">
            {{ __('messages.showing_range', ['first' => $commands->firstItem(), 'last' => $commands->lastItem(), 'total' => $commands->total()]) }}
        </div>
        {{ $commands->links() }}
    </div>
    @endif
</div>
@endsection
