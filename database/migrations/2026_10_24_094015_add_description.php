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
        Schema::table('saving_goals', function (Blueprint $table) {
            // description
            $table->text('description')->nullable();
            $table->timestamp('target_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            //
            $table->dropColumn('description');
            $table->dropColumn('target_date');
        });
    }
};
