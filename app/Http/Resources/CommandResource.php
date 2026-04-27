<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payload' => $this->payload,
            'status' => $this->status,
            'status_badge' => $this->status_badge,
            'response' => $this->response,
            'sent_at' => $this->sent_at,
            'response_at' => $this->response_at,
            'device' => $this->whenLoaded('device', function () {
                return [
                    'id' => $this->device->id,
                    'name' => $this->device->name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
