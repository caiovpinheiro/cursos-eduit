<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'activity_type_id',
        'title',
        'order',
        'activityable_id',
        'activityable_type',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the course that owns the activity
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the activity type
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * Get the polymorphic activity model
     */
    public function activityable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for ordering activities
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the user completions for this activity
     */
    public function userActivities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the users who completed this activity
     */
    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_activities')
            ->withPivot(['completed', 'completed_at'])
            ->withTimestamps();
    }
}
