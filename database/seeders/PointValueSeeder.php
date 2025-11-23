<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pointValues = [
            [
                'value' => '0',
                'label' => '0',
                'description' => 'No effort required',
                'color_class' => 'text-gray-600 bg-gray-100',
                'card_type' => 'number',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'value' => '0.5',
                'label' => '½',
                'description' => 'Minimal effort',
                'color_class' => 'text-blue-600 bg-blue-100',
                'card_type' => 'number',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'value' => '1',
                'label' => '1',
                'description' => 'Very small effort',
                'color_class' => 'text-green-600 bg-green-100',
                'card_type' => 'number',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'value' => '2',
                'label' => '2',
                'description' => 'Small effort',
                'color_class' => 'text-green-600 bg-green-100',
                'card_type' => 'number',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'value' => '3',
                'label' => '3',
                'description' => 'Medium effort',
                'color_class' => 'text-yellow-600 bg-yellow-100',
                'card_type' => 'number',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'value' => '5',
                'label' => '5',
                'description' => 'Moderate effort',
                'color_class' => 'text-yellow-600 bg-yellow-100',
                'card_type' => 'number',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'value' => '8',
                'label' => '8',
                'description' => 'Large effort',
                'color_class' => 'text-orange-600 bg-orange-100',
                'card_type' => 'number',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'value' => '13',
                'label' => '13',
                'description' => 'Very large effort',
                'color_class' => 'text-red-600 bg-red-100',
                'card_type' => 'number',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'value' => '21',
                'label' => '21',
                'description' => 'Huge effort',
                'color_class' => 'text-red-700 bg-red-200',
                'card_type' => 'number',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'value' => '34',
                'label' => '34',
                'description' => 'Enormous effort',
                'color_class' => 'text-red-800 bg-red-300',
                'card_type' => 'number',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'value' => '?',
                'label' => '?',
                'description' => 'Unknown complexity',
                'color_class' => 'text-purple-600 bg-purple-100',
                'card_type' => 'special',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'value' => '∞',
                'label' => '∞',
                'description' => 'Infinite complexity',
                'color_class' => 'text-purple-700 bg-purple-200',
                'card_type' => 'special',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'value' => '☕',
                'label' => '☕',
                'description' => 'Need a break',
                'color_class' => 'text-amber-600 bg-amber-100',
                'card_type' => 'break',
                'sort_order' => 13,
                'is_active' => true,
            ],
        ];

        foreach ($pointValues as $pointValue) {
            DB::table('point_values')->insert(array_merge($pointValue, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
