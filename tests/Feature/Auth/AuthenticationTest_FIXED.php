<?php

namespace Tests\Feature\Auth;

use App\Models\AccessCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed access code untuk test
        AccessCode::create([
            'code' => 'TEST-CODE',
            'is_active' => true,
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $accessCode = AccessCode::first();
        
        $user = User::factory()->create([
            'phone_verified_at' => now(),
            'access_code_id' => $accessCode->id,
        ]);

        $response = $this->post('/web/login', [
            'phone' => $user->phone,
            'password' => 'password',
            'g-recaptcha-response' => 'testing-token',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $accessCode = AccessCode::first();
        
        $user = User::factory()->create([
            'phone_verified_at' => now(),
            'access_code_id' => $accessCode->id,
        ]);

        $this->post('/web/login', [
            'phone' => $user->phone,
            'password' => 'wrong-password',
            'g-recaptcha-response' => 'testing-token',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
