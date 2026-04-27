<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices,serial_number,' . $this->device->id,
            'device_type_id' => 'required|exists:device_types,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,maintenance',
            'ip_address' => 'nullable|ip',
            'location' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
