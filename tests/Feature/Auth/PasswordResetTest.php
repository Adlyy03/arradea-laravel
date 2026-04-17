<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_requires_phone(): void
    {
        $response = $this->postJson('/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('phone');
    }

    public function test_password_reset_requires_phone_and_token(): void
    {
        $response = $this->postJson('/reset-password', [
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'token']);
    }
}
