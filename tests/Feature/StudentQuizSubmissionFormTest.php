<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentQuizSubmissionFormTest extends TestCase
{
    public static function quizForms(): array
    {
        return [
            'subject quiz' => ['student/quizzes/take.blade.php', 'subjects'],
            'program quiz' => ['student/quizzes/program/take.blade.php', 'programs'],
        ];
    }

    #[DataProvider('quizForms')]
    public function test_submission_stays_on_the_current_origin(string $view, string $scope): void
    {
        // A proxy or canonical URL setting must not send answers to another origin.
        URL::forceRootUrl('http://other-host.example');
        URL::forceScheme('http');

        try {
            $source = file_get_contents(resource_path('views/'.$view));
            $this->assertSame(1, preg_match('/<form id="quizForm"[^\r\n]+>/', $source, $matches));
            $form = Blade::render($matches[0], [
                'subject' => (object) ['id' => 317],
                'program' => (object) ['id' => 317],
                'quiz' => (object) ['id' => 71],
            ]);

            $this->assertStringContainsString('method="POST"', $form);
            $this->assertStringContainsString('action="/student/'.$scope.'/317/quizzes/71/submit"', $form);
        } finally {
            URL::forceRootUrl(null);
            URL::forceScheme(null);
        }
    }
}
