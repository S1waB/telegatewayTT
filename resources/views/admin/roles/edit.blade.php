@extends('layouts.app')
@section('title', 'Edit Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card tg-card border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" {{ $role->name === 'admin' ? 'readonly' : 'required' }}>
                                <label for="name">Role Name</label>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @if($role->name === 'admin')
                                <small class="text-muted mt-1 d-block">The admin role name cannot be changed.</small>
                            @endif
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $role->description) }}" placeholder="Description">
                                <label for="description">Description (optional)</label>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($role->name !== 'admin')
                        <div class="col-md-12 mt-4">
                            <h6 class="mb-3 fw-bold border-bottom pb-2">Permissions</h6>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" 
                                            {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) || (!old('permissions') && $role->hasPermissionTo($permission->name)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="col-md-12 mt-4">
                            <div class="alert alert-info border-0 d-flex align-items-center">
                                <i data-feather="info" class="me-3"></i>
                                <div>
                                    <strong>Admin Role</strong><br>
                                    The admin role inherently has all permissions. Individual permissions do not need to be assigned.
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-12 mt-5 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
