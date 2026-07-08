<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Notifications\Notification;

class QuizCreatedNotification extends Notification
{
    public function __construct(protected Quiz $quiz) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $quiz = $this->quiz->loadMissing(['subject', 'program']);

        // A quiz targets either a subject (diploma) or a program (course/english).
        $container = $quiz->isProgramQuiz() ? $quiz->program : $quiz->subject;
        $containerName = $container?->name_ar ?? $container?->name ?? '';

        $actionUrl = $quiz->isProgramQuiz()
            ? route('student.quizzes.program.show', [$quiz->program_id, $quiz->id])
            : route('student.quizzes.show', [$quiz->subject_id, $quiz->id]);

        $typeLabel = match($quiz->type) {
            'exam'     => 'امتحان',
            'homework' => 'واجب',
            default    => 'اختبار قصير',
        };

        $dateStr = $quiz->starts_at
            ? ' — موعد البدء: ' . $quiz->starts_at->format('Y/m/d H:i')
            : '';

        $body = "تم إضافة {$typeLabel} جديد «{$quiz->title_ar}» في " .
                $containerName .
                "{$dateStr}. الدرجة الكاملة: {$quiz->total_marks}";

        return [
            'notification_type' => 'quiz_created',
            'quiz_id'           => $quiz->id,
            'quiz_title'        => $quiz->title_ar,
            'quiz_type'         => $quiz->type,
            'subject_id'        => $quiz->subject_id,
            'program_id'        => $quiz->program_id,
            'subject_name'      => $containerName,
            'total_marks'       => $quiz->total_marks,
            'starts_at'         => $quiz->starts_at?->toIso8601String(),
            'ends_at'           => $quiz->ends_at?->toIso8601String(),
            'title'             => "تم إضافة {$typeLabel} جديد",
            'body'              => $body,
            'message_ar'        => $body,
            'icon'              => 'academic-cap',
            'action_url'        => $actionUrl,
        ];
    }
}
