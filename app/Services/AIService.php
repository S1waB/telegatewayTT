<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Alert;
use App\Models\User;
use App\Models\Command;
use Carbon\Carbon;

class AIService
{
    /**
     * Classify device status based on behavior and history.
     */
    public function classifyStatus(Device $device): string
    {
        $lastSeen = $device->last_seen_at;
        $recentAlertsCount = Alert::where('device_id', $device->device_id)
            ->where('created_at', '>=', now()->subDays(3))
            ->count();

        if (!$lastSeen || $lastSeen->diffInHours(now()) > 24) {
            return 'inactive';
        }

        if ($recentAlertsCount > 5 || $device->status === 'maintenance') {
            return 'maintenance';
        }

        return 'active';
    }

    /**
     * Predict failure probability (0-100%).
     */
    public function predictFailureProbability(Device $device): int
    {
        $score = 0;

        // Factor 1: Connectivity (Last Seen)
        if ($device->last_seen_at) {
            $hoursSinceSeen = $device->last_seen_at->diffInHours(now());
            $score += min($hoursSinceSeen * 2, 30); // Max 30% from connectivity
        } else {
            $score += 40;
        }

        // Factor 2: Alerts
        $unresolvedAlerts = Alert::where('device_id', $device->device_id)
            ->whereNull('resolved_at')
            ->count();
        $score += min($unresolvedAlerts * 10, 40); // Max 40% from alerts

        // Factor 3: Metric Stability (Simulated Logic)
        // If device has many data points with high variance, increase risk
        $dataCount = $device->data()->count();
        if ($dataCount > 0) {
            $score += 5; // Base load risk
        }

        return min($score, 100);
    }

    /**
     * Get AI Interpretation and Advice.
     */
    public function getAdvice(Device $device): array
    {
        $probability = $this->predictFailureProbability($device);
        $status = $this->classifyStatus($device);

        if ($probability > 70) {
            return [
                'level' => 'danger',
                'title' => 'Critical Risk Detected',
                'message' => 'Device shows signs of imminent failure. High frequency of connection drops and unresolved alerts. Immediate maintenance recommended.',
                'action' => 'Schedule Technical Visit'
            ];
        }

        if ($probability > 40 || $status === 'maintenance') {
            return [
                'level' => 'warning',
                'title' => 'Preventive Warning',
                'message' => 'Intermittent behavior detected. Predicted uptime is decreasing. Monitor sensor data closely for fluctuations.',
                'action' => 'Run Diagnostics'
            ];
        }

        return [
            'level' => 'success',
            'title' => 'System Optimal',
            'message' => 'All parameters are within normal range. Predicted failure risk is low for the next 30 days.',
            'action' => 'View Full Report'
        ];
    }

    /**
     * Get Fleet Overview for Admin.
     */
    public function getFleetHealthOverview(): array
    {
        $devices = Device::all();
        $total = $devices->count();
        
        if ($total === 0) {
            return [
                'average_health' => 100,
                'critical_count' => 0,
                'status_distribution' => [
                    'active' => 0,
                    'inactive' => 0,
                    'maintenance' => 0
                ],
                'total_devices' => 0
            ];
        }

        $criticalCount = 0;
        $totalProbability = 0;
        $distribution = ['active' => 0, 'inactive' => 0, 'maintenance' => 0];

        foreach ($devices as $device) {
            $prob = $this->predictFailureProbability($device);
            $totalProbability += $prob;
            if ($prob > 70) $criticalCount++;
            
            $predictedStatus = $this->classifyStatus($device);
            $distribution[$predictedStatus]++;
        }

        return [
            'average_health' => round(100 - ($totalProbability / $total)),
            'critical_count' => $criticalCount,
            'status_distribution' => $distribution,
            'total_devices' => $total
        ];
    }

    /**
     * Get strategic AI advice for Admins regarding users and fleet.
     */
    public function getAdminStrategicAdvice(): array
    {
        $advice = [];

        // 1. User Performance Analysis
        $users = User::role('operator')->withCount('commands')->get();
        $totalCommands = Command::count();
        $avgCommands = $users->count() > 0 ? $totalCommands / $users->count() : 0;

        $lowActivityUsers = $users->filter(fn($u) => $u->commands_count < ($avgCommands * 0.5));
        if ($lowActivityUsers->count() > 0) {
            $advice[] = [
                'type' => 'user_performance',
                'level' => 'warning',
                'title' => __('messages.operator_engagement_gap'),
                'message' => __('messages.operator_engagement_gap_msg', ['count' => $lowActivityUsers->count()]),
                'icon' => 'users-slash'
            ];
        }

        $highSuccessUsers = $users->filter(fn($u) => $u->commands_count > $avgCommands);
        if ($highSuccessUsers->count() > 0) {
            $advice[] = [
                'type' => 'user_performance',
                'level' => 'success',
                'title' => __('messages.top_performers_identified'),
                'message' => __('messages.top_performers_identified_msg', ['name' => $highSuccessUsers->first()->name]),
                'icon' => 'trophy'
            ];
        }

        // 2. Fleet Health Analysis
        $fleetHealth = $this->getFleetHealthOverview();
        if ($fleetHealth['average_health'] < 80) {
            $advice[] = [
                'type' => 'fleet_health',
                'level' => 'danger',
                'title' => __('messages.critical_fleet_degradation'),
                'message' => __('messages.critical_fleet_degradation_msg', ['pct' => $fleetHealth['average_health']]),
                'icon' => 'exclamation-triangle'
            ];
        } else {
            $advice[] = [
                'type' => 'fleet_health',
                'level' => 'success',
                'title' => __('messages.fleet_stability_high'),
                'message' => __('messages.fleet_stability_high_msg'),
                'icon' => 'shield-check'
            ];
        }

        // 3. Alert Response Analysis
        $avgResponseTime = Alert::whereNotNull('device_id')->whereNotNull('resolved_at')
            ->get()
            ->avg(fn($a) => $a->triggered_at->diffInMinutes($a->resolved_at));

        if ($avgResponseTime > 120) {
            $advice[] = [
                'type' => 'operational_efficiency',
                'level' => 'warning',
                'title' => __('messages.slow_incident_response'),
                'message' => __('messages.slow_incident_response_msg'),
                'icon' => 'clock'
            ];
        }

        return $advice;
    }

    /**
     * Get chart data for Admin Strategic insights.
     */
    public function getAdminStrategicChartData(): array
    {
        // 1. User Performance (Commands by Operator)
        $operators = User::role('operator')->withCount('commands')->take(5)->get();
        $userPerformance = [
            'labels' => $operators->pluck('name')->toArray(),
            'data' => $operators->pluck('commands_count')->toArray(),
        ];

        // 2. Incident Response Trend (Last 7 days)
        $responseTrend = [
            'labels' => [],
            'data' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $responseTrend['labels'][] = $date->format('M d');
            
            $avg = Alert::whereDate('created_at', $date)
                ->whereNotNull('resolved_at')
                ->get()
                ->avg(fn($a) => $a->triggered_at->diffInMinutes($a->resolved_at)) ?? 0;
            
            $responseTrend['data'][] = round($avg);
        }

        return [
            'userPerformance' => $userPerformance,
            'responseTrend' => $responseTrend
        ];
    }

    /**
     * Get chart data for Operator specific devices.
     */
    public function getOperatorHealthDistribution(int $userId): array
    {
        $devices = Device::assignedTo($userId)->get();
        $distribution = ['good' => 0, 'warning' => 0, 'critical' => 0];

        foreach ($devices as $device) {
            $prob = $this->predictFailureProbability($device);
            if ($prob > 70) $distribution['critical']++;
            elseif ($prob > 40) $distribution['warning']++;
            else $distribution['good']++;
        }

        return [
            'labels' => [
                __('messages.good_health'),
                __('messages.warning_health'),
                __('messages.critical_health')
            ],
            'data' => array_values($distribution),
            'colors' => ['#22c55e', '#f59e0b', '#ef4444']
        ];
    }

    /**
     * Analyze recent telemetry data for a specific device.
     */
    public function analyzeTelemetry(Device $device): array
    {
        $data = $device->data()->latest('received_at')->take(10)->get();
        
        if ($data->isEmpty()) {
            return [
                'status' => 'info',
                'title' => __('messages.no_telemetry_data_ai'),
                'message' => __('messages.no_telemetry_data_ai_msg'),
                'advice' => __('messages.no_advice_available')
            ];
        }

        // Extract values from JSON payload based on device type
        $values = $data->map(function($m) {
            $pd = $m->processed_data;
            if (isset($pd['temperature'])) return $pd['temperature'];
            if (isset($pd['bandwidth_mbps'])) return $pd['bandwidth_mbps'];
            if (isset($pd['brightness'])) return $pd['brightness'];
            return 0;
        });

        $avg = $values->avg();
        $max = $values->max();
        $min = $values->min();
        $variance = $max - $min;

        // Simple Anomaly Detection logic
        if ($variance > ($avg * 0.5)) {
            return [
                'status' => 'danger',
                'title' => __('messages.unstable_telemetry'),
                'message' => __('messages.unstable_telemetry_msg', ['variance' => round($variance, 2)]),
                'advice' => __('messages.unstable_telemetry_advice')
            ];
        }

        if ($avg > 80) {
            return [
                'status' => 'warning',
                'title' => __('messages.high_load_detected'),
                'message' => __('messages.high_load_detected_msg', ['avg' => round($avg, 2)]),
                'advice' => __('messages.high_load_detected_advice')
            ];
        }

        return [
            'status' => 'success',
            'title' => __('messages.stable_metrics'),
            'message' => __('messages.stable_metrics_msg', ['avg' => round($avg, 2)]),
            'advice' => __('messages.stable_metrics_advice')
        ];
    }
}
