<?php

namespace App\Console\Commands;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use JsonException;

class MqttListenCommand extends Command
{
    protected $signature = 'mqtt:listen';

    protected $description = 'Listen for Smart Eco-RT MQTT sensor and transaction messages';

    public function handle(): int
    {
        $mqtt = MQTT::connection();

        $mqtt->subscribe('smarteco/pandes/sensor', function (string $topic, string $message): void {
            $data = $this->decodePayload($message, [
                'id_hub',
                'jarak_jelantah',
                'jarak_sampah',
                'liter_jelantah',
                'kg_anorganik',
            ]);

            if ($data === null) {
                return;
            }

            if (! is_string($data['id_hub']) || $data['id_hub'] === ''
                || ! is_numeric($data['jarak_jelantah'])
                || ! is_numeric($data['jarak_sampah'])
                || ! is_numeric($data['liter_jelantah'])
                || ! is_numeric($data['kg_anorganik'])) {
                $this->error("Payload sensor tidak valid pada topik [{$topic}].");

                return;
            }

            Device::where('hub_id', $data['id_hub'])->update([
                'last_seen_at' => now(),
            ]);

            $this->updateBin(
                'jelantah',
                (float) $data['jarak_jelantah'],
                (float) $data['liter_jelantah'],
            );
            $this->updateBin(
                'anorganik',
                (float) $data['jarak_sampah'],
                (float) $data['kg_anorganik'],
            );

            $this->info("Pesan sensor berhasil diproses untuk hub {$data['id_hub']}.");
        });

        $mqtt->subscribe('smarteco/pandes/transaksi', function (string $topic, string $message): void {
            $data = $this->decodePayload($message, [
                'id_hub',
                'kode_otp',
                'liter_jelantah',
                'kg_anorganik',
            ]);

            if ($data === null) {
                return;
            }

            if (! is_string($data['id_hub']) || $data['id_hub'] === ''
                || ! is_scalar($data['kode_otp'])
                || ! is_numeric($data['liter_jelantah'])
                || ! is_numeric($data['kg_anorganik'])) {
                $this->error("Payload transaksi tidak valid pada topik [{$topic}].");

                return;
            }

            $otpCode = (string) $data['kode_otp'];
            $literJelantah = (float) $data['liter_jelantah'];
            $kgAnorganik = (float) $data['kg_anorganik'];

            if ($literJelantah > 0) {
                Transaction::create([
                    'hub_id' => $data['id_hub'],
                    'category' => 'jelantah',
                    'description' => 'Minyak Dapur Bekas',
                    'amount' => $literJelantah,
                    'unit' => 'L',
                    'points_earned' => (int) round($literJelantah * 100),
                    'otp_code' => $otpCode,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(5),
                ]);
            }

            if ($kgAnorganik > 0) {
                Transaction::create([
                    'hub_id' => $data['id_hub'],
                    'category' => 'anorganik',
                    'description' => 'Setoran Sampah Anorganik',
                    'amount' => $kgAnorganik,
                    'unit' => 'kg',
                    'points_earned' => (int) round($kgAnorganik * 50),
                    'otp_code' => $otpCode,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(5),
                ]);
            }

            $this->info("Pesan transaksi berhasil diproses untuk hub {$data['id_hub']}.");
        });

        $this->info('Mendengarkan topik MQTT Smart Eco-RT...');
        $mqtt->loop(true);

        return self::SUCCESS;
    }

    /**
     * @param  list<string>  $requiredFields
     * @return array<string, mixed>|null
     */
    private function decodePayload(string $message, array $requiredFields): ?array
    {
        try {
            $data = json_decode($message, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->error('JSON MQTT tidak valid: '.$exception->getMessage());

            return null;
        }

        if (! is_array($data)) {
            $this->error('Payload MQTT harus berupa objek JSON.');

            return null;
        }

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $data)) {
                $this->error("Payload MQTT tidak memiliki field wajib [{$field}].");

                return null;
            }
        }

        return $data;
    }

    private function updateBin(string $category, float $distance, float $weight): void
    {
        $bin = Bin::where('category', $category)->first();

        if ($bin === null) {
            $this->error("Bin kategori [{$category}] tidak ditemukan.");

            return;
        }

        if ($distance !== 999.0) {
            $bin->capacity_percent = $this->capacityFromDistance($distance);
        }

        $bin->current_weight = max(0, $weight);
        $bin->status = $bin->capacity_percent >= 80 ? 'hampir_penuh' : 'aman';
        $bin->save();

        if ($bin->capacity_percent >= 80
            && ! Notification::where('bin_category', $category)
                ->where('is_resolved', false)
                ->exists()) {
            Notification::create([
                'type' => 'warning',
                'title' => 'Kapasitas '.ucfirst($category).' Hampir Penuh',
                'message' => 'Kapasitas mencapai '.$bin->capacity_percent.'%. Segera jadwalkan pengosongan.',
                'bin_category' => $category,
                'capacity_snapshot' => $bin->capacity_percent,
                'is_read' => false,
                'is_resolved' => false,
            ]);
        }
    }

    private function capacityFromDistance(float $distance): int
    {
        return max(0, min(100, (int) round(((50 - $distance) / (50 - 15)) * 100)));
    }
}
