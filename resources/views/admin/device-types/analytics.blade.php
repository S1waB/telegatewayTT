@extends('layouts.app')
@section('title', __('messages.hardware_library_intelligence'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.device-types.index') }}" class="btn btn-sm btn-light border">
        <i class="bi bi-arrow-left me-1"></i> {{ __('messages.back_to_library') }}
    </a>
    <a href="{{ route('admin.device-types.export') }}" class="btn btn-sm btn-dark shadow-sm">
        <i class="bi bi-download me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card tg-card border-0">
            <div class="card-header bg-white p-4 border-bottom">
                <h6 class="fw-bold mb-0">{{ __('messages.hardware_utilization_by_category') }}</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="utilizationChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @foreach($deviceTypes as $type)
    <div class="col-md-4">
        <div class="card tg-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-light text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-{{ $type->icon ?? 'cpu' }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $type->name }}</h6>
                        <div class="small text-muted">ID: #{{ $type->id }}</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">{{ __('messages.active_vs_total') }}</span>
                    <span class="small fw-bold">{{ $type->active_count }} / {{ $type->devices_count }}</span>
                </div>
                <div class="progress" style="height: 6px;">
                    @php
                        $pct = $type->devices_count > 0 ? ($type->active_count / $type->devices_count) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('utilizationChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($deviceTypes->pluck('name')),
                datasets: [
                    {
                        label: '{{ __('messages.active_hardware') }}',
                        data: @json($deviceTypes->pluck('active_count')),
                        backgroundColor: '#198754',
                        borderRadius: 6
                    },
                    {
                        label: '{{ __('messages.inactive_hardware') }}',
                        data: @json($deviceTypes->map(function($t) { return $t->devices_count - $t->active_count; })),
                        backgroundColor: '#dee2e6',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
@endsection
