<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderBatch>
 */
class OrderBatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'customer_id'=> Member::factory(),
                'customer_name' => $this->faker->name(),
                'customer_address' => $this->faker->address(),
                'order_id' => $this->faker->unique()->numberBetween(1000, 9999),
                'created_at' => now(),
        ];
    }
}
