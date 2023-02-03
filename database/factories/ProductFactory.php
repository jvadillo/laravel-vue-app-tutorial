<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
            'description' => $this->faker->text($maxNbChars = 250),
            'price' => $this->faker->numberBetween(100, 4000),
        ];
    }
}
