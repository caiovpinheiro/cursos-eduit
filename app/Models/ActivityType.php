<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the activities for this type
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the specific activity models
     */
    public function videoActivities()
    {
        return $this->hasMany(VideoActivity::class);
    }

    public function articleActivities()
    {
        return $this->hasMany(ArticleActivity::class);
    }

    public function quizActivities()
    {
        return $this->hasMany(QuizActivity::class);
    }

    public function miniGameActivities()
    {
        return $this->hasMany(MiniGameActivity::class);
    }
}
