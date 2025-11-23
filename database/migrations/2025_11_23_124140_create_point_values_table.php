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
        Schema::create('point_values', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique(); // '0', '1', '2', '3', '5', '8', '13', '21', '34', '?', '☕'
            $table->string('label'); // Display label for UI
            $table->string('description')->nullable(); // Optional description
            $table->string('color_class')->nullable(); // CSS class for styling
            $table->string('card_type')->default('number'); // 'number', 'special', 'break'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_values');
    }
};
