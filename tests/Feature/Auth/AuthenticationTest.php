<?php

namespace Tests\Feature\Auth;

use App\Models\AccessCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'location.center_lat' => -6.200000,
            'location.center_lng' => 106.816666,
            'location.max_radius' => 5,
        ]);

        // Seed access code untuk test
        AccessCode::create([
            'code' => 'TEST-CODE',
            'is_active' => true,
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        Http::fake();

        $accessCode = AccessCode::first();

        $user = User::factory()->create([
            'phone_verified_at' => now(),
            'access_code_id' => $accessCode->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response = $this->post('/login', [
            'phone' => $user->phone,
            'password' => 'password',
            'g-recaptcha-response' => 'testing-token',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        Http::fake();

        $accessCode = AccessCode::first();

        $user = User::factory()->create([
            'phone_verified_at' => now(),
            'access_code_id' => $accessCode->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $this->post('/login', [
            'phone' => $user->phone,
            'password' => 'wrong-password',
            'g-recaptcha-response' => 'testing-token',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $accessCode = AccessCode::first();

        $user = User::factory()->create([
            'phone_verified_at' => now(),
            'access_code_id' => $accessCode->id,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}



