<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalExamActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'max_attempts',
        'duration_minutes',
        'passing_score',
    ];

    protected $casts = [
        'max_attempts' => 'integer',
        'duration_minutes' => 'integer',
        'passing_score' => 'decimal:2',
    ];

    /**
     * Get the activity that owns this final exam
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }

    /**
     * Get the questions for this final exam
     */
    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable')->orderBy('order');
    }

    /**
     * Get the attempts for this final exam
     */
    public function attempts()
    {
        return $this->morphMany(QuizAttempt::class, 'questionable')->orderBy('submitted_at', 'desc');
    }
}

