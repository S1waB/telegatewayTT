@extends('layouts.app')
@section('title', 'Alerts & Reports')

@section('content')
<div class="row g-4 mb-4">
    {{-- Total Alerts --}}
    <div class="col-md-3">
        <div class="card tg-card border-0 tg-stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-primary-light text-primary rounded-circle p-3">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                    <span class="badge bg-soft-primary text-primary px-2 py-1">Overall</span>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalAlerts }}</h3>
                <p class="text-muted small mb-0">Total Incident Reports</p>
            </div>
        </div>
    </div>

    {{-- Viewed Percentage --}}
    <div class="col-md-3">
        <div class="card tg-card border-0 tg-stat-card" style="--tg-primary: #3A8FE8;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-info-light text-info rounded-circle p-3">
                        <i class="bi bi-eye-fill fs-4"></i>
                    </div>
                    <div class="text-info fw-bold small">{{ $viewedPercentage }}%</div>
                </div>
                <h3 class="fw-bold mb-1">{{ $viewedAlerts }}</h3>
                <p class="text-muted small mb-0">Engagement Rate</p>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: {{ $viewedPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Traited Alerts (Responded) --}}
    <div class="col-md-3">
        <div class="card tg-card border-0 tg-stat-card" style="--tg-primary: #10B981;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-success-light text-success rounded-circle p-3">
                        <i class="bi bi-check-all fs-4"></i>
                    </div>
                    <span class="badge bg-success text-white px-2 py-1">Resolved</span>
                </div>
                <h3 class="fw-bold mb-1">{{ $respondedAlerts }}</h3>
                <p class="text-muted small mb-0">Processed Alerts</p>
            </div>
        </div>
    </div>

    {{-- Response Rate --}}
    <div class="col-md-3">
        <div class="card tg-card border-0 tg-stat-card" style="--tg-primary: #F59E0B;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-warning-light text-warning rounded-circle p-3">
                        <i class="bi bi-chat-left-dots-fill fs-4"></i>
                    </div>
                    <div class="text-warning fw-bold small">{{ $responseRate }}%</div>
                </div>
                <h3 class="fw-bold mb-1">Response</h3>
                <p class="text-muted small mb-0">Administrative Rate</p>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: {{ $responseRate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card tg-card border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-primary">Alert Center</h5>
                        <p class="text-muted small mb-0">Report issues, device malfunctions, or general platform feedback.</p>
                    </div>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createAlertModal">
                        <i class="bi bi-exclamation-triangle-fill"></i> Raise New Alert
                    </button>
                </div>

                {{-- Filter Bar --}}
                <form action="{{ request()->url() }}" method="GET" class="row g-3 mt-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by subject, description or user..." value="{{ request('search') }}" style="height: 45px;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select" style="height: 45px;">
                            <option value="">All Statuses</option>
                            <option value="not_viewed" {{ request('status') === 'not_viewed' ? 'selected' : '' }}>Not Viewed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="viewed" {{ request('status') === 'viewed' ? 'selected' : '' }}>Viewed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select" style="height: 45px;">
                            <option value="">All Types</option>
                            <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General Alerts</option>
                            <option value="device" {{ request('type') === 'device' ? 'selected' : '' }}>Device Alerts</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm" style="height: 45px;">
                                <i class="bi bi-funnel me-2"></i>Apply Filters
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'type']))
                                <a href="{{ request()->url() }}" class="btn btn-light border shadow-sm" style="height: 45px;" title="Reset Filters">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="tg-table-container">
    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Subject & User</th>
                    <th>Related Device</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td class="ps-4">
                        <div>
                            <div class="fw-bold text-dark">{{ $alert->subject }}</div>
                            <div class="small text-muted">by {{ $alert->user->name }}</div>
                        </div>
                    </td>
                    <td>
                        @if($alert->device)
                            <div class="badge bg-primary-light text-primary border px-2 py-1">
                                <i class="bi bi-cpu me-1"></i> {{ $alert->device->name }}
                            </div>
                        @else
                            <span class="text-muted small">General Alert</span>
                        @endif
                    </td>
                    <td>
                        {!! $alert->status_badge !!}
                    </td>
                    <td class="small text-muted">
                        {{ $alert->created_at->format('M d, H:i') }}
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('alerts.show', $alert) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="bi bi-eye-fill me-1"></i> Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash display-4 opacity-25 mb-3 d-block"></i>
                        No alerts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($alerts->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Displaying {{ $alerts->firstItem() }}–{{ $alerts->lastItem() }} of {{ $alerts->total() }} alerts
        </span>
        {{ $alerts->links() }}
    </div>
    @endif
</div>

{{-- ── Create Alert Modal ── --}}
<div class="modal fade" id="createAlertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('alerts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-primary">Report an Issue</h5>
                        <p class="text-muted small mb-0">Provide details and attach images to help us resolve the problem.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Briefly describe the issue..." required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Related Device (Optional)</label>
                            <select name="device_id" class="form-select">
                                <option value="">General Platform Alert</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->serial_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Detailed Description</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Please provide as much detail as possible..." required></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Attach Screenshots/Photos</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                            <div class="form-text small">You can select multiple images (JPG, PNG, max 5MB each).</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">Submit Alert</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
