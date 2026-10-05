<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrolledCourseResource extends JsonResource
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
            'cover_image_url' => $this->cover_image_url,
            'total_duration' => $this->total_duration,
            'modules_count' => $this->modules_count,
            'difficulty_level' => $this->difficulty_level,
            'difficulty_level_label' => $this->getDifficultyLevelLabel(),
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'is_free' => $this->isFree(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

