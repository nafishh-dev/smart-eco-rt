<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Device::updateOrCreate(
            ['hub_id' => 'PANDES-HUB-01'],
            [
                'name' => 'Smart Eco Bin Pro',
                'location' => 'Pos Ronda RT 03',
                'wifi_ssid' => 'EcoHome_5G',
                'battery_percent' => 94,
                'last_seen_at' => now(),
            ],
        );

        foreach ([
            [
                'category' => 'organik',
                'name' => 'Sampah Organik',
                'subtitle' => 'Sisa Makanan & Daun',
                'unit' => 'kg',
                'current_weight' => 6.4,
                'capacity_percent' => 45,
                'status' => 'aman',
            ],
            [
                'category' => 'anorganik',
                'name' => 'Sampah Anorganik',
                'subtitle' => 'Plastik, Kaleng & Kertas',
                'unit' => 'kg',
                'current_weight' => 7.1,
                'capacity_percent' => 82,
                'status' => 'hampir_penuh',
            ],
            [
                'category' => 'jelantah',
                'name' => 'Minyak Jelantah',
                'subtitle' => 'Minyak Goreng Bekas',
                'unit' => 'Liter',
                'current_weight' => 1.3,
                'capacity_percent' => 28,
                'status' => 'aman',
            ],
        ] as $bin) {
            Bin::updateOrCreate(['category' => $bin['category']], $bin);
        }

        Notification::firstOrCreate(
            ['title' => 'Kapasitas Sampah Anorganik Hampir Penuh'],
            [
                'type' => 'warning',
                'message' => 'Kapasitas sampah anorganik telah mencapai 82%.',
                'bin_category' => 'anorganik',
                'capacity_snapshot' => 82,
                'is_read' => false,
                'is_resolved' => false,
            ],
        );

        Notification::firstOrCreate(
            ['title' => 'Penjemputan Minyak Jelantah Selesai'],
            [
                'type' => 'info',
                'message' => 'Pengosongan selesai • +50 Poin Eco ditambahkan.',
                'is_read' => true,
                'is_resolved' => true,
            ],
        );

        foreach ([
            ['organik', 'Setoran sampah organik', 2.5, 'kg', 25, 'Budi', 1],
            ['anorganik', 'Setoran sampah anorganik', 3.2, 'kg', 32, 'Siti', 2],
            ['jelantah', 'Setoran minyak jelantah', 1.0, 'Liter', 50, 'Andi', 3],
            ['organik', 'Setoran sampah organik', 1.8, 'kg', 18, 'Rina', 5],
            ['anorganik', 'Setoran sampah anorganik', 2.1, 'kg', 21, 'Dewi', 6],
        ] as [$category, $description, $amount, $unit, $points, $citizen, $daysAgo]) {
            $createdAt = now()->subDays($daysAgo);

            Transaction::firstOrCreate(
                [
                    'hub_id' => 'PANDES-HUB-01',
                    'category' => $category,
                    'description' => $description,
                    'amount' => $amount,
                    'citizen_name' => $citizen,
                ],
                [
                    'unit' => $unit,
                    'points_earned' => $points,
                    'status' => 'claimed',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ],
            );
        }
    }
}
