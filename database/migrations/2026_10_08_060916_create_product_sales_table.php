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
        Schema::create('product_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity',15,2);
            $table->decimal('expected_unit_price',15,2)->nullable();
            $table->decimal('expected_total_amount',15,2)->nullable();
            $table->string('location');
            $table->string('unit')->nullable();
            $table->string('status')->default('Ready Now');
            $table->timestamp('ready_at_date')->nullable();
            $table->text('brief_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};
