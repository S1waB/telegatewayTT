@extends('layouts.guest')

@section('content')
<div class="card tg-card p-4 border-0">
    <div class="text-center mb-4">
        <img src="{{ asset('assets/images/logo.png') }}" alt="TeleGateway" class="login-logo">
        <p class="text-muted">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-floating mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username">
            <label for="email">Email address</label>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password" style="padding-right: 45px;">
            <label for="password">Password</label>
            <button type="button" id="togglePassword" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-2 text-muted" style="z-index: 5;">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-muted" for="remember">
                    Remember me
                </label>
            </div>
            
            @if (Route::has('password.request'))
                <a class="text-decoration-none small text-primary" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <button class="w-100 btn btn-lg btn-primary fw-medium" type="submit">Sign in</button>
        
        <div class="mt-4 text-center">
            <p class="text-muted small">
                Demo Accounts:<br>
                Admin: admin@telegateway.io<br>
                Operator: operator@telegateway.io<br>
                Password: password
            </p>
        </div>
    </form>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
@endsection
