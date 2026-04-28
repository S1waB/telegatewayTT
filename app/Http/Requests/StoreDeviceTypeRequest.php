<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:device_types,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'custom_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
    }
}
