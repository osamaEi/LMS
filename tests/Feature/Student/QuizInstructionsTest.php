<?php

namespace Tests\Feature\Student;

use App\Models\Quiz;
use Tests\TestCase;

class QuizInstructionsTest extends TestCase
{
    public function test_final_instructions_preserve_lines_and_escape_markup(): void
    {
        $quiz = new Quiz(['type' => 'final', 'description_ar' => "Read carefully\n<script>alert(1)</script>"]);
        $html = view('student.quizzes.partials.instructions', compact('quiz'))->render();
        $this->assertStringContainsString('تعليمات الاختبار النهائي', $html);
        $this->assertStringContainsString('white-space: pre-wrap', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
}
