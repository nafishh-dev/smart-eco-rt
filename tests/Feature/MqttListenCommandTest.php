<?php

namespace Tests\Feature;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Facades\MQTT;
use Tests\TestCase;

class MqttListenCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_processes_sensor_and_transaction_messages(): void
    {
        Device::create([
            'hub_id' => 'PANDES-HUB-01',
            'name' => 'Smart Eco Bin Pro',
            'location' => 'Pos Ronda RT 03',
            'wifi_ssid' => 'EcoHome_5G',
        ]);

        foreach (['jelantah', 'anorganik'] as $category) {
            Bin::create([
                'category' => $category,
                'name' => $category,
                'subtitle' => $category,
                'unit' => $category === 'jelantah' ? 'Liter' : 'kg',
            ]);
        }

        $callbacks = [];
        $mqtt = \Mockery::mock(MqttClient::class);
        $mqtt->shouldReceive('subscribe')
            ->twice()
            ->andReturnUsing(function (string $topic, ?callable $callback = null) use (&$callbacks): void {
                $callbacks[$topic] = $callback;
            });
        $mqtt->shouldReceive('loop')
            ->once()
            ->with(true)
            ->andReturnUsing(function () use (&$callbacks): void {
                $callbacks['smarteco/pandes/sensor']('smarteco/pandes/sensor', '{invalid json');
                $callbacks['smarteco/pandes/sensor'](
                    'smarteco/pandes/sensor',
                    '{"id_hub":"PANDES-HUB-01","jarak_jelantah":20,"jarak_sampah":25,"liter_jelantah":1.3,"kg_anorganik":7.1}',
                );
                $callbacks['smarteco/pandes/sensor'](
                    'smarteco/pandes/sensor',
                    '{"id_hub":"PANDES-HUB-01","jarak_jelantah":999,"jarak_sampah":5,"liter_jelantah":1.3,"kg_anorganik":7.1}',
                );
                $callbacks['smarteco/pandes/transaksi']('smarteco/pandes/transaksi', '{invalid json');
                $callbacks['smarteco/pandes/transaksi'](
                    'smarteco/pandes/transaksi',
                    '{"id_hub":"PANDES-HUB-01","kode_otp":"4821","liter_jelantah":0.5,"kg_anorganik":1.2}',
                );
            });

        $messageReceivedAt = now();
        MQTT::shouldReceive('connection')->once()->andReturn($mqtt);

        $this->artisan('mqtt:listen')->assertExitCode(0);

        $this->assertNotNull(Device::where('hub_id', 'PANDES-HUB-01')->first()->last_seen_at);
        $this->assertDatabaseHas('bins', [
            'category' => 'jelantah',
            'current_weight' => 1.3,
            'capacity_percent' => 86,
            'status' => 'hampir_penuh',
        ]);
        $this->assertDatabaseHas('bins', [
            'category' => 'anorganik',
            'current_weight' => 7.1,
            'capacity_percent' => 100,
            'status' => 'hampir_penuh',
        ]);
        foreach (Transaction::all() as $transaction) {
            $expiresIn = abs($transaction->expires_at->diffInSeconds($messageReceivedAt));
            $this->assertGreaterThanOrEqual(179, $expiresIn);
            $this->assertLessThanOrEqual(181, $expiresIn);
        }
        $this->assertDatabaseHas('notifications', [
            'bin_category' => 'jelantah',
            'capacity_snapshot' => 86,
            'is_resolved' => false,
        ]);
        $this->assertDatabaseHas('notifications', [
            'bin_category' => 'anorganik',
            'capacity_snapshot' => 100,
            'is_resolved' => false,
        ]);
        $this->assertSame(2, Notification::count());

        $this->assertDatabaseHas('transactions', [
            'category' => 'jelantah',
            'description' => 'Minyak Dapur Bekas',
            'amount' => 0.5,
            'unit' => 'L',
            'points_earned' => 50,
            'otp_code' => '4821',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('transactions', [
            'category' => 'anorganik',
            'description' => 'Setoran Sampah Anorganik',
            'amount' => 1.2,
            'unit' => 'kg',
            'points_earned' => 0,
            'otp_code' => '4821',
            'status' => 'pending',
        ]);
        $this->assertSame(2, Transaction::count());
    }
}
