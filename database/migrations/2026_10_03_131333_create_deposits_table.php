<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
        
    $table->uuid('id')->primary();

    $table->foreignUuid('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignUuid('saving_goal_id')
        ->constrained('saving_goals')
        ->cascadeOnDelete();

    $table->decimal('amount', 15, 2);

    //  unique payment reference.
    $table->string('reference')->unique();

    // pending, completed, failed
    $table->string('status')->default('pending');

    $table->string('payment_method')->nullable();

    // Pesapal  after creating the order.
    $table->string('pesapal_tracking_id')
        ->nullable()
        ->index();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
