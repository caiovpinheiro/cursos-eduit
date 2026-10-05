<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMaterialActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'content_richtext',
    ];

    /**
     * Get the activity that owns this support material
     */
    public function activity()
    {
        return $this->morphOne(Activity::class, 'activityable');
    }
}

