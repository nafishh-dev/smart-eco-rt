<?php

namespace Tests\Feature;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Resident;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpMqtt\Client\Facades\MQTT;
use Tests\TestCase;

class SmartEcoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_claims_all_unexpired_transactions_even_when_mqtt_publish_fails(): void
    {
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'jelantah',
            'description' => 'Minyak Dapur Bekas',
            'amount' => 0.5,
            'unit' => 'L',
            'points_earned' => 50,
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'anorganik',
            'description' => 'Setoran Sampah Anorganik',
            'amount' => 1.2,
            'unit' => 'kg',
            'points_earned' => 0,
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);
        MQTT::shouldReceive('publish')
            ->once()
            ->with('smarteco/pandes/klaim_sukses', 'OK')
            ->andThrow(new \RuntimeException('Broker unavailable.'));

        $this->postJson('/api/claim-otp', ['otp_code' => '4821'])
            ->assertOk()
            ->assertJsonPath('total_points', 50);

        $this->assertSame(0, Transaction::where('status', 'pending')->count());
        $this->assertSame(2, Transaction::where('status', 'claimed')->where('citizen_name', 'Warga RT')->count());
    }

    public function test_it_rejects_an_expired_otp(): void
    {
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'organik',
            'description' => 'Setoran Organik',
            'amount' => 1,
            'unit' => 'kg',
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->subSecond(),
        ]);

        $this->postJson('/api/claim-otp', ['otp_code' => '4821'])
            ->assertUnprocessable()
            ->assertExactJson(['message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);

        $this->assertDatabaseHas('transactions', ['otp_code' => '4821', 'status' => 'pending']);
    }

    public function test_public_claim_links_pending_settlements_to_resident_and_credits_only_oil_points(): void
    {
        $resident = Resident::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567801',
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'jelantah',
            'description' => 'Minyak Dapur Bekas',
            'amount' => 0.5,
            'unit' => 'L',
            'points_earned' => 50,
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(3),
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'anorganik',
            'description' => 'Setoran Sampah Anorganik',
            'amount' => 1.2,
            'unit' => 'kg',
            'points_earned' => 0,
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(3),
        ]);
        MQTT::shouldReceive('publish')
            ->once()
            ->with('smarteco/pandes/klaim_sukses', 'OK');

        $this->get('/klaim')->assertOk();
        $this->postJson('/api/claim-otp', [
            'otp_code' => '4821',
            'phone' => $resident->phone,
        ])
            ->assertOk()
            ->assertJsonPath('resident_name', 'Budi Santoso')
            ->assertJsonPath('total_points', 50)
            ->assertJsonPath('points_balance', 50);

        $this->assertSame(50, $resident->fresh()->points_balance);
        $this->assertSame(2, Transaction::where('resident_id', $resident->id)
            ->where('citizen_name', 'Budi Santoso')
            ->where('status', 'claimed')
            ->count());
    }

    public function test_resident_can_check_points_balance_without_authentication(): void
    {
        Resident::create([
            'name' => 'Siti Aminah',
            'phone' => '081234567802',
            'points_balance' => 75,
        ]);

        $this->getJson('/api/residents/balance?phone=081234567802')
            ->assertOk()
            ->assertJsonPath('resident_name', 'Siti Aminah')
            ->assertJsonPath('points_balance', 75);
    }

    public function test_it_rejects_an_otp_created_more_than_three_minutes_ago(): void
    {
        $transaction = Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'jelantah',
            'description' => 'Minyak Dapur Bekas',
            'amount' => 0.5,
            'unit' => 'L',
            'points_earned' => 50,
            'otp_code' => '4821',
            'status' => 'pending',
            'expires_at' => now()->addMinute(),
        ]);
        $transaction->forceFill(['created_at' => now()->subMinutes(4)])->save();

        $this->postJson('/api/claim-otp', ['otp_code' => '4821'])
            ->assertUnprocessable()
            ->assertExactJson(['message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
    }

    public function test_it_empties_a_bin_and_resolves_its_active_notifications(): void
    {
        $bin = Bin::create([
            'category' => 'anorganik',
            'name' => 'Sampah Anorganik',
            'subtitle' => 'Plastik dan Kertas',
            'unit' => 'kg',
            'current_weight' => 7.1,
            'capacity_percent' => 82,
            'status' => 'hampir_penuh',
        ]);
        Notification::create([
            'type' => 'warning',
            'title' => 'Penuh',
            'message' => 'Perlu dikosongkan.',
            'bin_category' => 'anorganik',
            'is_read' => false,
            'is_resolved' => false,
        ]);

        $this->postJson('/api/bins/anorganik/empty')->assertOk();

        $this->assertDatabaseHas('bins', [
            'id' => $bin->id,
            'current_weight' => 0,
            'capacity_percent' => 0,
            'status' => 'aman',
        ]);
        $this->assertDatabaseHas('notifications', [
            'title' => 'Penuh',
            'is_read' => true,
            'is_resolved' => true,
        ]);
        $this->assertDatabaseHas('notifications', [
            'title' => 'Penjemputan Anorganik Selesai',
            'message' => 'Pengosongan kompartemen selesai • Siap digunakan kembali.',
            'is_read' => false,
            'is_resolved' => true,
        ]);
    }

    public function test_it_handles_notification_and_device_actions(): void
    {
        Notification::create([
            'type' => 'warning',
            'title' => 'Kapasitas hampir penuh',
            'message' => 'Jadwalkan pengosongan.',
            'bin_category' => 'anorganik',
            'is_read' => false,
            'is_resolved' => false,
        ]);
        MQTT::shouldReceive('publish')
            ->once()
            ->with('smarteco/pandes/perintah_servo', 'ORGANIK');

        $this->postJson('/api/device/servo', ['direction' => 'ORGANIK'])->assertOk();
        $this->postJson('/api/notifications/read-all')->assertOk();
        $this->postJson('/api/device/call-officer')->assertOk();

        $this->assertDatabaseHas('notifications', [
            'title' => 'Petugas Kebersihan Dipanggil',
            'message' => 'Permintaan penjemputan manual telah dikirimkan ke petugas RT.',
            'is_read' => false,
            'is_resolved' => true,
        ]);
        $this->getJson('/api/live-status')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('active_warnings_count', 1);
    }

    public function test_it_serves_the_dashboard_pages_and_monthly_pdf(): void
    {
        Device::create([
            'hub_id' => 'PANDES-HUB-01',
            'name' => 'Smart Eco Bin Pro',
            'location' => 'Pos Ronda RT 03',
            'wifi_ssid' => 'EcoHome_5G',
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'organik',
            'description' => 'Setoran Organik',
            'amount' => 1,
            'unit' => 'kg',
            'status' => 'claimed',
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'jelantah',
            'description' => 'Minyak Dapur Bekas',
            'amount' => 0.5,
            'unit' => 'L',
            'points_earned' => 50,
            'status' => 'claimed',
        ]);
        Transaction::create([
            'hub_id' => 'PANDES-HUB-01',
            'category' => 'anorganik',
            'description' => 'Setoran Sampah Anorganik',
            'amount' => 2,
            'unit' => 'kg',
            'points_earned' => 0,
            'status' => 'claimed',
        ]);

        $this->actingAs(User::factory()->create([
            'name' => 'Pengurus RT 03',
            'email' => 'admin@smartecort.id',
        ]));
        $this->get('/dashboard')->assertOk()->assertDontSee('otp-form');
        $this->get('/riwayat')
            ->assertOk()
            ->assertSee('+50 Poin Eco')
            ->assertSee('Setoran Rutin');
        $this->get('/notifikasi')->assertOk();
        $this->get('/perangkat')->assertOk();
        $this->get('/profil')
            ->assertOk()
            ->assertSee('Pengurus RT 03')
            ->assertSee('admin@smartecort.id')
            ->assertSee('Admin & Operator IoT')
            ->assertSee('PANDES-HUB-01')
            ->assertSee('0,5')
            ->assertSee('50');
        $this->get('/warga')->assertOk();
        $this->get('/riwayat/export-pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-smart-eco-rt.pdf');
    }
}
