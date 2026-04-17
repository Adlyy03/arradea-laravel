<?php

namespace Tests\Feature\Auth;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_can_be_verified_with_valid_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'phone' => '+628111111111',
        ]);

        Otp::create([
            'phone' => $user->phone,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'verified_at' => null,
        ]);

        $response = $this->withSession(['register_phone' => $user->phone])
            ->post('/phone/verify', [
                'code' => '123456',
            ]);

        $this->assertNotNull($user->fresh()->phone_verified_at);
        $response->assertRedirect(route('verification.admin.approval'));
    }

    public function test_phone_is_not_verified_with_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'phone' => '+628222222222',
        ]);

        Otp::create([
            'phone' => $user->phone,
            'code' => '654321',
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'verified_at' => null,
        ]);

        $response = $this->withSession(['register_phone' => $user->phone])
            ->post('/phone/verify', [
                'code' => '000000',
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertNull($user->fresh()->phone_verified_at);
    }
}
