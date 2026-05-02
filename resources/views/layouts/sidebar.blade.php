<div class="tg-sidebar d-flex flex-column flex-shrink-0">
    <a href="/" class="tg-logo-container text-decoration-none d-block">
        <img src="{{ asset('assets/images/logo-white.png') }}" alt="TeleGateway" class="tg-logo-img">
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto">
        @role('admin')
            <li class="sidebar-heading">{{ __('messages.administration') }}</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i data-feather="home" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.dashboard') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.devices.index') }}" class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
                    <i data-feather="cpu" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.devices') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.device-types.index') }}" class="nav-link {{ request()->routeIs('admin.device-types.*') ? 'active' : '' }}">
                    <i data-feather="grid" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.device_types') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.commands.history') }}" class="nav-link {{ request()->routeIs('admin.commands.*') ? 'active' : '' }}">
                    <i data-feather="activity" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.commands_history') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i data-feather="users" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.users') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i data-feather="megaphone" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.broadcast_center') }}
                </a>
            </li>
            <li class="sidebar-heading">{{ __('messages.governance') }}</li>
            <li>
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i data-feather="shield" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.roles') }}
                </a>
            </li>
            <li class="sidebar-heading">{{ __('messages.reporting') }}</li>
            <li>
                <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                    <i data-feather="alert-circle" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.alert_center') }}
                </a>
            </li>
        @endrole

        @role('operator')
            <li class="sidebar-heading">{{ __('messages.operations') }}</li>
            <li class="nav-item">
                <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                    <i data-feather="home" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.dashboard') }}
                </a>
            </li>
            <li>
                <a href="{{ route('operator.devices.index') }}" class="nav-link {{ request()->routeIs('operator.devices.*') ? 'active' : '' }}">
                    <i data-feather="cpu" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.my_devices') }}
                </a>
            </li>
            <li>
                <a href="{{ route('operator.commands.history') }}" class="nav-link {{ request()->routeIs('operator.commands.*') ? 'active' : '' }}">
                    <i data-feather="activity" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.my_commands') }}
                </a>
            </li>
            <li>
                <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                    <i data-feather="alert-circle" class="me-2" style="width: 18px;"></i>
                    {{ __('messages.my_alerts') }}
                </a>
            </li>
        @endrole
    </ul>
    
    <div class="p-3 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 border-0" style="background: transparent; color: rgba(255,255,255,0.6);">
                <i data-feather="log-out" class="me-2" style="width: 18px;"></i> {{ __('messages.sign_out') }}
            </button>
        </form>
    </div>
</div>
