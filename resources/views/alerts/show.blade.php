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

        {{-- Conversation / Messaging Section --}}
        <div class="card tg-card border-0 mb-4">
            <div class="card-header bg-transparent p-4 border-bottom">
                <h6 class="fw-bold mb-0">Conversation History</h6>
            </div>
            <div class="card-body p-4">
                {{-- Initial Admin Response (Legacy / Main) --}}
                @if($alert->admin_response)
                    <div class="d-flex gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="p-3 rounded-3 shadow-sm" style="background: var(--tg-primary-light); border-left: 4px solid var(--tg-primary);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small text-primary">System Resolution</span>
                                    <span class="text-muted" style="font-size: 10px;">{{ $alert->updated_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="mb-0 small text-dark" style="white-space: pre-line;">{{ $alert->admin_response }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Threaded Messages --}}
                @foreach($alert->messages as $message)
                    <div class="d-flex gap-3 mb-4 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                        <div class="flex-shrink-0">
                            <x-avatar :url="$message->user->avatar_url" :size="40" class="shadow-sm border" />
                        </div>
                        <div class="flex-grow-1" style="max-width: 80%;">
                            <div class="p-3 rounded-3 shadow-sm {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light border' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small {{ $message->user_id === auth()->id() ? 'text-white' : 'text-primary' }}">
                                        {{ $message->user->name }}
                                        @if($message->user->hasRole('admin'))
                                            <i class="bi bi-patch-check-fill ms-1" title="Official Admin"></i>
                                        @endif
                                    </span>
                                    <span class="{{ $message->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 10px;">{{ $message->created_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="mb-0 small" style="white-space: pre-line;">{{ $message->message }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($alert->messages->count() === 0 && !$alert->admin_response)
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-chat-dots mb-2 d-block fs-4 opacity-50"></i>
                        No messages yet. Start the conversation below.
                    </div>
                @endif

                <hr class="my-4 opacity-10">

                {{-- Reply Form --}}
                <form action="{{ route('alerts.messages.store', $alert) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control bg-light border-0" rows="3" placeholder="Type your message here..." required style="resize: none;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
