<?php

namespace App\Services;

use App\Models\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;
use Exception;

class IoTGatewayService
{
    protected $host;
    protected $port;
    protected $clientId;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->host = config('mqtt.host');
        $this->port = config('mqtt.port');
        $this->clientId = config('mqtt.client_id');
        $this->username = config('mqtt.username');
        $this->password = config('mqtt.password');
    }

    public function sendCommand(Command $command): void
    {
        $command->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        try {
            $mqtt = new MqttClient($this->host, $this->port, $this->clientId);

            $connectionSettings = (new ConnectionSettings)
                ->setUsername($this->username)
                ->setPassword($this->password);

            $mqtt->connect($connectionSettings, true);

            $topic = "device/{$command->device_id}/command";
            
            $payload = json_encode([
                'command_id' => $command->id,
                'payload' => $command->payload
            ]);

            $mqtt->publish($topic, $payload, MqttClient::QOS_AT_MOST_ONCE);
            $mqtt->disconnect();
            
            Log::info("MQTT command sent to {$topic}", ['command_id' => $command->id]);

        } catch (Exception $e) {
            Log::warning("MQTT connection failed, fallback to HTTP expected.", ['error' => $e->getMessage(), 'command_id' => $command->id]);
            // keep status = 'sent' (HTTP callback expected)
            // If total failure is needed, you could update status to 'failed' here depending on business logic
        }
    }

    public function receiveResponse(int $deviceId, string $commandId, string $response, string $status): void
    {
        $command = Command::where('id', $commandId)->where('device_id', $deviceId)->first();

        if ($command) {
            $command->update([
                'status' => $status,
                'response' => $response,
                'response_at' => now(),
            ]);
            Log::info("Command {$commandId} response received", ['status' => $status]);
        } else {
            Log::error("Received response for unknown command", ['command_id' => $commandId, 'device_id' => $deviceId]);
        }
    }
}
