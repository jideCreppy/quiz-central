<?php

namespace Database\Seeders;

use App\Models\Difficulty;
use Illuminate\Database\Seeder;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Difficulty::factory()->create([
            'label' => 'Easy 😌',
            'value' => 'easy',
        ]);

        Difficulty::factory()->create([
            'label' => 'Medium 😥',
            'value' => 'medium',
        ]);

        Difficulty::factory()->create([
            'label' => 'Hard 😱',
            'value' => 'hard',
        ]);
    }
}
