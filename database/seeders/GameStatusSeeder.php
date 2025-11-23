<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'waiting',
                'label' => 'Waiting for Players',
                'description' => 'Game is created but waiting for players to join',
                'color_class' => 'text-yellow-600 bg-yellow-100',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'voting',
                'label' => 'Voting in Progress',
                'description' => 'Players are currently voting on story points',
                'color_class' => 'text-blue-600 bg-blue-100',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'revealed',
                'label' => 'Results Revealed',
                'description' => 'Votes have been revealed and are visible to all players',
                'color_class' => 'text-green-600 bg-green-100',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'completed',
                'label' => 'Game Completed',
                'description' => 'Game session has ended',
                'color_class' => 'text-gray-600 bg-gray-100',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('game_statuses')->updateOrInsert(
                ['name' => $status['name']], // Check if exists by name
                array_merge($status, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
