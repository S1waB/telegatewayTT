<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\DeviceMetric;
use App\Services\AlertEngine;
use App\Events\DeviceUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDeviceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job to store metrics, update status, and evaluate alerts.
     */
    public function handle(AlertEngine $alertEngine): void
    {
        $device = Device::where('device_id', $this->data['device_id'])->first();

        if (!$device) {
            return;
        }

        // 1. Store Device Metric
        $metric = DeviceMetric::create([
            'device_id' => $device->device_id,
            'raw_payload' => $this->data,
            'processed_data' => $this->data['payload'],
            'received_at' => $this->data['timestamp'],
        ]);

        // 2. Update Device Status
        $device->update([
            'status' => $this->data['status'],
            'last_seen_at' => $this->data['timestamp'],
        ]);

        // 3. Evaluate Alerts
        $alertEngine->evaluate($device, $metric);

        // 4. Broadcast Update
        event(new DeviceUpdated($device, $this->data));
    }
}
