@extends('layouts.app')
@section('title', __('messages.roles_management'))

@section('content')
<div class="tg-table-container">
    <div class="p-4 border-bottom bg-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0 fw-bold text-body">{{ __('messages.access_control') }}</h5>
                <p class="text-muted small mb-0">{{ __('messages.permissions_notice') }}</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="bi bi-plus-lg"></i> {{ __('messages.create_role') }}
            </button>
        </div>
        
        <form action="{{ route('admin.roles.index') }}" method="GET" class="row g-3 bg-body-tertiary p-3 rounded-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-5">
                <select name="permission" class="form-select select2" data-placeholder="{{ __('messages.filter_by_permission') }}">
                    <option value="">{{ __('messages.all_permissions') }}</option>
                    @foreach($permissions as $permission)
                        <option value="{{ $permission->id }}" {{ request('permission') == $permission->id ? 'selected' : '' }}>
                            {{ $permission->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">{{ __('messages.filter') }}</button>
                    @if(request()->anyFilled(['search', 'permission']))
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary" title="{{ __('messages.clear') }}"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table tg-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">{{ __('messages.role_identity') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th class="text-center">{{ __('messages.users') }}</th>
                    <th>{{ __('messages.permissions_overview') }}</th>
                    <th class="text-end pe-4">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="ps-4">
                        <div>
                            <div class="fw-bold text-body">{{ ucfirst($role->name) }}</div>
                            <div class="text-muted small">{{ __('messages.system_role') }}</div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $role->description ?? __('messages.no_detailed_description') }}</td>
                    <td class="text-center">
                        <span class="badge bg-body-tertiary text-body border rounded-pill px-3">{{ $role->users_count }}</span>
                    </td>
                    <td>
                        @if($role->name === 'admin')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">{{ __('messages.full_system_access') }}</span>
                        @else
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 small px-2 py-1" style="font-size: 10px;">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <span class="badge bg-body text-muted border small px-2" style="font-size: 10px;">+{{ $role->permissions->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-role-btn border-0 shadow-none" 
                                    data-role="{{ json_encode($role) }}"
                                    data-permissions="{{ json_encode($role->permissions->pluck('id')) }}"
                                    data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                    title="{{ __('messages.edit_configuration') }}">
                                <i class="bi bi-pencil-square" style="font-size: 1.1rem;"></i>
                            </button>
                            
                            @if($role->name !== 'admin')
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_role') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 shadow-none">
                                    <i class="bi bi-trash3" style="font-size: 1.1rem;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">{{ __('messages.no_roles_found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($roles->hasPages())
    <div class="card-footer bg-body d-flex justify-content-between align-items-center py-3 border-top">
        <span class="text-muted small">
            Displaying {{ $roles->firstItem() }}–{{ $roles->lastItem() }} of {{ $roles->total() }} roles
        </span>
        {{ $roles->links() }}
    </div>
    @endif
</div>

{{-- ── Create Role Modal ── --}}
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-primary">{{ __('messages.define_new_role') }}</h5>
                        <p class="text-muted small mb-0">{{ __('messages.identity_access_notice') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.role_identity') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-tag text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 bg-body-tertiary" placeholder="e.g. Technician" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.functional_description') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                                <input type="text" name="description" class="form-control border-start-0 bg-body-tertiary" placeholder="Role responsibilities...">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-0">{{ __('messages.permission_matrix') }}</label>
                            <span class="badge bg-body-tertiary text-muted border fw-normal">{{ __('messages.select_permissions_notice') }}</span>
                        </div>
                        <div class="row g-3">
                            @foreach($permissions->groupBy(fn($p) => explode('-', $p->name)[1] ?? 'other') as $group => $groupPermissions)
                                <div class="col-md-3">
                                    <div class="card border-0 shadow h-100 bg-body">
                                        <div class="card-header bg-body-tertiary bg-opacity-50 border-0 py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold mb-0 text-body" style="font-size: 11px; letter-spacing: 0.5px;">{{ strtoupper($group) }}</h6>
                                            <div class="form-check form-switch m-0" style="min-height: auto;">
                                                <input class="form-check-input select-all-group" type="checkbox" role="switch" style="width: 1.8rem; height: 0.9rem;">
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="row g-1">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="col-6">
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_create_{{ $permission->id }}" style="width: 0.9rem; height: 0.9rem;">
                                                            <label class="form-check-label text-muted text-nowrap overflow-hidden" for="perm_create_{{ $permission->id }}" title="{{ $permission->name }}" style="font-size: 10px; cursor: pointer;">
                                                                {{ explode('-', $permission->name)[0] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">{{ __('messages.discard_changes') }}</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">{{ __('messages.save_configuration') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Role Modal ── --}}
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0 p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-body">{{ __('messages.modify_role_access') }}</h5>
                        <p class="text-muted small mb-0">{{ __('messages.update_permissions_notice') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.role_identity') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-tag text-muted"></i></span>
                                <input type="text" name="name" id="edit_role_name" class="form-control border-start-0 bg-body-tertiary" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('messages.functional_description') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                                <input type="text" name="description" id="edit_role_description" class="form-control border-start-0 bg-body-tertiary">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-0">{{ __('messages.permission_matrix') }}</label>
                            <span id="edit_permission_count" class="badge bg-primary px-3 rounded-pill">0 {{ __('messages.selected') }}</span>
                        </div>
                        <div class="row g-3">
                            @foreach($permissions->groupBy(fn($p) => explode('-', $p->name)[1] ?? 'other') as $group => $groupPermissions)
                                <div class="col-md-3">
                                    <div class="card border-0 shadow h-100 bg-body">
                                        <div class="card-header bg-body-tertiary bg-opacity-50 border-0 py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold mb-0 text-body" style="font-size: 11px; letter-spacing: 0.5px;">{{ strtoupper($group) }}</h6>
                                            <div class="form-check form-switch m-0" style="min-height: auto;">
                                                <input class="form-check-input select-all-group" type="checkbox" role="switch" style="width: 1.8rem; height: 0.9rem;">
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="row g-1">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="col-6">
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input edit-perm-check" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_edit_{{ $permission->id }}" data-id="{{ $permission->id }}" style="width: 0.9rem; height: 0.9rem;">
                                                            <label class="form-check-label text-muted text-nowrap overflow-hidden" for="perm_edit_{{ $permission->id }}" title="{{ $permission->name }}" style="font-size: 10px; cursor: pointer;">
                                                                {{ explode('-', $permission->name)[0] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">{{ __('messages.keep_original') }}</button>
                    <button type="submit" class="btn btn-dark px-5 rounded-pill shadow">{{ __('messages.commit_updates') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle Edit Modal Population
    document.querySelectorAll('.edit-role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const role = JSON.parse(this.dataset.role);
            const rolePerms = JSON.parse(this.dataset.permissions);
            
            document.getElementById('editRoleForm').action = `/admin/roles/${role.id}`;
            document.getElementById('edit_role_name').value = role.name;
            document.getElementById('edit_role_description').value = role.description || '';
            
            // Uncheck all first
            document.querySelectorAll('.edit-perm-check').forEach(cb => cb.checked = false);
            
            // Check relevant ones
            rolePerms.forEach(id => {
                const cb = document.querySelector(`.edit-perm-check[data-id="${id}"]`);
                if(cb) cb.checked = true;
            });
            
            updatePermCount();
        });
    });

    // Select All Group Toggles
    document.querySelectorAll('.select-all-group').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const groupBody = this.closest('.card').querySelector('.card-body');
            const checkboxes = groupBody.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updatePermCount();
        });
    });

    // Update permission count badge in edit modal
    function updatePermCount() {
        const count = document.querySelectorAll('.edit-perm-check:checked').length;
        const badge = document.getElementById('edit_permission_count');
        if(badge) badge.textContent = `${count} {{ __('messages.selected') }}`;
    }

    document.querySelectorAll('.edit-perm-check').forEach(cb => {
        cb.addEventListener('change', updatePermCount);
    });
</script>
@endpush
@endsection
