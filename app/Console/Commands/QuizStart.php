<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\TriviaCommands;
use App\Services\TriviaQuizService;
use Illuminate\Console\Command;

class QuizStart extends Command
{
    use TriviaCommands;

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
    protected $description = 'Launch Quiz Central in the terminal';

    /**
     * Execute the console command.
     */
    public function handle(TriviaQuizService $quiz): void
    {
        $quiz->launch();
    }
}
