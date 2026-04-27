@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card tg-card border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-12 text-center mb-3">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover;">
                                <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow" style="cursor: pointer;">
                                    <i data-feather="camera" style="width: 16px; height: 16px;"></i>
                                </label>
                                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="small text-muted mt-2">Upload a new avatar to replace current</div>
                            @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                <label for="name">Full Name</label>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                <label for="email">Email address</label>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Leave blank to keep current">
                                <label for="password">New Password (optional)</label>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ (old('role') ?? $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <label for="role">Assign Role</label>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_active">User is active</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
