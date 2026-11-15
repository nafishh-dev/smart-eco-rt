<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_officer_account_can_log_in_and_log_out(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::where('email', 'admin@smartecort.id')->firstOrFail();
        $this->assertSame('Pengurus RT 03', $user->name);
        $this->assertTrue(Hash::check('password', $user->password));

        $this->get('/login')->assertOk();
        $this->post('/login', [
            'email' => 'admin@smartecort.id',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertOk();

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_web_pages_redirect_guests_to_login(): void
    {
        foreach ([
            '/',
            '/riwayat',
            '/riwayat/export-pdf',
            '/notifikasi',
            '/perangkat',
            '/profil',
        ] as $uri) {
            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    public function test_database_seeder_awards_points_only_for_jelantah(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            0,
            (int) Transaction::whereIn('category', ['organik', 'anorganik'])->sum('points_earned'),
        );
        $this->assertSame(
            50,
            (int) Transaction::where('category', 'jelantah')->sum('points_earned'),
        );
    }
}
