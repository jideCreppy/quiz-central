<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\QuizType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Category::factory()->create([ // Add more categories as needed. see https://opentdb.com/api_config.php for more values that can be added to the database.
            'label' => 'Entertainment (Film)',
            'value' => 11,
        ]);

        Category::factory()->create([
            'label' => 'Science & Nature',
            'value' => 17,
        ]);

        Category::factory()->create([
            'label' => 'History',
            'value' => 23,
        ]);

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

        QuizType::factory()->create([
            'label' => 'True / False',
            'value' => 'boolean',
        ]);

        QuizType::factory()->create([
            'label' => 'Multiple Choice',
            'value' => 'multiple',
        ]);
    }
}
