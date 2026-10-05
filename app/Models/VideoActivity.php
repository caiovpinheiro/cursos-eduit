<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'transcript',
        'link',
        'duration',
    ];

    protected $casts = [
        'duration' => 'integer', // Duration in seconds
    ];

    /**
     * Get the activity that owns this video
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }
}
