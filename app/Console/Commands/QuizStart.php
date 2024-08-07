<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\QuizType;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\{form, info, note, outro, pause, progress, select, spin, table, text, warning};

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
    protected $description = 'Launch Quiz Central in the terminal';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $triviaUrl = 'https://opentdb.com/api.php?';

        note('🧑‍💻Welcome to Quiz Central!👩‍💻');
        pause('Press enter to continue...');
        info('Please select the following options:');

        $progressBar = progress('Initializing..', steps: 5, hint: "Let's set up your quiz.");

        $progressBar->advance();

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

        $triviaUrl .= 'amount=' . $limit;

        $progressBar->advance();

        $category = select(
            label: 'Select a category:',
            options: Category::all()->pluck('label', 'value'),
            default: Category::first()->value('label'),
            hint: 'Please select a category.',
            required: 'Quiz category is required.'
        );

        $triviaUrl .= '&category=' . $category;

        $progressBar->advance();

        $difficultyLevel = select(
            label: 'Select a difficulty:',
            options: Difficulty::all()->pluck('label', 'value'),
            default: Difficulty::first()->value('label'),
            hint: 'Please select a difficulty level.',
            required: 'Quiz difficulty level is required.'
        );

        $triviaUrl .= '&difficulty=' . $difficultyLevel;

        $progressBar->advance();

        $quizType = select(
            label: 'Select the type of answer for the quiz:',
            options: QuizType::all()->pluck('label', 'value'),
            default: QuizType::first()->value('label'),
            hint: 'Please select your quiz answer type.',
            required: 'Quiz answer type is required.'
        );

        $triviaUrl .= '&type=' . $quizType;

        $progressBar->advance();

        $triviaResponse = spin(fn () => Http::get($triviaUrl)->json(), 'Fetching Questions...');

        if($triviaResponse['response_code'] === 0 && count($triviaResponse['results']) > 0) {
            $triviaResponse = $triviaResponse['results'];
        } else {
            outro("Sorry! Something went wrong. Please try again.🙏");
        }

        $quizForm = form();
        $answers = [];

        foreach ($triviaResponse as $question) {

            if ($quizType == 'boolean') {
                $answers[$question['question']]['question'] = htmlspecialchars_decode($question['question']);
                $answers[$question['question']]['correct'] = $question['correct_answer'];
                $answers[$question['question']]['incorrect'] = $question['incorrect_answers'];

                $quizForm->select(label: htmlspecialchars_decode($question['question']), options: ['True', 'False'], name: $question['question']);
            }
        }

        $quizForm = $quizForm->submit();

        $result = [];
        $incorrectAnswers = 0;

        foreach ($answers as $question => $answer) {
            if ($quizForm[$question] != $answer['correct']) {
                $incorrectAnswers++;
            }
            $result[] = [html_entity_decode($question), $quizForm[$question], $answer['correct']];
        }

        table(
            headers: ['Quiz', 'Your Answer', 'Correct Answer'],
            rows: $result,
        );

        if ($incorrectAnswers) {
            warning("😔 You got {$incorrectAnswers} incorrect answer(s).🙏");
        }

        $difficultyLevel = ucfirst($difficultyLevel);
        $category = Category::where('value', $category)->first()->label;
        $quizType = ucfirst($quizType);

        outro("🚀 Your quiz settings: Quiz Limit: {$limit}, Difficulty Level: {$difficultyLevel}, Category: {$category} and Answer Type: {$quizType} 🚀");
    }
}
