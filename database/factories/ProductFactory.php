<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => fake()->randomElement(['VIP Статус', 'Набор ресурсов', '1000 Монет', 'Разбан', 'Титул "Король"']),
            'description'  => fake()->sentence(),
            'cost'         => fake()->randomFloat(2, 50, 5000),
            'bought_count' => fake()->numberBetween(0, 100),
            'icon_url'     => 'https://picsum.photos/seed/' . fake()->word . '/128/128',
            'item_id'      => fake()->word() . '_' . fake()->numberBetween(1, 100),
            'item_data'    => json_encode(['durability' => 100, 'color' => 'red']),
            'is_active'    => true,
            'sort_order'   => fake()->numberBetween(1, 10),
        ];
    }
}
