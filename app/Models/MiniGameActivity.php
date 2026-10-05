<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGameActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_type',
        'config', // JSON field for game configuration
        'max_score',
    ];

    protected $casts = [
        'config' => 'array',
        'max_score' => 'integer',
    ];

    /**
     * Get the activity that owns this mini game
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }
}
