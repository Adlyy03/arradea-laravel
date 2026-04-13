<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/web/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'access_code' => 'ARRADEA2026',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'testing-token',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
}
