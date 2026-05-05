<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimulateIoT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iot:simulate {--device=all} {--interval=5} {--count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate IoT device data transmission to the gateway API';

    protected $devices = [
        'relay-001' => ['type' => 'actuator', 'state' => 'off', 'brightness' => 50],
        'router-001' => ['type' => 'network', 'bandwidth_mbps' => 50, 'connection_status' => 'connected'],
        'temp-001' => ['type' => 'sensor', 'temperature' => 22.5, 'unit' => 'C', 'humidity' => 45],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        $count = (int) $this->option('count');
        $targetDevice = $this->option('device');

        $this->info("Starting IoT Simulation (Target: $targetDevice, Count: $count, Interval: {$interval}s)");
        $this->output->writeln("Timestamp            | Device ID   | Payload Preview                    | Status");
        $this->output->writeln("---------------------|-------------|------------------------------------|--------");

        for ($i = 0; $i < $count; $i++) {
            foreach ($this->devices as $id => $config) {
                if ($targetDevice !== 'all' && $targetDevice !== $id) continue;

                // 1. Update Simulation Data
                $this->updateSimulation($id, $i);

                // 2. Prepare Envelope
                $envelope = [
                    'device_id' => $id,
                    'type' => $config['type'],
                    'status' => 'online',
                    'timestamp' => now()->toIso8601String(),
                    'payload' => $this->devices[$id],
                ];

                // 3. Send Request
                try {
                    $response = Http::post(url('/api/gateway/receive'), $envelope);
                    $status = $response->status();
                } catch (\Exception $e) {
                    $status = 'ERROR';
                    Log::error("Simulation Error for $id: " . $e->getMessage());
                }

                // 4. Print Row
                $preview = json_encode(array_slice($this->devices[$id], 1, 2));
                $timestamp = now()->format('Y-m-d H:i:s');
                $this->output->writeln(sprintf("%-20s | %-11s | %-34s | %-6s", $timestamp, $id, $preview, $status));
            }

            if ($i < $count - 1) sleep($interval);
        }

        $this->info("Simulation complete.");
    }

    /**
     * Update device data based on simulation rules.
     */
    protected function updateSimulation(string $id, int $cycle): void
    {
        switch ($id) {
            case 'temp-001':
                $this->devices[$id]['temperature'] += rand(-5, 5) / 10; // Drift ±0.5
                break;
            case 'router-001':
                $this->devices[$id]['bandwidth_mbps'] += rand(-20, 20); // Drift ±20
                $this->devices[$id]['bandwidth_mbps'] = max(0, min(300, $this->devices[$id]['bandwidth_mbps']));
                
                // Random error status (5% chance)
                $this->devices[$id]['connection_status'] = (rand(1, 100) > 95) ? 'error' : 'connected';
                break;
            case 'relay-001':
                // Toggle state every 3 cycles
                if ($cycle % 3 === 0) {
                    $this->devices[$id]['state'] = ($this->devices[$id]['state'] === 'on') ? 'off' : 'on';
                }
                break;
        }
    }
}
