<?php

namespace Database\Factories;

use App\Models\SavingCategory;
use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavingGoal>
 */
class SavingGoalFactory extends Factory
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
        'user_id'       =>User::factory()->create(),
        'category_id'   =>SavingCategory::factory()->create(),
        'target_amount' =>fake()->numberBetween(100,10000),
        'current_amount'=>fake()->numberBetween(100,10000),
        'is_active'     =>true
        ];
    }
}
