<aside class="tg-sidebar" id="tgSidebar">

    {{-- ── Logo ─────────────────────────────────────────────── --}}
    <div class="tg-sidebar-logo">
        <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--tg-brand),var(--tg-accent));display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:900;color:#fff;box-shadow:0 4px 14px rgba(46,95,163,0.35);flex-shrink:0;">T</div>
        <span class="brand-name">TeleGateway</span>
        <button class="d-lg-none ms-auto btn p-1" onclick="document.getElementById('tgSidebar').classList.remove('show');document.getElementById('sidebarOverlay').classList.remove('show');"
                style="background:var(--tg-surface-muted);border:1px solid var(--tg-border);border-radius:.6rem;color:var(--tg-text-muted);">
            <i class="bi bi-x" style="font-size:1.1rem;"></i>
        </button>
    </div>

    {{-- ── Navigation ───────────────────────────────────────── --}}
    <nav class="flex-1 py-2 px-2 overflow-auto" style="flex:1;overflow-y:auto;">

        @role('admin')
        <p class="sidebar-heading">{{ __('messages.administration') }}</p>

        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i> {{ __('messages.dashboard') }}
        </a>
        <a href="{{ route('admin.devices.index') }}" class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
            <i class="bi bi-cpu"></i> {{ __('messages.devices') }}
        </a>
        <a href="{{ route('admin.device-types.index') }}" class="nav-link {{ request()->routeIs('admin.device-types.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> {{ __('messages.device_types') }}
        </a>
        <a href="{{ route('admin.commands.history') }}" class="nav-link {{ request()->routeIs('admin.commands.*') ? 'active' : '' }}">
            <i class="bi bi-terminal"></i> {{ __('messages.commands_history') }}
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> {{ __('messages.users') }}
        </a>
        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> {{ __('messages.broadcast_center') }}
        </a>

        <div style="margin: .75rem .5rem;border-top:1px solid var(--tg-border);"></div>
        <p class="sidebar-heading">{{ __('messages.governance') }}</p>

        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> {{ __('messages.roles') }}
        </a>

        <div style="margin: .75rem .5rem;border-top:1px solid var(--tg-border);"></div>
        <p class="sidebar-heading">{{ __('messages.reporting') }}</p>

        <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle"></i> {{ __('messages.alert_center') }}
        </a>
        @endrole

        @role('operator')
        <p class="sidebar-heading">{{ __('messages.operations') }}</p>

        <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i> {{ __('messages.dashboard') }}
        </a>
        <a href="{{ route('operator.devices.index') }}" class="nav-link {{ request()->routeIs('operator.devices.*') ? 'active' : '' }}">
            <i class="bi bi-cpu"></i> {{ __('messages.my_devices') }}
        </a>
        <a href="{{ route('operator.commands.history') }}" class="nav-link {{ request()->routeIs('operator.commands.*') ? 'active' : '' }}">
            <i class="bi bi-terminal"></i> {{ __('messages.my_commands') }}
        </a>
        <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle"></i> {{ __('messages.my_alerts') }}
        </a>
        @endrole

        {{-- Profile (all roles) --}}
        <div style="margin: .75rem .5rem;border-top:1px solid var(--tg-border);"></div>
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> {{ __('messages.profile') }}
        </a>

    </nav>

    {{-- ── User + Logout ────────────────────────────────────── --}}
    <div class="tg-sidebar-user">
        <div class="user-card mb-2">
            <div class="avatar">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div style="min-width:0;flex:1;">
                <p class="mb-0 fw-semibold text-truncate" style="font-size:.82rem;color:var(--tg-text);">{{ auth()->user()->name }}</p>
                <p class="mb-0 text-truncate" style="font-size:.72rem;color:var(--tg-text-muted);">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn w-100 py-2" style="border-radius:.875rem;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);color:#ef4444;font-size:.82rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                <i class="bi bi-box-arrow-left"></i> {{ __('messages.sign_out') }}
            </button>
        </form>
    </div>

</aside>
