<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TeleGateway') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link href="{{ asset('css/telegateway.css') }}" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
</head>
<body>
    
    @include('layouts.sidebar')
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-content">
        <!-- Topbar -->
        <header class="tg-topbar d-flex align-items-center justify-content-between px-4 sticky-top">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-link text-dark p-0 d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-2"></i>
                </button>
                <h5 class="mb-0 text-muted fw-bold">@yield('title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="text-secondary position-relative">
                    <i data-feather="bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>
                
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <x-avatar :url="auth()->user()->avatar_url" :size="32" class="me-2" />
                        <span class="d-none d-md-inline ms-2 fw-medium">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages Container -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
            @if(session('success'))
                <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        feather.replace();
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Auto hide toasts
            setTimeout(function() {
                var toasts = document.querySelectorAll('.toast');
                toasts.forEach(function(toastNode) {
                    var toast = new bootstrap.Toast(toastNode);
                    toast.hide();
                });
            }, 3000);
        });
        // Sidebar Toggle Logic
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.tg-sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
