<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\QuizAttempt;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'issue_date',
        'hash_validation',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
    ];

    protected $appends = [
        'final_exam_score',
    ];

    /**
     * Boot method to generate UUID and hash
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->uuid)) {
                $certificate->uuid = Str::uuid()->toString();
            }
            if (empty($certificate->issue_date)) {
                $certificate->issue_date = now();
            }
        });

        static::created(function ($certificate) {
            $certificate->generateValidationHash();
        });
    }

    /**
     * Get the user that owns the certificate
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this certificate
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate validation hash for the certificate
     */
    public function generateValidationHash()
    {
        $data = [
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'issue_date' => $this->issue_date->toISOString(),
        ];

        $this->hash_validation = hash('sha256', json_encode($data) . config('app.key'));
        $this->save();
    }

    /**
     * Get the final exam score attribute (accessor)
     * Since all courses have a final exam, this will always return the score
     */
    public function getFinalExamScoreAttribute(): array
    {
        // Find the final exam activity for this course
        $finalExamActivity = Activity::where('course_id', $this->course_id)
            ->where('activityable_type', 'App\\Models\\FinalExamActivity')
            ->first();

        if (!$finalExamActivity) {
            return [
                'score' => null,
                'correct_answers' => null,
                'total_questions' => null,
                'submitted_at' => null,
            ];
        }

        // Find the passed attempt for this user
        $passedAttempt = QuizAttempt::where('user_id', $this->user_id)
            ->where('questionable_id', $finalExamActivity->activityable_id)
            ->where('questionable_type', 'App\\Models\\FinalExamActivity')
            ->where('passed', true)
            ->orderBy('submitted_at', 'desc')
            ->first();

        if (!$passedAttempt) {
            return [
                'score' => null,
                'correct_answers' => null,
                'total_questions' => null,
                'submitted_at' => null,
            ];
        }

        return [
            'score' => (float) $passedAttempt->score,
            'correct_answers' => $passedAttempt->correct_answers,
            'total_questions' => $passedAttempt->total_questions,
            'submitted_at' => $passedAttempt->submitted_at,
        ];
    }
}
