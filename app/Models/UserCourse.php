<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    protected $table = 'user_courses';

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'progress' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the enrollment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this enrollment
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the course is completed
     */
    public function isCompleted(): bool
    {
        return $this->progress >= 100 && !is_null($this->completed_at);
    }

    /**
     * Mark course as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'progress' => 100,
            'completed_at' => now(),
        ]);

        // Dispatch event for certificate generation
        event(new \App\Events\CourseCompleted($this->user, $this->course, $this));
    }

    /**
     * Update progress percentage
     */
    public function updateProgress(int $progress): void
    {
        $this->update([
            'progress' => min(100, max(0, $progress)),
        ]);

        // Auto-complete if progress reaches 100%
        if ($this->progress >= 100 && is_null($this->completed_at)) {
            $this->markAsCompleted();
        }
    }

    /**
     * Ensure certificate is generated if course is completed
     */
    public function ensureCertificate(): void
    {
        if (!$this->isCompleted()) {
            return;
        }

        // Check if certificate already exists
        $existingCertificate = \App\Models\Certificate::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->first();

        if (!$existingCertificate) {
            // Reload relationships if needed
            if (!$this->relationLoaded('user')) {
                $this->load('user');
            }
            if (!$this->relationLoaded('course')) {
                $this->load('course');
            }

            if ($this->user && $this->course) {
                event(new \App\Events\CourseCompleted($this->user, $this->course, $this));
            }
        }
    }
}
