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
        Schema::create('game_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // waiting, voting, revealed, completed
            $table->string('label'); // Waiting for Players, Voting in Progress, Results Revealed, Game Completed
            $table->string('description')->nullable();
            $table->string('color_class')->nullable(); // For UI styling: text-yellow-600, text-blue-600, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_statuses');
    }
};
