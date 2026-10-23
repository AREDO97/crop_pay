<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            $table->decimal('target_amount', 15, 2)
                ->default(0)
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            $table->decimal('target_amount', 15, 2)
                ->nullable()
                ->default(null)
                ->change();
        });
    }
};