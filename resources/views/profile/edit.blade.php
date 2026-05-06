@extends('layouts.app')
@section('title', __('messages.my_profile'))

@section('content')
<div class="row">
    <div class="col-md-4">
        {{-- Profile Summary Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-5">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover; border: 4px solid #F0F6FF">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2" title="Active"></span>
                </div>
                <h4 class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
                <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
                <div class="d-flex justify-content-center gap-2">
                    @foreach(auth()->user()->roles as $role)
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </div>
                <hr class="my-4">
                <div class="row text-start g-3">
                    <div class="col-6">
                        <div class="text-muted small">{{ __('messages.joined') }}</div>
                        <div class="fw-medium">{{ auth()->user()->created_at->format('M Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">{{ __('messages.devices') }}</div>
                        <div class="fw-medium">{{ auth()->user()->devices()->count() }} {{ __('messages.units') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Account Settings --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>{{ __('messages.account_information') }}</h6>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('profile.update') }}" class="mt-2" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="row g-3">
                        <div class="col-md-12 text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="{{ auth()->user()->avatar_url }}" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover; border: 3px solid #1A6FBF">
                                <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow-sm" style="cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                                </label>
                                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="small text-muted mt-2">{{ __('messages.avatar_notice') }}</div>
                            @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.full_name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.email_address') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.phone_number') }}</label>
                            <input type="tel" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.gender') }}</label>
                            <select name="gender" class="form-select">
                                <option value="" disabled {{ is_null($user->gender) ? 'selected' : '' }}>Select gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                            </select>
                            @error('gender') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">{{ __('messages.physical_address') }}</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') }}</button>
                            @if (session('status') === 'profile-updated')
                                <span class="text-success small ms-2"><i class="bi bi-check-circle me-1"></i>{{ __('messages.saved') }}</span>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security Settings --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>{{ __('messages.security_password') }}</h6>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">{{ __('messages.current_password') }}</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                            @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.new_password') }}</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('messages.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-dark px-4">{{ __('messages.update_password') }}</button>
                            @if (session('status') === 'password-updated')
                                <span class="text-success small ms-2"><i class="bi bi-check-circle me-1"></i>{{ __('messages.password_updated') }}</span>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
@endsection
