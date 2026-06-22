@extends('layouts.guest')

@section('content')
<div style="padding: 2rem 2rem 1.75rem;">

    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="font-size:1.65rem;letter-spacing:-0.02em;color:var(--tg-text);">
            {{ __('messages.sign_in') ?? 'Sign In' }}
        </h2>
        <p style="font-size:.875rem;color:var(--tg-text-muted);">{{ __('messages.access_your_account') ?? 'Access your TeleGateway workspace' }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="d-flex flex-column gap-3">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="form-label fw-semibold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--tg-text-muted);">
                Email
            </label>
            <div class="input-group">
                <span class="input-group-text" style="border-right:none;border-radius:.875rem 0 0 .875rem;">
                    <i class="bi bi-envelope" style="font-size:.9rem;"></i>
                </span>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="your@email.com"
                       class="form-control @error('email') is-invalid @enderror"
                       style="border-left:none;border-radius:0 .875rem .875rem 0;"
                       required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label fw-semibold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--tg-text-muted);">
                {{ __('messages.password') ?? 'Password' }}
            </label>
            <div class="input-group">
                <span class="input-group-text" style="border-right:none;border-radius:.875rem 0 0 .875rem;">
                    <i class="bi bi-lock" style="font-size:.9rem;"></i>
                </span>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="••••••••"
                       class="form-control @error('password') is-invalid @enderror"
                       style="border-left:none;border-right:none;"
                       required autocomplete="current-password">
                <button type="button" id="togglePassword"
                        class="input-group-text"
                        style="cursor:pointer;border-left:none;border-radius:0 .875rem .875rem 0;">
                    <i class="bi bi-eye" id="eyeIcon" style="font-size:.9rem;"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember + Forgot -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember" style="font-size:.85rem;color:var(--tg-text-muted);">
                    {{ __('messages.remember_me') ?? 'Remember me' }}
                </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:.85rem;color:var(--tg-brand);text-decoration:none;font-weight:600;">
                    {{ __('messages.forgot_password') ?? 'Forgot password?' }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary w-100 py-3 mt-1" style="font-size:.95rem;font-weight:700;border-radius:1rem;">
            <i class="bi bi-box-arrow-in-right me-1"></i>
            {{ __('messages.sign_in') ?? 'Sign In' }}
        </button>
    </form>

    <!-- Demo accounts -->
    <div class="mt-4 pt-3" style="border-top:1px solid var(--tg-border);">
        <p class="text-center mb-2" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--tg-text-muted);">Demo Accounts</p>
        <div class="row g-2">
            <div class="col-6">
                <div style="background:var(--tg-surface-muted);border:1px solid var(--tg-border);border-radius:.875rem;padding:.6rem .9rem;font-size:.75rem;cursor:pointer;transition:all .2s;"
                     onclick="document.getElementById('email').value='admin@telegateway.com';document.getElementById('password').value='password';"
                     onmouseover="this.style.borderColor='var(--tg-brand)'" onmouseout="this.style.borderColor='var(--tg-border)'">
                    <div class="fw-bold" style="color:var(--tg-text);">Admin</div>
                    <div style="color:var(--tg-text-muted);">admin@telegateway.com</div>
                </div>
            </div>
            <div class="col-6">
                <div style="background:var(--tg-surface-muted);border:1px solid var(--tg-border);border-radius:.875rem;padding:.6rem .9rem;font-size:.75rem;cursor:pointer;transition:all .2s;"
                     onclick="document.getElementById('email').value='operator@telegateway.com';document.getElementById('password').value='password';"
                     onmouseover="this.style.borderColor='var(--tg-brand)'" onmouseout="this.style.borderColor='var(--tg-border)'">
                    <div class="fw-bold" style="color:var(--tg-text);">Operator</div>
                    <div style="color:var(--tg-text-muted);">operator@telegateway.com</div>
                </div>
            </div>
        </div>
        <p class="text-center mt-2" style="font-size:.72rem;color:var(--tg-text-muted);">Password: <code style="color:var(--tg-brand);">password</code> — click a card to autofill</p>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const p = document.getElementById('password');
        const e = document.getElementById('eyeIcon');
        if (p.type === 'password') {
            p.type = 'text';
            e.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            p.type = 'password';
            e.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
@endsection
