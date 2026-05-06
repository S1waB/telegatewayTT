@extends('layouts.app')
@section('title', __('messages.broadcast_center'))

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-4">
        <h3 class="fw-bold mb-0 text-body">{{ __('messages.broadcast_center') }}</h3>
        <p class="text-muted small mb-0">{{ __('messages.manage_announcements_notice') }}</p>
    </div>
    <div class="col-md-8 text-md-end mt-3 mt-md-0">
        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-medium d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
            <i class="bi bi-plus-lg"></i> {{ __('messages.add_announcement') }}
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="card tg-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.announcements.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-body-tertiary border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-body-tertiary" placeholder="{{ __('messages.search_announcements') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-body-tertiary border-0">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('messages.all_statuses') }}</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('messages.sent') }}</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('messages.scheduled') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('messages.draft') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select bg-body-tertiary border-0">
                    <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>{{ __('messages.all_categories') }}</option>
                    <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>{{ __('messages.general') }}</option>
                    <option value="Update" {{ request('category') == 'Update' ? 'selected' : '' }}>{{ __('messages.update') }}</option>
                    <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>{{ __('messages.maintenance') }}</option>
                    <option value="Alert" {{ request('category') == 'Alert' ? 'selected' : '' }}>{{ __('messages.alert') }}</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-light fw-medium">{{ __('messages.filter') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Announcements Table -->
<div class="card tg-card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 border-0 text-uppercase small fw-bold text-muted tracking-wider py-3">{{ __('messages.title') }}</th>
                        <th class="border-0 text-uppercase small fw-bold text-muted tracking-wider py-3">{{ __('messages.date') }}</th>
                        <th class="border-0 text-uppercase small fw-bold text-muted tracking-wider py-3">{{ __('messages.status') }}</th>
                        <th class="border-0 text-uppercase small fw-bold text-muted tracking-wider py-3">{{ __('messages.audience') }}</th>
                        <th class="pe-4 border-0 text-uppercase small fw-bold text-muted tracking-wider py-3 text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-semibold text-body">{{ $announcement->subject }}</div>
                                @if($announcement->category)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill mt-1" style="font-size: 10px;">{{ $announcement->category }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="small fw-medium text-body">{{ $announcement->created_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $announcement->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="py-3">
                                @if($announcement->status === 'sent')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium"><i class="bi bi-check-circle me-1"></i> {{ __('messages.sent') }}</span>
                                @elseif($announcement->status === 'scheduled')
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-medium"><i class="bi bi-clock me-1"></i> {{ __('messages.scheduled') }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 fw-medium"><i class="bi bi-pencil-square me-1"></i> {{ __('messages.draft') }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-{{ $announcement->target_type === 'role' ? 'shield-lock' : 'person' }} small"></i>
                                    </div>
                                    <div>
                                        <div class="small fw-medium">{{ $announcement->target_type === 'role' ? __('messages.roles') : __('messages.specific_users') }}</div>
                                        <div class="small text-muted" style="font-size: 11px;">{{ __('messages.selected_count', ['count' => is_array($announcement->target_ids) ? count($announcement->target_ids) : 0]) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <button class="btn btn-sm btn-light rounded-circle p-2 text-muted hover-primary" onclick='viewAnnouncement(@json($announcement))' title="{{ __('messages.view_details') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <div class="bg-body-tertiary rounded-circle d-inline-flex p-4 mb-3">
                                        <i class="bi bi-inbox fs-2 text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold text-body">{{ __('messages.no_announcements_found') }}</h6>
                                    <p class="small mb-0">{{ __('messages.adjust_filters_notice') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($announcements->hasPages())
        <div class="card-footer bg-transparent p-3 border-top">
            {{ $announcements->links() }}
        </div>
    @endif
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg border-radius-xl">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">{{ __('messages.new_announcement') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.announcements.send') }}" method="POST" enctype="multipart/form-data" id="announcementForm">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">{{ __('messages.title') }}</label>
                            <input type="text" name="subject" class="form-control bg-body-tertiary border-0 py-2" placeholder="{{ __('messages.announcement_subject_placeholder') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">{{ __('messages.category') }}</label>
                            <select name="category" class="form-select bg-body-tertiary border-0 py-2">
                                <option value="General">{{ __('messages.general') }}</option>
                                <option value="Update">{{ __('messages.update') }}</option>
                                <option value="Maintenance">{{ __('messages.maintenance') }}</option>
                                <option value="Alert">{{ __('messages.alert') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">{{ __('messages.message_content') }}</label>
                        <textarea name="message" class="form-control bg-body-tertiary border-0 py-2" rows="6" placeholder="{{ __('messages.write_message_placeholder') }}" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block">{{ __('messages.audience') }}</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="modal_target_role" value="role" checked onchange="toggleModalTarget('role')">
                                <label class="form-check-label" for="modal_target_role">{{ __('messages.roles') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="modal_target_user" value="user" onchange="toggleModalTarget('user')">
                                <label class="form-check-label" for="modal_target_user">{{ __('messages.specific_users') }}</label>
                            </div>
                        </div>

                        <div id="modal_role_selection">
                            <select name="role_ids[]" class="form-select select2-modal w-100" multiple="multiple" data-placeholder="{{ __('messages.choose_roles') }}">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="modal_user_selection" class="d-none">
                            <select name="user_ids[]" class="form-select select2-modal w-100" multiple="multiple" data-placeholder="{{ __('messages.search_users') }}">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.action') }}</label>
                            <select name="status" id="statusSelect" class="form-select bg-body-tertiary border-0 py-2" onchange="toggleSchedule()">
                                <option value="sent">{{ __('messages.send_now') }}</option>
                                <option value="scheduled">{{ __('messages.schedule_later') }}</option>
                                <option value="draft">{{ __('messages.save_as_draft') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-none" id="scheduleContainer">
                            <label class="form-label small fw-bold">{{ __('messages.schedule_datetime') }}</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control bg-body-tertiary border-0 py-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">{{ __('messages.attachments_optional') }}</label>
                        <input type="file" name="attachments[]" class="form-control bg-body-tertiary border-0 py-2" multiple>
                        <div class="form-text small">Max size: 5MB per file.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-medium px-4">{{ __('messages.save_submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Announcement Modal -->
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg border-radius-xl">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-3">
                    <div id="viewCategoryBadge" class="badge bg-primary bg-opacity-10 text-primary rounded-pill"></div>
                    <div id="viewStatusBadge" class="badge rounded-pill"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h3 id="viewSubject" class="fw-bold text-body mb-4"></h3>
                
                <div class="d-flex align-items-center gap-4 mb-4 p-3 bg-body-tertiary rounded-3">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold tracking-wider mb-1" style="font-size: 10px;">{{ __('messages.date') }}</div>
                        <div id="viewDate" class="small fw-medium"></div>
                    </div>
                    <div class="border-start ps-4">
                        <div class="small text-muted text-uppercase fw-bold tracking-wider mb-1" style="font-size: 10px;">{{ __('messages.audience') }}</div>
                        <div id="viewAudience" class="small fw-medium"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="small text-muted text-uppercase fw-bold tracking-wider mb-2" style="font-size: 10px;">{{ __('messages.message') }}</div>
                    <div id="viewMessage" class="text-body" style="white-space: pre-wrap; font-size: 15px; line-height: 1.6;"></div>
                </div>

                <div id="viewAttachmentsContainer" class="d-none border-top pt-4">
                    <div class="small text-muted text-uppercase fw-bold tracking-wider mb-3" style="font-size: 10px;">{{ __('messages.attachments') }}</div>
                    <div id="viewAttachmentsList" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .hover-primary:hover {
        background-color: var(--tg-primary-light) !important;
        color: var(--tg-primary) !important;
    }
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f8f9fa !important;
        border: none !important;
        min-height: 40px;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addAnnouncementModal')
        });
    });

    function toggleModalTarget(type) {
        if (type === 'role') {
            $('#modal_role_selection').removeClass('d-none');
            $('#modal_user_selection').addClass('d-none');
        } else {
            $('#modal_role_selection').addClass('d-none');
            $('#modal_user_selection').removeClass('d-none');
        }
    }

    function toggleSchedule() {
        const val = document.getElementById('statusSelect').value;
        const container = document.getElementById('scheduleContainer');
        if (val === 'scheduled') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    function viewAnnouncement(data) {
        // Set metadata
        document.getElementById('viewSubject').textContent = data.subject;
        document.getElementById('viewMessage').textContent = data.message;
        
        const dateObj = new Date(data.created_at);
        document.getElementById('viewDate').innerHTML = `<i class="bi bi-calendar-event me-1"></i> ${dateObj.toLocaleDateString()} <br> <span class="text-muted" style="font-size:11px;">${dateObj.toLocaleTimeString()}</span>`;
        
        const targetCount = Array.isArray(data.target_ids) ? data.target_ids.length : 0;
        document.getElementById('viewAudience').innerHTML = `<i class="bi bi-${data.target_type === 'role' ? 'shield-lock' : 'person'} me-1"></i> ${data.target_type.charAt(0).toUpperCase() + data.target_type.slice(1)}s (${targetCount})`;

        // Category Badge
        const catBadge = document.getElementById('viewCategoryBadge');
        if(data.category) {
            catBadge.textContent = data.category;
            catBadge.classList.remove('d-none');
        } else {
            catBadge.classList.add('d-none');
        }

        // Status Badge
        const statusBadge = document.getElementById('viewStatusBadge');
        statusBadge.className = 'badge rounded-pill px-3 py-2 fw-medium ';
        if (data.status === 'sent') {
            statusBadge.classList.add('bg-success', 'bg-opacity-10', 'text-success');
            statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> {{ __('messages.sent') }}';
        } else if (data.status === 'scheduled') {
            statusBadge.classList.add('bg-warning', 'bg-opacity-10', 'text-warning');
            statusBadge.innerHTML = '<i class="bi bi-clock me-1"></i> {{ __('messages.scheduled') }}';
        } else {
            statusBadge.classList.add('bg-secondary', 'bg-opacity-10', 'text-secondary');
            statusBadge.innerHTML = '<i class="bi bi-pencil-square me-1"></i> {{ __('messages.draft') }}';
        }

        // Attachments
        const attContainer = document.getElementById('viewAttachmentsContainer');
        const attList = document.getElementById('viewAttachmentsList');
        attList.innerHTML = '';
        
        if (data.attachments && Array.isArray(data.attachments) && data.attachments.length > 0) {
            attContainer.classList.remove('d-none');
            data.attachments.forEach(path => {
                const fileName = path.split('/').pop();
                attList.innerHTML += `
                    <a href="/storage/${path}" target="_blank" class="btn btn-sm btn-light border text-start d-flex align-items-center gap-2" style="max-width: 200px;">
                        <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                        <span class="text-truncate" style="font-size: 11px;">${fileName}</span>
                    </a>
                `;
            });
        } else {
            attContainer.classList.add('d-none');
        }

        // Show Modal
        const modal = new bootstrap.Modal(document.getElementById('viewAnnouncementModal'));
        modal.show();
    }
</script>
@endpush
@endsection
