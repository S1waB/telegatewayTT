<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TeleGateway') }}</title>
    
    <script>
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('tg-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>

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
                <div class="theme-switch shadow-sm" id="themeToggler" title="Toggle Somber/Light Mode">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </div>

                <div class="dropdown">
                    <a href="#" class="text-secondary position-relative dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i data-feather="bell"></i>
                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.6rem;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            <span id="notificationCountText" class="badge bg-primary rounded-pill">0 New</span>
                        </li>
                        <div id="notificationList">
                            <li class="p-4 text-center text-muted small">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div><br>
                                Loading notifications...
                            </li>
                        </div>
                    </ul>
                </div>
                
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
        // Theme Toggle Logic
        const themeToggler = document.getElementById('themeToggler');
        const themeIcon = document.getElementById('themeIcon');
        const htmlRoot = document.documentElement;

        // Initialize icon on load
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            } else {
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            }
        }
        
        updateThemeIcon(htmlRoot.getAttribute('data-bs-theme'));

        if (themeToggler) {
            themeToggler.addEventListener('click', () => {
                const currentTheme = htmlRoot.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                htmlRoot.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('tg-theme', newTheme);
                updateThemeIcon(newTheme);
            });
        }
        // Notifications Polling
        function fetchNotifications() {
            $.ajax({
                url: '{{ route('notifications.unread') }}',
                method: 'GET',
                success: function(response) {
                    let count = response.count;
                    let notifications = response.notifications;
                    
                    let badge = $('#notificationBadge');
                    let countText = $('#notificationCountText');
                    let list = $('#notificationList');
                    
                    if (count > 0) {
                        badge.removeClass('d-none').text(count > 99 ? '99+' : count);
                        countText.text(count + ' New');
                    } else {
                        badge.addClass('d-none');
                        countText.text('0 New');
                    }
                    
                    list.empty();
                    
                    if (notifications.length > 0) {
                        notifications.forEach(function(notif) {
                            let data = notif.data;
                            let date = new Date(notif.created_at).toLocaleDateString() + ' ' + new Date(notif.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                            
                            let html = `
                                <li>
                                    <a class="dropdown-item p-3 border-bottom text-wrap" href="javascript:void(0)" onclick="markNotificationAsRead('${notif.id}', '${data.url}')">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="bg-primary-light text-primary rounded-circle p-2 mt-1">
                                                <i class="bi bi-info-circle"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 small text-dark fw-medium" style="line-height: 1.4;">${data.message}</p>
                                                <span class="text-muted" style="font-size: 10px;">${date}</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            `;
                            list.append(html);
                        });
                        
                        list.append(`
                            <li><a class="dropdown-item text-center small text-primary fw-bold py-2 bg-light" href="#">View All</a></li>
                        `);
                    } else {
                        list.append(`
                            <li class="p-4 text-center text-muted small">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                No new notifications
                            </li>
                        `);
                    }
                }
            });
        }

        window.markNotificationAsRead = function(id, url) {
            $.ajax({
                url: `/notifications/${id}/mark-read`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    window.location.href = url;
                }
            });
        };

        // Initial fetch
        fetchNotifications();
        
        // Poll every 15 seconds
        setInterval(fetchNotifications, 15000);
    </script>
    @stack('scripts')
</body>
</html>
