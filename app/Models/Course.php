<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'long_description',
        'cover_image_url',
        'workload',
        'modules_count',
        'difficulty_level',
        'category',
        'price',
        'promotional_price',
        'discount_percentage',
        'is_active',
        'total_duration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'workload' => 'integer',
        'modules_count' => 'integer',
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'total_duration' => 'integer',
    ];

    /**
     * Get the activities for the course
     */
    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('order');
    }

    /**
     * Get the users enrolled in the course
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')
            ->withPivot(['progress', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the certificates for the course
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Scope for active courses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get difficulty level options
     */
    public static function getDifficultyLevelOptions(): array
    {
        return [
            'iniciante' => 'Iniciante',
            'intermediario' => 'Intermediário',
            'avancado' => 'Avançado',
        ];
    }

    /**
     * Get difficulty level label
     */
    public function getDifficultyLevelLabel(): string
    {
        return self::getDifficultyLevelOptions()[$this->difficulty_level] ?? 'Iniciante';
    }

    /**
     * Get category options
     */
    public static function getCategoryOptions(): array
    {
        return [
            'programacao' => 'Programação',
            'banco_dados' => 'Banco de Dados',
            'produtividade' => 'Produtividade',
            'lideranca' => 'Liderança',
            'marketing' => 'Marketing',
            'design' => 'Design',
            'negocios' => 'Negócios',
            'tecnologia' => 'Tecnologia',
            'outros' => 'Outros',
        ];
    }

    /**
     * Get category label
     */
    public function getCategoryLabel(): ?string
    {
        return $this->category ? self::getCategoryOptions()[$this->category] : null;
    }

    /**
     * Check if course is free
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Check if course has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_percentage > 0 && $this->promotional_price !== null;
    }

    /**
     * Get final price (promotional if available, otherwise regular price)
     */
    public function getFinalPrice(): float
    {
        if ($this->hasDiscount()) {
            return (float) $this->promotional_price;
        }
        
        return (float) ($this->price ?? 0);
    }

    /**
     * Formatted total content duration for listings (minutes), e.g. "51m" or "1h 30m".
     * Falls back to workload in hours when duration is not calculated.
     */
    public function getFormattedTotalDurationLabel(): string
    {
        $min = (int) ($this->total_duration ?? 0);
        if ($min > 0) {
            if ($min < 60) {
                return $min.'m';
            }
            $h = intdiv($min, 60);
            $rest = $min % 60;

            return $rest > 0 ? $h.'h '.$rest.'m' : $h.'h';
        }

        $w = (int) ($this->workload ?? 0);

        return $w > 0 ? $w.'h' : '—';
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmount(): float
    {
        if ($this->hasDiscount()) {
            return (float) ($this->price ?? 0) - (float) $this->promotional_price;
        }
        
        return 0.0;
    }
}
