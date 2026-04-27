@props(['title', 'value', 'icon' => 'activity', 'color' => 'primary'])

<div class="card tg-card tg-stat-card h-100" style="border-left-color: var(--bs-{{ $color }})">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ $title }}</h6>
                <h3 class="mb-0 fw-bold">{{ $value }}</h3>
            </div>
            <div class="icon-circle" style="background-color: rgba(var(--bs-{{ $color }}-rgb), 0.1); color: var(--bs-{{ $color }});">
                <i data-feather="{{ $icon }}"></i>
            </div>
        </div>
    </div>
</div>
