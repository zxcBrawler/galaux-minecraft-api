<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'login' => fake()->unique()->userName(),
            'password_hash' => bcrypt('password'),
            'email' => fake()->unique()->safeEmail(),
            'uuid' => fake()->uuid(),
            'is_online' => fake()->boolean(30),
            'cosmetics' => json_encode(['skin' => 'default', 'cape' => 'none']),
            'role' => 'user',
            'is_banned' => false,
            'money' => fake()->randomFloat(2, 0, 1000),
            'telegram_link' => 'https://t.me/' . fake()->userName(),
            'discord_link' => fake()->userName() . '#0000',
            'profile_info' => fake()->sentence(),
            'is_private' => fake()->boolean(20),
            'who_can_message' => 'all',
            'last_seen' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
//    public function unverified(): static
//    {
//        return $this->state(fn (array $attributes) => [
//            'email_verified_at' => null,
//        ]);
//    }
}
