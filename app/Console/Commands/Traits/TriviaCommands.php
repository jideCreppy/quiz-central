<?php

namespace App\Console\Commands\Traits;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\QuizType;

use function Laravel\Prompts\progress;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

trait TriviaCommands
{
    protected function manageProgress(): void
    {
        $this->progressBar = progress('Setting up your quiz..', steps: 4, hint: "Let's set up your quiz.");
    }

    public function getLimit(): int
    {
        $limit = (int) text(
            label: 'How many questions would you like to answer?',
            placeholder: 'e.g. 10',
            default: '5',
            required: 'A quiz limit is required.',
            validate: function ($amount) {
                return (is_numeric($amount) && ($amount > 0 && $amount < 11))
                    ? null
                    : 'You need to set a limit between 1-10.';
            },
            hint: 'Please enter a number between 1 and 10.'
        );

        $this->progressBar->advance();

        return $limit;
    }

    public function getCategory(): int
    {
        $category = (int) select(
            label: 'Select a category:',
            options: Category::all()->pluck('label', 'value'),
            default: Category::first()->value('label'),
            hint: 'Please select a category.',
            required: 'Quiz category is required.'
        );

        $this->progressBar->advance();

        return $category;
    }

    public function getDifficultyLevel(): string
    {
        $difficultyLevel = select(
            label: 'Select a difficulty:',
            options: Difficulty::all()->pluck('label', 'value'),
            default: Difficulty::first()->value('label'),
            hint: 'Please select a difficulty level.',
            required: 'Quiz difficulty level is required.'
        );

        $this->progressBar->advance();

        return $difficultyLevel;
    }

    public function getQuizType(): string
    {
        $type = select(
            label: 'Select the type of answer for the quiz:',
            options: QuizType::all()->pluck('label', 'value'),
            default: QuizType::first()->value('label'),
            hint: 'Please select your quiz answer type.',
            required: 'Quiz answer type is required.'
        );

        $this->progressBar->advance();

        return $type;
    }

    public function buildTriviaEndpoint(int $limit, int $category, string $difficultyLevel, string $quizType): string
    {
        return 'https://opentdb.com/api.php?'.'amount='.$limit.'&category='.$category.'&difficulty='.$difficultyLevel.'&type='.$quizType;
    }
}
