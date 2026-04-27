@extends('layouts.app')
@section('title', 'Create Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card tg-card border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Role Name" required>
                                <label for="name">Role Name (e.g. manager)</label>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" placeholder="Description">
                                <label for="description">Description (optional)</label>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h6 class="mb-3 fw-bold border-bottom pb-2">Permissions</h6>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 mt-5 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Create Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
