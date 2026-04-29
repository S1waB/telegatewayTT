@extends('layouts.app')
@section('title', 'Alert Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('alerts.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
        <i class="bi bi-arrow-left me-1"></i> Back to Alert Center
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        {{-- Alert Details Card --}}
        <div class="card tg-card border-0 mb-4">
            <div class="card-header bg-transparent p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">{{ $alert->subject }}</h5>
                    <span class="text-muted small">Submitted {{ $alert->created_at->diffForHumans() }} by {{ $alert->user->name }}</span>
                </div>
                {!! $alert->status_badge !!}
            </div>
            <div class="card-body p-4">
                <p class="mb-4" style="white-space: pre-line;">{{ $alert->description }}</p>

                @if($alert->attachments->count() > 0)
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Attachments ({{ $alert->attachments->count() }})</h6>
                    <div class="row g-3">
                        @foreach($alert->attachments as $attachment)
                            <div class="col-md-4 col-6">
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="d-block card h-100 border-0 shadow-sm hover-lift">
                                    <img src="{{ Storage::url($attachment->file_path) }}" class="card-img-top rounded shadow-sm" style="height: 120px; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Admin Response Section --}}
        @if($alert->admin_response)
            <div class="card border-0 shadow-sm" style="background: var(--tg-primary-light); border-left: 4px solid var(--tg-primary) !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle p-2 me-3">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">Admin Response</h6>
                    </div>
                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $alert->admin_response }}</p>
                </div>
            </div>
        @elseif(!auth()->user()->hasRole('admin'))
            <div class="text-center py-4 text-muted bg-light rounded-3 border">
                <i class="bi bi-clock-history mb-2 d-block"></i>
                Waiting for administrative review...
            </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Metadata Card --}}
        <div class="card tg-card border-0 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 small text-uppercase text-muted">Context & Asset</h6>
                
                <div class="mb-4">
                    <div class="small text-muted mb-1">Related Device</div>
                    @if($alert->device)
                        <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                            <div class="bg-white p-2 rounded-2 shadow-sm me-3">
                                <i class="bi bi-cpu text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">{{ $alert->device->name }}</div>
                                <div class="text-muted" style="font-size: 11px;">SN: {{ $alert->device->serial_number }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted italic small">General Platform Issue</div>
                    @endif
                </div>

                <div class="mb-4">
                    <div class="small text-muted mb-1">Current Status</div>
                    <div class="fw-bold">{!! $alert->status_badge !!}</div>
                </div>

                @role('admin')
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Manage Alert</h6>
                    <form action="{{ route('alerts.respond', $alert) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Update Status</label>
                            <select name="status" class="form-select">
                                <option value="not_viewed" {{ $alert->status === 'not_viewed' ? 'selected' : '' }}>Not Viewed</option>
                                <option value="pending" {{ $alert->status === 'pending' ? 'selected' : '' }}>Pending Investigation</option>
                                <option value="viewed" {{ $alert->status === 'viewed' ? 'selected' : '' }}>Viewed / Resolved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Response to User</label>
                            <textarea name="admin_response" class="form-control" rows="6" placeholder="Provide an update or resolution..." required>{{ $alert->admin_response }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow">Submit Response</button>
                    </form>
                @endrole
            </div>
        </div>
    </div>
</div>
@endsection
