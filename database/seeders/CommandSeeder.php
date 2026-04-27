<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Command;
use App\Models\Device;
use App\Models\User;

class CommandSeeder extends Seeder
{
    public function run()
    {
        $operator = User::where('email', 'operator@telegateway.io')->first();
        $router = Device::where('name', 'Main Office Router')->first();
        $relay = Device::where('name', 'Lighting Relay 1')->first();

        $statuses = ['pending', 'sent', 'success', 'failed'];

        for ($i = 0; $i < 10; $i++) {
            $device = $i % 2 == 0 ? $router : $relay;
            $status = $statuses[array_rand($statuses)];

            $payload = $device->id == $router->id 
                ? ['action' => 'reboot', 'timeout' => 60] 
                : ['action' => 'toggle', 'state' => 'on'];

            Command::create([
                'device_id' => $device->id,
                'user_id' => $operator->id,
                'payload' => $payload,
                'status' => $status,
                'response' => $status == 'success' ? '{"result": "ok"}' : ($status == 'failed' ? '{"error": "timeout"}' : null),
                'sent_at' => in_array($status, ['sent', 'success', 'failed']) ? now()->subMinutes(10 - $i) : null,
                'response_at' => in_array($status, ['success', 'failed']) ? now()->subMinutes(10 - $i)->addSeconds(5) : null,
            ]);
        }
    }
}
