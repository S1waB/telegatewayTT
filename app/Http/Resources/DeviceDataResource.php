<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'metric' => $this->metric,
            'value' => $this->value,
            'unit' => $this->unit,
            'recorded_at' => $this->recorded_at,
            'device_id' => $this->device_id,
        ];
    }
}
