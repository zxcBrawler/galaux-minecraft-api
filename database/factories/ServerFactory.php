<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Server>
 */
class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => fake()->company() . ' Minecraft',
            'ip'           => fake()->ipv4(),
            'player_count' => fake()->numberBetween(0, 500),
            'is_online'    => fake()->boolean(80),
            'is_official'  => fake()->boolean(20),
            'rating' => fake()->randomFloat(2, 3, 5),
            'version'      => fake()->randomElement(['1.0', '1.1', '2.0-beta']),
            'mc_version'   => fake()->randomElement(['1.20.1', '1.19.4', '1.16.5']),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
