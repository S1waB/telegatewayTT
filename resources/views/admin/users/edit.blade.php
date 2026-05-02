@extends('layouts.app')
@section('title', __('messages.edit_user'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
        <i class="bi bi-arrow-left me-1"></i> {{ __('messages.back_to_users') }}
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        {{-- Main Edit Card --}}
        <div class="card tg-card border-0 shadow-sm mb-4">
            <div class="card-body p-5">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-12 text-center mb-3">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover; border: 3px solid #1A6FBF">
                                <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow" style="cursor: pointer;">
                                    <i data-feather="camera" style="width: 16px; height: 16px;"></i>
                                </label>
                                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="small text-muted mt-2">{{ __('messages.upload_avatar_notice') }}</div>
                            @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                <label for="name">{{ __('messages.full_name') }}</label>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                <label for="email">{{ __('messages.email_address') }}</label>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+216 XX XXX XXX">
                                <label for="phone_number">{{ __('messages.phone_number') }}</label>
                                @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="" disabled {{ is_null($user->gender) ? 'selected' : '' }}>Select gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <label for="gender">{{ __('messages.gender') }}</label>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Leave blank to keep current">
                                <label for="password">{{ __('messages.new_password') }} ({{ __('messages.optional') }})</label>
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
                                <label for="role">{{ __('messages.assign_role') }}</label>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" style="height: 100px" placeholder="Address">{{ old('address', $user->address) }}</textarea>
                                <label for="address">{{ __('messages.physical_address') }}</label>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('messages.user_is_active') }}</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">{{ __('messages.cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-4">{{ __('messages.update_user') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danger Zone Card --}}
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #E63946 !important;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 fw-bold text-danger">{{ __('messages.reset_user_password') }}</h6>
                        <p class="text-muted mb-0 small">{{ __('messages.reset_password_notice') }}</p>
                    </div>
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_reset_password', ['name' => $user->name]) }}')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                            <i class="bi bi-shield-lock me-1"></i> {{ __('messages.generate_new_password') }}
                        </button>
                    </form>
                </div>
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
