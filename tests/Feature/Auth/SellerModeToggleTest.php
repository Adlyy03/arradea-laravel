<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SellerModeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_activate_seller_mode_via_api(): void
    {
        $user = User::factory()->create(['is_seller' => false]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Toko Mobile',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Seller mode activated successfully.',
            ]);

        $this->assertTrue((bool) $user->fresh()->is_seller);
        $this->assertNotNull($user->fresh()->store);
    }

    public function test_user_can_deactivate_seller_mode_via_api(): void
    {
        $user = User::factory()->create(['is_seller' => true, 'seller_status' => 'approved']);
        $user->store()->create(['name' => 'Toko Existing']);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/seller-mode', [
            'enable' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Seller mode deactivated successfully.',
            ]);

        $this->assertFalse((bool) $user->fresh()->is_seller);
    }
}
