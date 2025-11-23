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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest players
            $table->boolean('is_moderator')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            
            $table->unique(['game_id', 'name']); // Unique name per game
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
