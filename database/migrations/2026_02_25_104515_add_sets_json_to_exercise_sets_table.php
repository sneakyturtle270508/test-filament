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
        Schema::table('exercise_sets', function (Blueprint $table) {
            $table->json('sets')->nullable();
            $table->dropColumn(['reps', 'weight']);
        });
    }

    public function down(): void
    {
        Schema::table('exercise_sets', function (Blueprint $table) {
            $table->dropColumn('sets');
            $table->integer('reps')->nullable();
            $table->decimal('weight', 5, 1)->nullable();
        });
    }
};
