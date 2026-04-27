<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeviceType;
use App\Models\User;

class DeviceTypeSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@telegateway.io')->first();

        DeviceType::create([
            'name' => 'Router',
            'description' => 'Network routing device',
            'icon' => 'router',
            'created_by' => $admin->id,
        ]);

        DeviceType::create([
            'name' => 'Temperature Sensor',
            'description' => 'Measures ambient temperature',
            'icon' => 'thermometer',
            'created_by' => $admin->id,
        ]);

        DeviceType::create([
            'name' => 'Relay Module',
            'description' => 'Controls high voltage circuits',
            'icon' => 'toggle-on',
            'created_by' => $admin->id,
        ]);
    }
}
