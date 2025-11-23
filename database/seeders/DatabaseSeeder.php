<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Game Statuses
        \App\Models\GameStatus::create(['name' => 'waiting', 'label' => 'Waiting for Players', 'description' => 'Waiting for players']);
        \App\Models\GameStatus::create(['name' => 'active', 'label' => 'Game Active', 'description' => 'Game is active']);
        \App\Models\GameStatus::create(['name' => 'completed', 'label' => 'Game Completed', 'description' => 'Game completed']);

        // Seed Point Values (Fibonacci sequence for Planning Poker)
        \App\Models\PointValue::create(['value' => '0', 'label' => '0']);
        \App\Models\PointValue::create(['value' => '1', 'label' => '1']);
        \App\Models\PointValue::create(['value' => '2', 'label' => '2']);
        \App\Models\PointValue::create(['value' => '3', 'label' => '3']);
        \App\Models\PointValue::create(['value' => '5', 'label' => '5']);
        \App\Models\PointValue::create(['value' => '8', 'label' => '8']);
        \App\Models\PointValue::create(['value' => '13', 'label' => '13']);
        \App\Models\PointValue::create(['value' => '21', 'label' => '21']);
        \App\Models\PointValue::create(['value' => '34', 'label' => '34']);
        \App\Models\PointValue::create(['value' => '?', 'label' => 'Unknown']);
        \App\Models\PointValue::create(['value' => '∞', 'label' => 'Infinite']);
    }
}
