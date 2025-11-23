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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('acceptance_criteria')->nullable();
            $table->string('estimated_points')->nullable(); // Final estimation result
            $table->integer('sort_order')->default(0);
            $table->boolean('is_current')->default(false); // Currently being estimated
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            $table->index(['game_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
