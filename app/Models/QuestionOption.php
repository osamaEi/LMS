<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_ar',
        'option_en',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the option text based on current locale
     */
    public function getOptionAttribute()
    {
        return app()->getLocale() === 'en' && $this->option_en ? $this->option_en : $this->option_ar;
    }

    // Relationships

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
