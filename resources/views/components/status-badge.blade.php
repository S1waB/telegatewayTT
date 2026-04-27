@props(['status'])

@php
    $color = match ($status) {
        'pending' => 'warning',
        'sent' => 'info',
        'success' => 'success',
        'failed' => 'danger',
        'active' => 'success',
        'inactive' => 'secondary',
        'maintenance' => 'warning',
        default => 'secondary',
    };
@endphp

<span class="badge bg-{{ $color }} px-2 py-1 rounded-pill">
    {{ ucfirst($status) }}
</span>
