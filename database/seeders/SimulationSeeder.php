<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\User;
use App\Models\DeviceType;
use App\Models\DeviceMetric;
use App\Models\Command;
use Carbon\Carbon;

class SimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operator = User::role('operator')->first();
        $operatorId = $operator ? $operator->id : null;

        $devices = [
            [
                'device_id' => 'relay-001',
                'serial_number' => 'RLY-778899',
                'name' => 'Lighting Relay',
                'category' => 'actuator',
                'device_type_id' => 3, // Relay Module
                'status' => 'active',
                'user_id' => $operatorId,
            ],
            [
                'device_id' => 'router-001',
                'serial_number' => 'RTR-445566',
                'name' => 'Router',
                'category' => 'network',
                'device_type_id' => 1, // Router
                'status' => 'active',
                'user_id' => $operatorId,
            ],
            [
                'device_id' => 'temp-001',
                'serial_number' => 'TMP-112233',
                'name' => 'Lobby Temp Sensor',
                'category' => 'sensor',
                'device_type_id' => 2, // Temperature Sensor
                'status' => 'active',
                'user_id' => $operatorId,
            ],
        ];

        foreach ($devices as $deviceData) {
            $device = Device::updateOrCreate(
                ['device_id' => $deviceData['device_id']],
                $deviceData
            );

            // Seed sample metrics for the last 2 hours
            for ($i = 20; $i >= 0; $i--) {
                $time = Carbon::now()->subMinutes($i * 6);
                $processedData = [];

                if ($device->device_id === 'temp-001') {
                    $processedData = ['temperature' => 22 + (rand(-20, 20) / 10), 'humidity' => 50 + rand(-5, 5)];
                } elseif ($device->device_id === 'router-001') {
                    $processedData = ['bandwidth_mbps' => 85 + rand(-10, 10), 'latency_ms' => 15 + rand(0, 5)];
                } elseif ($device->device_id === 'relay-001') {
                    $processedData = ['state' => rand(0, 1) ? 'on' : 'off'];
                }

                DeviceMetric::create([
                    'device_id' => $device->device_id,
                    'raw_payload' => $processedData,
                    'processed_data' => $processedData,
                    'received_at' => $time,
                ]);
            }

            // Seed some sample commands
            Command::create([
                'device_id' => $device->id,
                'user_id' => $operatorId,
                'payload' => ['action' => 'reboot'],
                'status' => 'success',
                'response' => 'Device rebooted successfully',
                'sent_at' => Carbon::now()->subHour(),
                'response_at' => Carbon::now()->subHour()->addSeconds(45),
            ]);

            Command::create([
                'device_id' => $device->id,
                'user_id' => $operatorId,
                'payload' => ['action' => 'diagnostics'],
                'status' => 'success',
                'response' => 'All systems nominal',
                'sent_at' => Carbon::now()->subMinutes(30),
                'response_at' => Carbon::now()->subMinutes(29),
            ]);
        }
    }
}
