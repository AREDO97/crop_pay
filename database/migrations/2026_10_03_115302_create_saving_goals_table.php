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
        Schema::create('saving_goals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->constrained('saving_categories')
            ->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('target_amount',15,2)->nullable();
            $table->decimal('current_amount',15,2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    //2026_10_03_115302_create_saving_goals_table.php
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_goals');
    }
};
