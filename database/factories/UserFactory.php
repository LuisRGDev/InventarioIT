<?php

namespace Database\Factories;

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
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Explícito en vez de confiar en el default de la columna a
            // nivel de BD: Eloquent no hidrata ese default en el modelo en
            // memoria tras create()/make(), así que sin esto
            // User::factory()->create()->active queda en null (no true)
            // hasta que el modelo se recarga desde la base. actingAs() en
            // los tests usa esa instancia en memoria tal cual, y
            // EnsureUserIsActive interpreta null como "no activo".
            'active' => true,
        ];
    }

    /**
     * Indicate that the user's account is deactivated.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
