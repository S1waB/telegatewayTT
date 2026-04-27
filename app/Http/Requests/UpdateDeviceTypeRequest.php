<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:device_types,name,' . $this->device_type->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ];
    }
}
