<?php

namespace App\Notifications;

use App\Models\QuizAttempt;
use Illuminate\Notifications\Notification;

class QuizResultReleasedNotification extends Notification
{
    public function __construct(protected QuizAttempt $attempt) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $attempt = $this->attempt->loadMissing(['quiz.subject', 'quiz.program']);
        $quiz    = $attempt->quiz;

        // The result lives under the subject or the program flow.
        $actionUrl = $quiz->isProgramQuiz()
            ? route('student.quizzes.program.result', [$quiz->program_id, $quiz->id, $attempt->id])
            : route('student.quizzes.result', [$quiz->subject_id, $quiz->id, $attempt->id]);

        $body = "تم اعتماد نتيجة اختبار «{$quiz->title_ar}». يمكنك الآن الاطلاع على درجتك وإجاباتك.";

        return [
            'notification_type' => 'quiz_result_released',
            'quiz_id'           => $quiz->id,
            'attempt_id'        => $attempt->id,
            'quiz_title'        => $quiz->title_ar,
            'score'             => $attempt->score,
            'title'             => 'ظهرت نتيجة اختبارك',
            'body'              => $body,
            'message_ar'        => $body,
            'icon'              => 'academic-cap',
            'action_url'        => $actionUrl,
        ];
    }
}
