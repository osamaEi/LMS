<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'marks_obtained',
        'teacher_feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2',
    ];

    /**
     * Auto-grade the answer if possible
     */
    public function autoGrade()
    {
        $question = $this->question;

        // Only auto-grade multiple choice and true/false questions
        if (!in_array($question->type, ['multiple_choice', 'true_false'])) {
            return $this;
        }

        if ($this->selected_option_id) {
            $isCorrect = $question->isOptionCorrect($this->selected_option_id);
            $this->is_correct = $isCorrect;
            $this->marks_obtained = $isCorrect ? $question->marks : 0;
        } else {
            $this->is_correct = false;
            $this->marks_obtained = 0;
        }

        $this->save();

        return $this;
    }

    /**
     * Manual grade for essay/short answer questions
     */
    public function manualGrade($marks, $feedback = null)
    {
        $this->marks_obtained = min($marks, $this->question->marks);
        $this->is_correct = $marks >= ($this->question->marks * 0.5); // 50% or more is considered correct
        $this->teacher_feedback = $feedback;
        $this->save();

        // Recalculate attempt score
        $this->attempt->calculateScore();

        return $this;
    }

    // Relationships

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
