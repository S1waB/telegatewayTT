<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\User;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        $operator = User::where('email', 'operator@telegateway.io')->first();
        $routerType = DeviceType::where('name', 'Router')->first();
        $sensorType = DeviceType::where('name', 'Temperature Sensor')->first();
        $relayType = DeviceType::where('name', 'Relay Module')->first();

        Device::create([
            'device_id' => 'router-001',
            'name' => 'Main Office Router',
            'serial_number' => 'RT-10001',
            'device_type_id' => $routerType->id,
            'user_id' => $operator->id,
            'status' => 'active',
            'ip_address' => '192.168.1.1',
            'location' => 'HQ - Server Room',
            'last_seen_at' => now(),
        ]);

        Device::create([
            'device_id' => 'sensor-001',
            'name' => 'Warehouse Temp Sensor',
            'serial_number' => 'TS-20001',
            'device_type_id' => $sensorType->id,
            'user_id' => $operator->id,
            'status' => 'active',
            'ip_address' => '10.0.0.15',
            'location' => 'Warehouse A',
            'last_seen_at' => now()->subMinutes(5),
        ]);

        Device::create([
            'device_id' => 'sensor-002',
            'name' => 'Lobby Temp Sensor',
            'serial_number' => 'TS-20002',
            'device_type_id' => $sensorType->id,
            'user_id' => $operator->id,
            'status' => 'inactive',
            'ip_address' => '10.0.0.16',
            'location' => 'HQ - Lobby',
            'last_seen_at' => now()->subDays(2),
        ]);

        Device::create([
            'device_id' => 'relay-001',
            'name' => 'Lighting Relay 1',
            'serial_number' => 'RL-30001',
            'device_type_id' => $relayType->id,
            'user_id' => $operator->id,
            'status' => 'active',
            'ip_address' => '192.168.2.10',
            'location' => 'Building B - Floor 1',
            'last_seen_at' => now(),
        ]);

        Device::create([
            'device_id' => 'relay-002',
            'name' => 'HVAC Relay',
            'serial_number' => 'RL-30002',
            'device_type_id' => $relayType->id,
            'user_id' => $operator->id,
            'status' => 'maintenance',
            'ip_address' => '192.168.2.11',
            'location' => 'Building B - Roof',
            'last_seen_at' => now()->subHours(1),
        ]);
    }
}
