<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TeleGateway — Smart IoT Device Management</title>
    <meta name="description" content="TeleGateway is a powerful IoT gateway management platform for monitoring, controlling, and analyzing your connected devices.">

    <!-- Detect saved theme immediately to avoid flash -->
    <script>
        const _t = localStorage.getItem('tg-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', _t);
        if (_t === 'dark') document.documentElement.classList.add('theme-dark');
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link href="{{ asset('css/telegateway.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <style>
        /* ── Guest-only extras ─────────────────────────────── */
        .landing-section { padding: 5rem 1.5rem; position: relative; }

        .service-card {
            background: var(--tg-surface);
            border: 1px solid var(--tg-border);
            border-radius: 1.75rem;
            padding: 2rem;
            box-shadow: 0 8px 32px var(--tg-shadow);
            backdrop-filter: blur(12px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px var(--tg-shadow);
        }
        .service-icon {
            width: 52px; height: 52px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .review-card {
            background: var(--tg-surface);
            border: 1px solid var(--tg-border);
            border-radius: 1.75rem;
            padding: 1.75rem;
            box-shadow: 0 8px 32px var(--tg-shadow);
            backdrop-filter: blur(12px);
            transition: box-shadow 0.3s ease;
        }
        .review-card:hover { box-shadow: 0 16px 48px var(--tg-shadow); }

        .hero-mockup {
            background: var(--tg-surface);
            border: 1px solid var(--tg-border);
            border-radius: 1.75rem;
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 40px var(--tg-shadow);
            transition: transform 0.3s ease;
        }
        .hero-mockup:hover { transform: scale(1.015); }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            background: rgba(46,95,163,0.12);
            border: 1px solid rgba(46,95,163,0.25);
            color: #2E5FA3;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        [data-bs-theme="dark"] .hero-badge { color: #60a5fa; }

        .gradient-text {
            background: linear-gradient(135deg, var(--tg-text) 0%, var(--tg-brand) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating orbs */
        .orb-gold   { background: rgba(252,211,77,0.55); box-shadow: 0 0 80px rgba(252,211,77,0.30); }
        .orb-green  { background: rgba(52,211,153,0.65); box-shadow: 0 0 70px rgba(52,211,153,0.25); }
        .orb-pink   { background: rgba(244,114,182,0.45); box-shadow: 0 0 90px rgba(244,114,182,0.22); }
        .orb-blue   { background: rgba(96,165,250,0.55); box-shadow: 0 0 60px rgba(96,165,250,0.30); }
    </style>
</head>
<body>
<div class="tg-auth-page">

    <!-- ── Animated Blobs ──────────────────────────────────────────── -->
    <div class="tg-blob-wrapper">
        <div class="tg-blob tg-blob-1"></div>
        <div class="tg-blob tg-blob-2"></div>
        <div class="tg-blob tg-blob-3"></div>
    </div>

    <!-- ── Floating Orbs ──────────────────────────────────────────── -->
    <div style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0;">
        <div class="tg-orb orb-gold"  style="width:52px;height:52px;top:24%;left:7%; animation-duration:7s;"></div>
        <div class="tg-orb orb-green" style="width:44px;height:44px;top:38%;right:11%;animation-delay:.6s;animation-duration:8s;"></div>
        <div class="tg-orb orb-pink"  style="width:60px;height:60px;bottom:42%;left:4%;animation-delay:1.3s;animation-duration:9s;"></div>
        <div class="tg-orb orb-blue"  style="width:36px;height:36px;bottom:22%;right:7%;animation-delay:.9s;animation-duration:6s;animation-name:tgSpin;"></div>
    </div>

    <!-- ── Sticky Navigation ──────────────────────────────────────── -->
    <header class="tg-auth-nav">
        <div class="d-flex align-items-center gap-2 cursor-pointer" onclick="window.scrollTo({top:0,behavior:'smooth'})" style="cursor:pointer;">
            <div style="width:40px;height:40px;border-radius:12px;background:var(--tg-surface);border:1px solid var(--tg-border);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="TeleGateway" style="height:26px;width:auto;">
            </div>
            <span style="font-size:1.15rem;font-weight:800;letter-spacing:-0.02em;background:linear-gradient(135deg,var(--tg-brand),var(--tg-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TeleGateway</span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <nav class="d-none d-md-flex align-items-center gap-4" style="font-size:.875rem;font-weight:500;">
                <button class="btn btn-link p-0 text-decoration-none" style="color:var(--tg-text-muted);" onclick="window.scrollTo({top:0,behavior:'smooth'})">{{ __('messages.home') }}</button>
                <button class="btn btn-link p-0 text-decoration-none" style="color:var(--tg-text-muted);" onclick="document.getElementById('services').scrollIntoView({behavior:'smooth'})">{{ __('messages.services') }}</button>
                <button class="btn btn-link p-0 text-decoration-none" style="color:var(--tg-text-muted);" onclick="document.getElementById('contact').scrollIntoView({behavior:'smooth'})">{{ __('messages.contact') }}</button>
            </nav>

            <div style="width:1px;height:20px;background:var(--tg-border);" class="d-none d-md-block"></div>

            <div class="d-flex align-items-center gap-2">
                <!-- Language switcher -->
                <x-lang-switcher />

                <!-- Theme toggle -->
                <button id="themeToggleBtn" class="theme-switch" title="Toggle theme">
                    <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- ── HERO SECTION ────────────────────────────────────────────── -->
    <section id="home" style="position:relative;z-index:1;">
        <div class="container-xl px-4 py-5 py-md-6" style="min-height:calc(100vh - 67px);">
            <div class="row g-5 align-items-center h-100" style="min-height:calc(100vh - 150px);">

                <!-- Left: Branding + Pitch -->
                <div class="col-lg-6">
                    <div class="d-flex flex-column gap-4">
                        <div>
                            <div class="hero-badge mb-3">
                                <i class="bi bi-stars"></i>
                                {{ __('messages.smart_gateway_management') ?? 'Smart IoT Gateway Management' }}
                            </div>
                            <h1 class="fw-black lh-1 mb-3" style="font-size:clamp(2.5rem,5vw,4rem);letter-spacing:-0.03em;">
                                <span class="gradient-text">TeleGateway</span>
                            </h1>
                            <p class="mb-0" style="font-size:1.1rem;color:var(--tg-text-muted);max-width:520px;line-height:1.7;">
                                {{ __('messages.hero_description') ?? 'Monitor, control, and analyze your connected IoT devices with a powerful, elegant platform built for modern operations.' }}
                            </p>
                        </div>

                        <!-- Device mockup card -->
                        <div class="hero-mockup" style="max-width:420px;">
                            <div class="d-flex align-items-center gap-3 pb-3 mb-3" style="border-bottom:1px solid var(--tg-border);">
                                <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--tg-brand),var(--tg-accent));display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:900;color:#fff;box-shadow:0 6px 20px rgba(46,95,163,0.35);">T</div>
                                <div>
                                    <div class="fw-bold" style="color:var(--tg-text);">TeleGateway Control</div>
                                    <div style="font-size:.8rem;color:var(--tg-text-muted);">{{ __('messages.realtime_device_monitoring') ?? 'Real-time device monitoring' }}</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div style="background:var(--tg-surface-muted);border:1px solid var(--tg-border);border-radius:1rem;padding:1rem;">
                                        <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--tg-text-muted);">{{ __('messages.devices') ?? 'Devices' }}</div>
                                        <div class="fw-bold mt-1" style="font-size:1.4rem;color:var(--tg-text);">128</div>
                                        <div style="font-size:.75rem;color:#22c55e;"><i class="bi bi-arrow-up-short"></i> Online</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background:var(--tg-surface-muted);border:1px solid var(--tg-border);border-radius:1rem;padding:1rem;">
                                        <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--tg-text-muted);">{{ __('messages.alerts') ?? 'Alerts' }}</div>
                                        <div class="fw-bold mt-1" style="font-size:1.4rem;color:var(--tg-text);">3</div>
                                        <div style="font-size:.75rem;color:#f59e0b;"><i class="bi bi-exclamation-triangle"></i> Active</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Login Card -->
                <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                    <div class="tg-login-card w-100" style="max-width:420px;">
                        @yield('content')
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ── SERVICES SECTION ────────────────────────────────────────── -->
    <section id="services" class="landing-section" style="position:relative;z-index:1;border-top:1px solid var(--tg-border);">
        <div class="container-xl px-4">
            <div class="text-center mb-5">
                <h2 class="fw-black mb-2" style="font-size:clamp(1.8rem,4vw,2.8rem);letter-spacing:-0.02em;">{{ __('messages.our_services') ?? 'Our Services' }}</h2>
                <p style="color:var(--tg-text-muted);font-size:1rem;">{{ __('messages.services_subtitle') ?? 'Everything you need to manage your IoT infrastructure.' }}</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon" style="background:rgba(46,95,163,0.12);color:#2E5FA3;"><i class="bi bi-router"></i></div>
                        <h4 class="fw-bold mb-2" style="font-size:1.05rem;">{{ __('messages.device_management') ?? 'Device Management' }}</h4>
                        <p style="color:var(--tg-text-muted);font-size:.875rem;line-height:1.6;">{{ __('messages.device_management_desc') ?? 'Register, configure and track all your IoT devices from a single dashboard.' }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon" style="background:rgba(240,165,0,0.12);color:#F0A500;"><i class="bi bi-activity"></i></div>
                        <h4 class="fw-bold mb-2" style="font-size:1.05rem;">{{ __('messages.real_time_monitoring') ?? 'Real-time Monitoring' }}</h4>
                        <p style="color:var(--tg-text-muted);font-size:.875rem;line-height:1.6;">{{ __('messages.monitoring_desc') ?? 'Live telemetry data, status updates and performance analytics at your fingertips.' }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon" style="background:rgba(34,197,94,0.12);color:#22c55e;"><i class="bi bi-bell"></i></div>
                        <h4 class="fw-bold mb-2" style="font-size:1.05rem;">{{ __('messages.smart_alerts') ?? 'Smart Alerts' }}</h4>
                        <p style="color:var(--tg-text-muted);font-size:.875rem;line-height:1.6;">{{ __('messages.alerts_desc') ?? 'Receive instant notifications and actionable alerts when anomalies are detected.' }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6;"><i class="bi bi-shield-check"></i></div>
                        <h4 class="fw-bold mb-2" style="font-size:1.05rem;">{{ __('messages.access_control') ?? 'Access Control' }}</h4>
                        <p style="color:var(--tg-text-muted);font-size:.875rem;line-height:1.6;">{{ __('messages.access_desc') ?? 'Role-based permissions and audit logs keep your operations secure and compliant.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── REVIEWS SECTION ─────────────────────────────────────────── -->
    <section style="position:relative;z-index:1;border-top:1px solid var(--tg-border);padding:5rem 1.5rem;background:var(--tg-surface-muted);">
        <div class="container-xl px-4">
            <div class="text-center mb-5">
                <h2 class="fw-black mb-2" style="font-size:clamp(1.8rem,4vw,2.8rem);letter-spacing:-0.02em;">{{ __('messages.what_users_say') ?? 'What our users say' }}</h2>
                <p style="color:var(--tg-text-muted);font-size:1rem;">{{ __('messages.reviews_subtitle') ?? 'Trusted by teams worldwide for reliable IoT management.' }}</p>
            </div>
            <div class="row g-4">
                @foreach([
                    ['initials'=>'AH','name'=>'Ahmed Hassan','role'=>'Network Engineer','color'=>'#2E5FA3','bg'=>'rgba(46,95,163,0.12)','text'=>'TeleGateway transformed how we manage our 200+ IoT devices. The real-time monitoring and alerts saved us hours every week.'],
                    ['initials'=>'LM','name'=>'Laura Martinez','role'=>'Operations Manager','color'=>'#22c55e','bg'=>'rgba(34,197,94,0.12)','text'=>'The access control system is exactly what we needed. Clean interface, intuitive roles, and everything just works beautifully.'],
                    ['initials'=>'KT','name'=>'Kenji Tanaka','role'=>'IoT Developer','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,0.12)','text'=>'The command history and device analytics give us insights we never had before. Highly recommend for any IoT operation team.'],
                ] as $review)
                <div class="col-md-4">
                    <div class="review-card h-100 d-flex flex-column justify-content-between gap-4">
                        <div>
                            <div class="d-flex gap-1 mb-3" style="color:#F0A500;">
                                @for($i=0;$i<5;$i++) <i class="bi bi-star-fill" style="font-size:.85rem;"></i> @endfor
                            </div>
                            <p style="color:var(--tg-text-muted);font-size:.875rem;line-height:1.7;font-style:italic;">"{{ $review['text'] }}"</p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3" style="border-top:1px solid var(--tg-border);">
                            <div style="width:40px;height:40px;border-radius:50%;background:{{ $review['bg'] }};color:{{ $review['color'] }};border:1px solid {{ $review['color'] }}40;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;">{{ $review['initials'] }}</div>
                            <div>
                                <div class="fw-bold" style="font-size:.875rem;color:var(--tg-text);">{{ $review['name'] }}</div>
                                <div style="font-size:.75rem;color:var(--tg-text-muted);">{{ $review['role'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── CONTACT SECTION ─────────────────────────────────────────── -->
    <section id="contact" class="landing-section" style="position:relative;z-index:1;border-top:1px solid var(--tg-border);">
        <div class="container-xl px-4">
            <div class="text-center mb-5">
                <h2 class="fw-black mb-2" style="font-size:clamp(1.8rem,4vw,2.8rem);letter-spacing:-0.02em;">{{ __('messages.contact_us') ?? 'Contact Us' }}</h2>
                <p style="color:var(--tg-text-muted);">{{ __('messages.contact_subtitle') ?? 'Have a question? Our team is ready to help.' }}</p>
            </div>
            <div class="row g-5 align-items-start">
                <div class="col-lg-5">
                    <div class="service-card">
                        <div class="d-flex flex-column gap-4">
                            @foreach([
                                ['icon'=>'bi-envelope','label'=>'Email','value'=>'contact@telegateway.io'],
                                ['icon'=>'bi-telephone','label'=>'Phone','value'=>'+216 71 000 000'],
                                ['icon'=>'bi-geo-alt','label'=>'Address','value'=>'Tunis, Tunisia'],
                            ] as $c)
                            <div class="d-flex align-items-start gap-3">
                                <div style="width:44px;height:44px;border-radius:.875rem;background:rgba(46,95,163,0.12);color:var(--tg-brand);border:1px solid rgba(46,95,163,0.2);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                                    <i class="bi {{ $c['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:.875rem;color:var(--tg-text);">{{ $c['label'] }}</div>
                                    <div style="font-size:.875rem;color:var(--tg-text-muted);margin-top:.2rem;">{{ $c['value'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="service-card">
                        <form id="contactForm" class="d-flex flex-column gap-3">
                            <div>
                                <label class="form-label fw-semibold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--tg-text-muted);">{{ __('messages.your_name') ?? 'Your Name' }}</label>
                                <input type="text" class="form-control" placeholder="John Doe" id="contactName">
                            </div>
                            <div>
                                <label class="form-label fw-semibold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--tg-text-muted);">Email</label>
                                <input type="email" class="form-control" placeholder="john@example.com" id="contactEmail">
                            </div>
                            <div>
                                <label class="form-label fw-semibold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--tg-text-muted);">{{ __('messages.message') ?? 'Message' }}</label>
                                <textarea class="form-control" rows="4" placeholder="{{ __('messages.how_can_we_help') ?? 'How can we help?' }}" id="contactMsg" style="resize:none;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-send"></i> {{ __('messages.send_message') ?? 'Send Message' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ──────────────────────────────────────────────────── -->
    <footer style="z-index:1;position:relative;padding:2rem 1.5rem;border-top:1px solid var(--tg-border);background:var(--tg-surface-muted);text-align:center;">
        <p style="font-size:.8rem;color:var(--tg-text-muted);">© {{ date('Y') }} TeleGateway. {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}</p>
    </footer>

</div><!-- /.tg-auth-page -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme toggle
    const _html   = document.documentElement;
    const _btn    = document.getElementById('themeToggleBtn');
    const _icon   = document.getElementById('themeToggleIcon');

    function _applyTheme(t) {
        _html.setAttribute('data-bs-theme', t);
        localStorage.setItem('tg-theme', t);
        _icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        if (t === 'dark') _icon.style.color = '#fcd34d';
        else _icon.style.color = '';
    }
    _applyTheme(localStorage.getItem('tg-theme') || 'light');
    _btn.addEventListener('click', () => {
        const cur = _html.getAttribute('data-bs-theme');
        _applyTheme(cur === 'dark' ? 'light' : 'dark');
    });

    // Contact form (demo)
    document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type=submit]');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        btn.disabled = true;
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Message sent!';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-send"></i> Send Message';
                btn.disabled = false;
                this.reset();
            }, 2000);
        }, 1000);
    });
</script>
</body>
</html>
