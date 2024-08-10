<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\TriviaCommands;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

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

    public function __construct()
    {
        parent::__construct();
        $this->displayIntro();
        $this->manageProgress();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->begin();
        $this->checkReplay();
    }

    public function displayIntro(): void
    {
        note('🧑‍💻 Welcome to Quiz Central! 👩‍💻');
        note("Let's set up your quiz:");
    }

    public function begin(): void
    {
        $limit = $this->getLimit();
        $category = $this->getCategory();
        $difficulty = $this->getDifficultyLevel();
        $quizType = $this->getQuizType();
        $triviaResponse = $this->fetchQuiz(
            $limit,
            $category,
            $difficulty,
            $quizType
        );
        $this->checkSuccessfulApiCall($triviaResponse);
        $triviaResponse = $triviaResponse['results'];

        $answers = [];
        $result = [];
        $quizForm = form();
        $incorrectAnswers = 0;
;
        foreach ($triviaResponse as $question) {
            $options = ['True', 'False'];

            $questionKey = $question['question'];

            if ($quizType == 'boolean') {
                $answers[$questionKey]['question'] = htmlspecialchars_decode($questionKey);
                $answers[$questionKey]['correct'] = $question['correct_answer'];
                $answers[$questionKey]['incorrect'] = $question['incorrect_answers'];
            } else if ($quizType == 'multiple') {
                $options = array_merge($question['incorrect_answers'], [$question['correct_answer']]);
                $options = collect($options)->map(fn ($option) => htmlspecialchars_decode($option))->unique()->toArray();

                $answers[$questionKey]['question'] = htmlspecialchars_decode($questionKey);
                $answers[$questionKey]['correct'] = $question['correct_answer'];
                $answers[$questionKey]['incorrect'] = $question['incorrect_answers'];
            }

            $quizForm->select(
                label: html_entity_decode($question['question']),
                options: $options,
                name: $question['question']
            );
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

        $this->displaySummary($difficulty, $category, $quizType, $limit);
    }

    public function fetchQuiz(int $limit, int $category, string $difficultyLevel, string $quizType): array|bool
    {
        return spin(function () use ($limit, $category, $difficultyLevel, $quizType) {

            try {
                $response = Http::get($this->buildTriviaEndpoint($limit, $category, $difficultyLevel, $quizType));

                if ($response->failed() || $response->serverError() || $response->clientError()) {
                    return false;
                }

                return $response->json();

            } catch (ConnectionException $exception) {
                return false;
            }

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

    public function checkReplay(): void
    {
        if (confirm(label: 'Would you like to play again?', default: 'yes', yes: 'Yes', no: 'No')) {
            $this->handle();
        } else {
            info('Thanks for playing. Goodbye 👋');
        }
    }
}
