<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\QuizType;
use Illuminate\Console\Command;
use function Laravel\Prompts\{info, spin, note};

class QuizStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:quiz-start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch the quiz in the terminal';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        note('Welcome to the Quiz Central!');
        info('Please select the following options:');

        $category = Category::all();
        $difficulty = Difficulty::all();
        $quizType = QuizType::all();

        //Build quiz options form
    }
}
