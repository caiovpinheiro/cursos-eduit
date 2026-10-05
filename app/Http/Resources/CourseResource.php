<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'is_active' => $this->is_active,
            'activities_count' => $this->whenLoaded('activities', function () {
                return $this->activities->count();
            }),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'enrollment' => $this->when($request->user() && $request->user()->isStudent(), function () {
                return $this->pivot ? [
                    'progress' => $this->pivot->progress,
                    'completed_at' => $this->pivot->completed_at,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}