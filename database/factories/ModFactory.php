<?php
namespace Database\Factories;

use App\Models\Mod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModFactory extends Factory
{
    protected $model = Mod::class;

    public function definition(): array
    {
        return [
            'name'         => $this->faker->words(2, true),
            'mod_id'       => $this->faker->unique()->slug(2),
            'description'  => $this->faker->sentence(),
            'icon_url'     => $this->faker->imageUrl(64, 64, 'abstract'),
            'modrinth_id'  => $this->faker->bothify('??######'),
            'curseforge_id' => $this->faker->randomNumber(6),
        ];
    }
}
