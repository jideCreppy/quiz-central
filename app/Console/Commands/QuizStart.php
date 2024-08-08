<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\QuizType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use Laravel\Prompts\Progress;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

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


    protected array|Progress $progressBar;

    public function __construct()
    {
        $this->progressBar = progress('Setting up your quiz..', steps: 4, hint: "Let's set up your quiz.");

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->intro();

        [$difficulty, $category, $quizType, $limit] = $this->begin();

        $this->displaySummary($difficulty, $category, $quizType, $limit);

        $this->checkReplay();
    }

    public function begin(): array
    {
        $limit = $this->getLimit();

        $category = $this->getCategory();

        $difficulty = $this->getDifficultyLevel();

        $quizType = $this->getQuizType();

        $triviaResponse = $this->fetchQuiz($limit, $category, $difficulty, $quizType);

        $this->checkSuccessfulApiCall($triviaResponse);

        $triviaResponse = $triviaResponse['results'];

        $answers = [];
        $result = [];
        $quizForm = form();
        $incorrectAnswers = 0;

        foreach ($triviaResponse as $question) {
            if ($quizType == 'boolean') {
                $answers[$question['question']]['question'] = htmlspecialchars_decode($question['question']);
                $answers[$question['question']]['correct'] = $question['correct_answer'];
                $answers[$question['question']]['incorrect'] = $question['incorrect_answers'];

                $quizForm->select(label: html_entity_decode($question['question']), options: ['True', 'False'],
                    name: $question['question']);
            }
        }

        $quizForm = $quizForm->submit();

        foreach ($answers as $question => $answer) {
            if ($quizForm[$question] != $answer['correct']) {
                $incorrectAnswers += 1;
            }
            $result[] = [html_entity_decode($question), $quizForm[$question], $answer['correct']];
        }

        $this->getResultStats($result);

        if ($incorrectAnswers) {
            warning("😔 You got {$incorrectAnswers} incorrect answer(s) out of {$limit} 🙏");
        } else {
            info('🎉 You got all the answers correct! Thank you for taking the quiz! 🎉');
        }

        return [$difficulty, $category, $quizType, $limit];
    }

    public function buildTriviaEndpoint(int $limit, int $category, string $difficultyLevel, string $quizType): string
    {
        return 'https://opentdb.com/api.php?'.'amount='.$limit.'&category='.$category.'&difficulty='.$difficultyLevel.'&type='.$quizType;
    }

    public function getLimit(): int
    {
        $this->progressBar->advance();

        return (int) text(
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
    }

    public function getCategory(): int
    {
        $this->progressBar->advance();

        return (int) select(
            label: 'Select a category:',
            options: Category::all()->pluck('label', 'value'),
            default: Category::first()->value('label'),
            hint: 'Please select a category.',
            required: 'Quiz category is required.'
        );
    }

    public function getDifficultyLevel(): string
    {
        $this->progressBar->advance();

        return select(
            label: 'Select a difficulty:',
            options: Difficulty::all()->pluck('label', 'value'),
            default: Difficulty::first()->value('label'),
            hint: 'Please select a difficulty level.',
            required: 'Quiz difficulty level is required.'
        );
    }

    public function getQuizType(): string
    {
        $this->progressBar->advance();

        return select(
            label: 'Select the type of answer for the quiz:',
            options: QuizType::all()->pluck('label', 'value'),
            default: QuizType::first()->value('label'),
            hint: 'Please select your quiz answer type.',
            required: 'Quiz answer type is required.'
        );
    }

    public function fetchQuiz(int $limit, int $category, string $difficultyLevel, string $quizType): array|bool
    {
        return spin(function () use ($limit, $category, $difficultyLevel, $quizType) {
            $response = Http::get($this->buildTriviaEndpoint($limit, $category, $difficultyLevel, $quizType));

            if ($response->failed()) {
                return false;
            }

            return $response->json();

        }, 'Fetching Questions...');
    }

    public function checkSuccessfulApiCall(mixed $triviaResponse): void
    {
        if (! $triviaResponse || $triviaResponse['response_code'] != 0 || count($triviaResponse['results']) <= 0) {
            error('Sorry! Something went wrong. Please try again.🙏');
            exit;
        }
    }

    public function getResultStats(array $result): void
    {
        table(
            headers: ['Quiz', 'Your Answer', 'Correct Answer'],
            rows: $result,
        );
    }

    public function displaySummary(string $difficultyLevel, int $category, string $quizType, int $limit): void
    {
        $difficultyLevel = ucfirst($difficultyLevel);
        $category = Category::where('value', $category)->first()->label;
        $quizType = ucfirst($quizType);

        outro("🚀 Your quiz settings: Quiz Limit: {$limit}, Difficulty Level: {$difficultyLevel}, Category: {$category} and Answer Type: {$quizType} 🚀");
    }

    public function intro(): void
    {
        note('🧑‍💻 Welcome to Quiz Central! 👩‍💻');
        note("Let's set up your quiz:");
    }

    /**
     * @return void
     */
    public function checkReplay(): void
    {
        if (confirm(label: 'Would you like to play again?', default: 'yes', yes: 'Yes', no: 'No')) {
            $this->handle();
        } else {
            info('Thanks for playing. Goodbye 👋');
        }
    }
}
