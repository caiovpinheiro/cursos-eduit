<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'content_richtext',
    ];

    /**
     * Get the activity that owns this article
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }
}
