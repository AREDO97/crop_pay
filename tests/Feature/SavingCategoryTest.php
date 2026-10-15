<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('admins can create saving category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    User::factory()->count(3)->create(['role' => 'farmer']);

    $response = $this->postJson('/api/v1/saving_category',[
        'name' =>'poultry',
        'description' =>'quality birds'
    ]);

    $response->assertStatus(200);
});

// only admins can update saving category
test('admins can update saving category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    User::factory()->count(3)->create(['role' => 'farmer']);

    $response = $this->postJson('/api/v1/saving_category',[
        'name' =>'poultry',
        'description' =>'quality birds'
    ]);

     $response = $this->patchJson('/api/v1/saving_category/1',[
        'name' =>'Maize',
        'description' =>'quality maize'
    ]);


    $response->assertStatus(200);
});


// only admins can delete saving category
test('admins can delete saving category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    User::factory()->count(3)->create(['role' => 'farmer']);

    $response = $this->postJson('/api/v1/saving_category',[
        'name' =>'poultry',
        'description' =>'quality birds'
    ]);
    // delete
     $response = $this->deleteJson('/api/v1/saving_category/1',[
        'is_active' =>false,
       
    ]);

    $response->assertStatus(200);
});


// all users view saving categories
test('all users can view saving category', function () {
    $user = User::factory()->create(['role' => 'farmer']);
    Sanctum::actingAs($user);

    User::factory()->count(3)->create(['role' => 'farmer']);

    $response = $this->postJson('/api/saving_category',[
        'name' =>'poultry',
        'description' =>'quality birds'
    ]);
    // view category
     $response = $this->getJson('/api/v1/saving_category');

    $response->assertStatus(200);
});