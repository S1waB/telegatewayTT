@extends('layouts.app')
@section('title', __('messages.device_types'))

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold">{{ __('messages.device_types_library') }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.device-types.analytics') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-collection-play"></i> {{ __('messages.type_analytics') }}
                </a>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createTypeModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_new_type') }}
                </button>
            </div>
        </div>

        {{-- Filters/Search --}}
        <form method="GET" action="{{ route('admin.device-types.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">{{ __('messages.search') }}</button>
            </div>
            @if(request('search'))
                <div class="col-md-1">
                    <a href="{{ route('admin.device-types.index') }}" class="btn btn-light w-100" title="{{ __('messages.clear_filters') }}"><i class="bi bi-x-lg"></i></a>
                </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th style="width: 60px;">Icon</th>
                        <th>{{ __('messages.type_name') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th class="text-center">{{ __('messages.total_devices') }}</th>
                        <th class="text-center">{{ __('messages.active_pct') }}</th>
                        <th class="text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deviceTypes as $type)
                    @php
                        $activePercent = $type->devices_count > 0 ? round(($type->active_devices_count / $type->devices_count) * 100) : 0;
                        $displayIcon = $type->image_url ?: null;
                    @endphp
                    <tr>
                        <td>
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm overflow-hidden" style="width: 40px; height: 40px;">
                                @if($type->image_url)
                                    <img src="{{ $type->image_url }}" alt="{{ $type->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="bi bi-{{ $type->icon ?? 'box' }}" style="font-size: 1.2rem;"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-body">{{ $type->name }}</div>
                            <div class="text-muted small">ID: #{{ $type->id }}</div>
                        </td>
                        <td>
                            <div class="text-truncate text-muted" style="max-width: 250px;" title="{{ $type->description }}">
                                {{ $type->description ?? __('messages.no_description') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-body-tertiary text-body border">{{ $type->devices_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 60px; min-width: 60px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activePercent }}%"></div>
                                </div>
                                <span class="small fw-bold {{ $activePercent > 0 ? 'text-success' : 'text-muted' }}">{{ $activePercent }}%</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- View --}}
                                <button type="button" class="btn btn-sm btn-outline-info p-0 d-flex align-items-center justify-content-center" 
                                        style="width: 32px; height: 32px; border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#viewTypeModal"
                                        data-type="{{ json_encode(array_merge($type->toArray(), ['image_url' => $type->image_url])) }}"
                                        data-active-pct="{{ $activePercent }}"
                                        title="{{ __('messages.view_details') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                {{-- Edit --}}
                                <button type="button" class="btn btn-sm btn-outline-primary p-0 d-flex align-items-center justify-content-center" 
                                        style="width: 32px; height: 32px; border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#editTypeModal"
                                        data-type="{{ json_encode(array_merge($type->toArray(), ['image_url' => $type->image_url])) }}"
                                        title="{{ __('messages.edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Delete --}}
                                <form action="{{ route('admin.device-types.destroy', $type) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center" 
                                            style="width: 32px; height: 32px; border-radius: 6px;" 
                                            {{ $type->devices_count > 0 ? 'disabled' : '' }}
                                            title="{{ $type->devices_count > 0 ? __('messages.cannot_delete_in_use') : __('messages.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                            {{ __('messages.no_results_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($deviceTypes->hasPages())
    <div class="card-footer bg-body d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Showing {{ $deviceTypes->firstItem() }}–{{ $deviceTypes->lastItem() }} of {{ $deviceTypes->total() }} types
        </span>
        {{ $deviceTypes->links() }}
    </div>
    @endif
</div>

{{-- ── Create Modal ── --}}
<div class="modal fade" id="createTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.device-types.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>{{ __('messages.new_device_type') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('messages.type_name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Smart Sensor" required>
                    </div>

                    <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active small py-2" data-bs-toggle="tab" data-bs-target="#preset-tab" type="button">{{ __('messages.preset_icons') }}</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link small py-2" data-bs-toggle="tab" data-bs-target="#custom-tab" type="button">{{ __('messages.custom_upload') }}</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Presets --}}
                        <div class="tab-pane fade show active" id="preset-tab">
                            <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-body-tertiary" style="max-height: 150px; overflow-y: auto;">
                                @foreach($icons as $icon)
                                    <input type="radio" class="btn-check" name="icon" id="icon_{{ $icon }}" value="{{ $icon }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary p-2 d-flex align-items-center justify-content-center" for="icon_{{ $icon }}" style="width: 40px; height: 40px;">
                                        <i class="bi bi-{{ $icon }}"></i>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        {{-- Custom --}}
                        <div class="tab-pane fade" id="custom-tab">
                            <div class="text-center p-3 border rounded bg-body-tertiary">
                                <label for="custom_icon" class="d-block mb-2 cursor-pointer">
                                    <div class="bg-body rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; border: 2px dashed #ddd;">
                                        <i class="bi bi-cloud-arrow-up text-muted fs-4" id="create_preview_icon"></i>
                                        <img id="create_preview_img" class="d-none rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span class="small text-primary fw-bold">{{ __('messages.upload_image_notice') }}</span>
                                </label>
                                <input type="file" name="custom_icon" id="custom_icon" class="d-none" accept="image/*" onchange="previewCreate(this)">
                                <div class="text-muted small mt-1">{{ __('messages.recommended_format') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-bold">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief explanation of this type..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.create_type') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Modal ── --}}
<div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>{{ __('messages.edit_device_type') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('messages.type_name') }}</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active small py-2" data-bs-toggle="tab" data-bs-target="#edit-preset-tab" type="button">{{ __('messages.preset_icons') }}</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link small py-2" data-bs-toggle="tab" data-bs-target="#edit-custom-tab" type="button">{{ __('messages.custom_upload') }}</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Presets --}}
                        <div class="tab-pane fade show active" id="edit-preset-tab">
                            <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-body-tertiary" style="max-height: 150px; overflow-y: auto;">
                                @foreach($icons as $icon)
                                    <input type="radio" class="btn-check" name="icon" id="edit_icon_{{ $icon }}" value="{{ $icon }}">
                                    <label class="btn btn-outline-primary p-2 d-flex align-items-center justify-content-center" for="edit_icon_{{ $icon }}" style="width: 40px; height: 40px;">
                                        <i class="bi bi-{{ $icon }}"></i>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        {{-- Custom --}}
                        <div class="tab-pane fade" id="edit-custom-tab">
                            <div class="text-center p-3 border rounded bg-body-tertiary">
                                <label for="edit_custom_icon" class="d-block mb-2 cursor-pointer">
                                    <div class="bg-body rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; border: 2px dashed #ddd;">
                                        <i class="bi bi-cloud-arrow-up text-muted fs-4" id="edit_preview_icon"></i>
                                        <img id="edit_preview_img" class="d-none rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span class="small text-primary fw-bold">{{ __('messages.upload_image_notice') }}</span>
                                </label>
                                <input type="file" name="custom_icon" id="edit_custom_icon" class="d-none" accept="image/*" onchange="previewEdit(this)">
                                <div id="current_image_status" class="small text-muted mt-1">{{ __('messages.leave_empty_to_keep') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-bold">{{ __('messages.description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-dark px-4">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── View Modal ── --}}
<div class="modal fade" id="viewTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>{{ __('messages.type_details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-info-subtle p-4 text-center border-bottom">
                    <div id="view_icon_container" class="bg-body rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-3 overflow-hidden" style="width: 80px; height: 80px;">
                        <i id="view_icon" class="bi" style="font-size: 2.5rem; color: var(--bs-info);"></i>
                        <img id="view_img" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 id="view_name" class="fw-bold text-body mb-1"></h4>
                    <span class="badge bg-info text-white px-3 py-2 rounded-pill">{{ __('messages.system_library_item') }}</span>
                </div>
                <div class="p-4">
                    <div class="row g-4 mb-4 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-body-tertiary">
                                <div class="text-muted small mb-1">{{ __('messages.total_devices') }}</div>
                                <h3 id="view_total" class="fw-bold mb-0 text-primary"></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-body-tertiary">
                                <div class="text-muted small mb-1">{{ __('messages.active_pct') }}</div>
                                <h3 id="view_active" class="fw-bold mb-0 text-success"></h3>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">{{ __('messages.description') }}</label>
                        <p id="view_description" class="bg-body-tertiary p-3 rounded border text-muted"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">{{ __('messages.close_view') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewCreate(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('create_preview_img').src = e.target.result;
                document.getElementById('create_preview_img').classList.remove('d-none');
                document.getElementById('create_preview_icon').classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEdit(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_preview_img').src = e.target.result;
                document.getElementById('edit_preview_img').classList.remove('d-none');
                document.getElementById('edit_preview_icon').classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Handle Edit Modal
    document.getElementById('editTypeModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const type = JSON.parse(button.getAttribute('data-type'));
        
        document.getElementById('editForm').action = `/admin/device-types/${type.id}`;
        document.getElementById('edit_name').value = type.name;
        document.getElementById('edit_description').value = type.description || '';
        
        // Reset preview
        document.getElementById('edit_preview_img').classList.add('d-none');
        document.getElementById('edit_preview_icon').classList.remove('d-none');

        if (type.image_url) {
            document.getElementById('edit_preview_img').src = type.image_url;
            document.getElementById('edit_preview_img').classList.remove('d-none');
            document.getElementById('edit_preview_icon').classList.add('d-none');
            
            // Switch to custom tab automatically if there's an image
            const customTab = new bootstrap.Tab(document.querySelector('button[data-bs-target="#edit-custom-tab"]'));
            customTab.show();
        } else {
            // Select the correct icon and show preset tab
            const iconInput = document.getElementById(`edit_icon_${type.icon || 'box'}`);
            if(iconInput) iconInput.checked = true;
            
            const presetTab = new bootstrap.Tab(document.querySelector('button[data-bs-target="#edit-preset-tab"]'));
            presetTab.show();
        }
    });

    // Handle View Modal
    document.getElementById('viewTypeModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const type = JSON.parse(button.getAttribute('data-type'));
        const activePct = button.getAttribute('data-active-pct');
        
        document.getElementById('view_name').textContent = type.name;
        document.getElementById('view_description').textContent = type.description || 'No description provided for this device type.';
        document.getElementById('view_total').textContent = type.devices_count;
        document.getElementById('view_active').textContent = activePct + '%';
        
        if (type.image_url) {
            document.getElementById('view_img').src = type.image_url;
            document.getElementById('view_img').classList.remove('d-none');
            document.getElementById('view_icon').classList.add('d-none');
        } else {
            document.getElementById('view_img').classList.add('d-none');
            document.getElementById('view_icon').classList.remove('d-none');
            document.getElementById('view_icon').className = `bi bi-${type.icon || 'box'}`;
        }
    });
</script>
@endpush
@endsection
