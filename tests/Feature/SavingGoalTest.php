<?php

use App\Models\SavingCategory;
use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// users can only see there saving gaols
test('users can view saving goals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);
    $category = SavingCategory::factory()->create();
    $response = $this->postJson('/api/v1/saving_goals/create',[
        'user_id'       =>$admin->id,
        'category_id'   =>$category->id,
        'target_amount' =>10000,
    ]);
    $response= $this->getJson('/api/v1/saving_goals');

    $response->assertStatus(200);
});

// users can update their saving goals
test('users can update saving goals', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Sanctum::actingAs($admin);

    $category = SavingCategory::factory()->create();

    $goal = SavingGoal::factory()->create([
        'user_id' => $admin->id
    ]);

    $response = $this->patchJson("/api/v1/saving_goals/{$goal->id}", [
        'category_id' => $category->id,
    ]);

    $response->assertStatus(200);
});
