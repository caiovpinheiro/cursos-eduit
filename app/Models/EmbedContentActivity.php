<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmbedContentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'embed_code',
    ];

    /**
     * Get the activity that owns this embed content
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }
}

