<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'questionable_id',
        'questionable_type',
        'attempt_number',
        'answers',
        'total_questions',
        'correct_answers',
        'score',
        'passed',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'score' => 'decimal:2',
        'passed' => 'boolean',
        'submitted_at' => 'datetime',
        'attempt_number' => 'integer',
    ];

    /**
     * Get the user that owns the attempt
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the questionable entity (QuizActivity or FinalExamActivity)
     */
    public function questionable()
    {
        return $this->morphTo();
    }

    /**
     * Check if this attempt passed
     */
    public function hasPassed(): bool
    {
        return $this->passed;
    }

    /**
     * Get score percentage formatted
     */
    public function getScorePercentageAttribute(): string
    {
        return number_format($this->score, 2) . '%';
    }

    /**
     * Get summary of attempt
     */
    public function getSummaryAttribute(): string
    {
        return "{$this->correct_answers}/{$this->total_questions} corretas ({$this->score_percentage})";
    }
}

