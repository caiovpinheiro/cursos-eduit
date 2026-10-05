<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'cover_image_url' => $this->cover_image_url,
            'workload' => $this->workload,
            'modules_count' => $this->modules_count,
            'difficulty_level' => $this->difficulty_level,
            'difficulty_level_label' => $this->getDifficultyLevelLabel(),
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'discount_percentage' => $this->discount_percentage,
            'final_price' => $this->getFinalPrice(),
            'discount_amount' => $this->getDiscountAmount(),
            'is_free' => $this->isFree(),
            'has_discount' => $this->hasDiscount(),
            'modules' => $this->whenLoaded('activities', function () {
                return $this->activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'order' => $activity->order,
                        'type' => $this->getActivityTypeName($activity->activityable_type),
                        'duration' => $this->getActivityDuration($activity),
                    ];
                });
            }),
            'total_duration' => $this->total_duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get activity type name from activityable_type
     */
    private function getActivityTypeName(string $activityableType): string
    {
        $types = [
            'App\\Models\\VideoActivity' => 'video',
            'App\\Models\\ArticleActivity' => 'article',
            'App\\Models\\QuizActivity' => 'quiz',
            'App\\Models\\MiniGameActivity' => 'mini_game',
        ];

        return $types[$activityableType] ?? 'unknown';
    }

    /**
     * Get activity duration based on type
     */
    private function getActivityDuration($activity): int
    {
        if ($activity->activityable_type === 'App\\Models\\VideoActivity') {
            // Carregar o relacionamento se não estiver carregado
            if (!$activity->relationLoaded('activityable')) {
                $activity->load('activityable');
            }
            return $activity->activityable ? (int) ($activity->activityable->duration ?? 0) : 0;
        }

        // Para outros tipos, retornar duração estimada baseada no tipo
        $estimatedDurations = [
            'App\\Models\\ArticleActivity' => 10, // 10 minutos para ler artigo
            'App\\Models\\QuizActivity' => 5,     // 5 minutos para quiz
            'App\\Models\\MiniGameActivity' => 15, // 15 minutos para mini-game
        ];

        return $estimatedDurations[$activity->activityable_type] ?? 0;
    }
}
