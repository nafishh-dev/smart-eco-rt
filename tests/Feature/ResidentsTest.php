<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_resident_and_redeem_points(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/warga', [
            'name' => 'Dewi Lestari',
            'phone' => '081234567899',
        ])->assertRedirect(route('warga'));

        $resident = Resident::where('phone', '081234567899')->firstOrFail();
        $resident->update(['points_balance' => 80]);

        $this->post(route('warga.redeem', $resident), ['points' => 30])
            ->assertRedirect(route('warga'));

        $this->assertSame(50, $resident->fresh()->points_balance);
    }

    public function test_admin_cannot_redeem_more_than_the_resident_balance(): void
    {
        $this->actingAs(User::factory()->create());
        $resident = Resident::create([
            'name' => 'Dewi Lestari',
            'phone' => '081234567899',
            'points_balance' => 20,
        ]);

        $this->from('/warga')
            ->post(route('warga.redeem', $resident), ['points' => 30])
            ->assertRedirect('/warga')
            ->assertSessionHasErrors('points');

        $this->assertSame(20, $resident->fresh()->points_balance);
    }
}
