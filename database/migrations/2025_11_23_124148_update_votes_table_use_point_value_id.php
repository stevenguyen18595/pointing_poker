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
        Schema::table('votes', function (Blueprint $table) {
            // Add foreign key to point_values table
            $table->foreignId('point_value_id')->after('player_id')->constrained('point_values');
            
            // Drop the old points_value column
            $table->dropColumn('points_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Add back the old points_value column
            $table->string('points_value')->after('player_id');
            
            // Drop the foreign key and column
            $table->dropForeign(['point_value_id']);
            $table->dropColumn('point_value_id');
        });
    }
};
