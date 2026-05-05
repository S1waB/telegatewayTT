<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceMetric;
use App\Models\Alert;
use Illuminate\Support\Facades\Log;

class AlertEngine
{
    /**
     * Evaluate device metrics against defined thresholds and create alerts.
     */
    public function evaluate(Device $device, DeviceMetric $metric): void
    {
        $payload = $metric->processed_data;

        switch ($device->device_id) {
            case 'temp-001':
                $this->handleTemperatureAlerts($device, $payload);
                break;
            case 'router-001':
                $this->handleRouterAlerts($device, $payload);
                break;
            case 'relay-001':
                $this->logStateChange($device, $payload);
                break;
        }
    }

    /**
     * Handle temperature sensor thresholds.
     */
    protected function handleTemperatureAlerts(Device $device, array $payload): void
    {
        $temp = $payload['temperature'] ?? null;

        if ($temp === null) return;

        if ($temp > 35) {
            $this->createAlert($device, 'temperature', 'High temperature detected: ' . $temp . '°C', 'critical');
        } elseif ($temp < 0) {
            $this->createAlert($device, 'temperature', 'Low temperature warning: ' . $temp . '°C', 'warning');
        } else {
            $this->resolveOpenAlerts($device, 'temperature');
        }
    }

    /**
     * Handle router network thresholds.
     */
    protected function handleRouterAlerts(Device $device, array $payload): void
    {
        $status = $payload['connection_status'] ?? 'unknown';
        $bandwidth = $payload['bandwidth_mbps'] ?? 100;

        if ($status === 'error') {
            $this->createAlert($device, 'connection', 'Router error status detected', 'critical');
        } elseif ($bandwidth < 10) {
            $this->createAlert($device, 'bandwidth', 'Low bandwidth detected: ' . $bandwidth . ' Mb/s', 'warning');
        } else {
            $this->resolveOpenAlerts($device, 'connection');
            $this->resolveOpenAlerts($device, 'bandwidth');
        }
    }

    /**
     * Log state changes for actuators.
     */
    protected function logStateChange(Device $device, array $payload): void
    {
        Log::info("Actuator {$device->device_id} state changed: " . ($payload['state'] ?? 'unknown'));
    }

    /**
     * Create an alert if a duplicate open alert doesn't already exist.
     */
    protected function createAlert(Device $device, string $type, string $message, string $severity): void
    {
        $exists = Alert::where('device_id', $device->device_id)
            ->where('subject', $type)
            ->whereNull('resolved_at')
            ->exists();

        if (!$exists) {
            Alert::create([
                'device_id' => $device->device_id,
                'user_id' => $device->user_id,
                'subject' => $type,
                'description' => $message,
                'severity' => $severity,
                'status' => 'pending',
                'triggered_at' => now(),
            ]);
        }
    }

    /**
     * Mark open alerts of a specific type as resolved.
     */
    protected function resolveOpenAlerts(Device $device, string $type): void
    {
        Alert::where('device_id', $device->device_id)
            ->where('subject', $type)
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => now(),
                'status' => 'viewed'
            ]);
    }
}
