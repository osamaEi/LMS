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
     * English-only options leave option_ar empty; fall back to the English
     * text so every view reading option_ar still shows something.
     */
    public function getOptionArAttribute($value)
    {
        return filled($value) ? $value : ($this->attributes['option_en'] ?? $value);
    }

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
