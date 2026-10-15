<?php

namespace Database\Factories;

use App\Models\SavingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavingCategory>
 */
class SavingCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
         'name'       =>fake()->name(),
        'description' => fake()->sentence(),
        'is_active'   => true,
        ];
    }
}
