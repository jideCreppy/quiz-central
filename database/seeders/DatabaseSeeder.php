<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Add more categories as needed. See https://opentdb.com/api_config.php for more values that can be added to the database.

        $this->call([
            CategorySeeder::class,
            DifficultySeeder::class,
            QuizTypeSeeder::class,
        ]);
    }
}
