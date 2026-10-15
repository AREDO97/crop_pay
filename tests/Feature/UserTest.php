<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('you can view other users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    User::factory()->count(3)->create(['role' => 'farmer']);

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(200);
});



test('admins can soft delete users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    $targetUser = User::factory()->create(['role' => 'farmer', 'is_active' => true]);

    $response = $this->patchJson("/api/v1/users/suspend/{$targetUser->id}", [
        'is_active' => false,
    ]);

    $response->assertStatus(200);
});

test('admins can unsuspend users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    $targetUser = User::factory()->create(['role' => 'farmer', 'is_active' => false]);

    $response = $this->patchJson("/api/v1/users/unsuspend/{$targetUser->id}", [
        'is_active' => true,
    ]);

    $response->assertStatus(200);
});

test('admin can view recent activity', function () {
    $user = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/userActivity');

    $response->assertStatus(200);
});


