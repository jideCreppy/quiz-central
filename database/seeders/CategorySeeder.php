<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
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
    }
}
