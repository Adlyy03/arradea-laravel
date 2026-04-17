<?php

namespace Database\Factories;

use App\Models\AccessCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accessCode = AccessCode::query()->where('is_active', true)->first()
            ?? AccessCode::query()->create([
                'code' => 'ARRADEA2026',
                'is_active' => true,
            ]);

        return [
            'name' => fake()->name(),
            'phone' => '+628' . fake()->unique()->numerify('##########'),
            'wilayah' => 'Arradea',
            'access_code_id' => $accessCode->id,
            'phone_verified_at' => now(),
            'latitude' => 0,
            'longitude' => 0,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }
}
