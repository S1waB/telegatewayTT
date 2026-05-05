<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(Device $device, array $envelope)
    {
        $this->device = $device;
        $this->payload = $envelope;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('iot-gateway'),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->device->device_id,
            'type' => $this->device->category,
            'status' => $this->device->status,
            'payload' => $this->payload['payload'],
            'timestamp' => $this->payload['timestamp'],
            'alerts' => $this->device->alerts()
                ->whereNull('resolved_at')
                ->latest()
                ->take(3)
                ->get(),
        ];
    }
}
