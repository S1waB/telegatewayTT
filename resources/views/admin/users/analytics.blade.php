@extends('layouts.app')
@section('title', 'User Intelligence Reports')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border">
        <i class="bi bi-arrow-left me-1"></i> Back to Users
    </a>
    <a href="{{ route('admin.users.export') }}" class="btn btn-sm btn-dark shadow-sm">
        <i class="bi bi-download me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-4">
        <div class="card tg-card border-0 h-100">
            <div class="card-body p-4 text-center">
                <div class="bg-primary-light text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Workforce</h6>
                <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                <div class="mt-2 small text-success">
                    <i class="bi bi-person-check me-1"></i> {{ $activeUsers }} Active
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card tg-card border-0 h-100">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Role Distribution</h6>
                <i class="bi bi-shield-shaded text-muted"></i>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <canvas id="roleChart" style="max-height: 200px;"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div id="roleLegend" class="d-flex flex-column gap-2">
                            @foreach($roleDistribution as $role)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-medium">{{ ucfirst($role->name) }}</span>
                                    <span class="badge bg-light text-dark border">{{ $role->total }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Activity -->
    <div class="col-md-12">
        <div class="tg-table-container">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="mb-0 fw-bold">Recent User Activity</h6>
                <span class="badge bg-primary rounded-pill">Top 10 Active</span>
            </div>
            <div class="table-responsive">
                <table class="table tg-table mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Last Active</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogins as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-avatar :url="$user->avatar_url" :size="32" class="me-2" />
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-light text-primary border small">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="small text-muted">{{ $user->last_active_at->diffForHumans() }}</td>
                            <td>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}-subtle text-{{ $user->is_active ? 'success' : 'secondary' }} small">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('roleChart').getContext('2d');
        const roles = @json($roleDistribution->pluck('name'));
        const totals = @json($roleDistribution->pluck('total'));
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: roles,
                datasets: [{
                    data: totals,
                    backgroundColor: ['#1A6FBF', '#198754', '#ffc107', '#dc3545', '#6610f2'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
@endsection
