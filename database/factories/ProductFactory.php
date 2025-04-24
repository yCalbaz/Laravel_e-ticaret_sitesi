<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produc>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;
    public function definition(): array
    {
        return [
            'product_name' => $this->faker->word,
            'product_sku' => $this->faker->unique()->word,
            'product_price' => $this->faker->randomFloat(2, 1, 1000),
            'product_image' => $this->faker->imageUrl(),
            'details' => $this->faker->sentence,
        ];
    }
}
