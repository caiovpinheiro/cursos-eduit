<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionable_id',
        'questionable_type',
        'statement',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the parent questionable model (Quiz or FinalExam)
     */
    public function questionable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for ordering questions
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}

