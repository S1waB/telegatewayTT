<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeviceData;
use App\Models\Device;

class DeviceDataSeeder extends Seeder
{
    public function run()
    {
        $sensors = Device::whereHas('type', function ($query) {
            $query->where('name', 'Temperature Sensor');
        })->get();

        foreach ($sensors as $sensor) {
            for ($i = 24; $i >= 0; $i--) {
                DeviceData::create([
                    'device_id' => $sensor->id,
                    'metric' => 'temperature',
                    'value' => 20 + rand(-50, 50) / 10, // 15.0 to 25.0
                    'unit' => 'C',
                    'recorded_at' => now()->subHours($i),
                ]);
            }
        }
    }
}
