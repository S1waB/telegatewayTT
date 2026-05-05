<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'name' => $this->name,
            'type' => $this->category,
            'status' => $this->status,
            'last_seen_at' => $this->last_seen_at ? $this->last_seen_at->toIso8601String() : null,
            'latest_metric' => $this->metrics()->latest()->first(),
            'open_alerts_count' => $this->alerts()->whereNull('resolved_at')->count(),
            'metrics_history' => $this->metrics()->latest()->take(20)->get(),
        ];
    }
}
