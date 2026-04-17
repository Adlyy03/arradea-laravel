<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        Http::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => '+628123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/phone/verify');

        $this->assertDatabaseHas('users', [
            'phone' => '+628123456789',
            'name' => 'Test User',
        ]);

        $user = User::where('phone', '+628123456789')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->phone_verified_at);
    }
}
