<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'ip_address' => $this->ip_address,
            'location' => $this->location,
            'avatar_url' => $this->avatar_url,
            'last_seen_at' => $this->last_seen_at,
            'device_type' => new DeviceTypeResource($this->whenLoaded('type')),
            'assigned_user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
