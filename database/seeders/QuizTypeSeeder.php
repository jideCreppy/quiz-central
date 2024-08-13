<?php

namespace Database\Seeders;

use App\Models\QuizType;
use Illuminate\Database\Seeder;

class QuizTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
