<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Forbidden | {{ config('app.name', 'TeleGateway') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link href="{{ asset('css/telegateway.css') }}" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--tg-bg-body-tertiary);
        }
        .error-card {
            max-width: 500px;
            width: 100%;
            padding: 3rem;
            text-align: center;
            background: white;
            border-radius: 1.5rem;
            border: 1px solid var(--tg-card-border);
            box-shadow: var(--tg-card-shadow);
        }
        [data-bs-theme="dark"] .error-card {
            background: #111C2A;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            background: rgba(220, 53, 69, 0.1);
            width: 120px;
            height: 120px;
            line-height: 120px;
            border-radius: 50%;
            display: inline-block;
        }
        .error-code {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--tg-text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--tg-text-main);
        }
        .error-message {
            color: var(--tg-text-muted);
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center px-4">
        <div class="error-card shadow-lg">
            <div class="error-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="error-code">Error 403</div>
            <h1 class="error-title">Access Denied</h1>
            <p class="error-message">User does not have the right roles.</p>
            
            <div class="d-grid">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg shadow-sm py-3 rounded-pill">
                    <i class="bi bi-house-door-fill me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Apply saved theme
        const savedTheme = localStorage.getItem('tg-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
</body>
</html>
