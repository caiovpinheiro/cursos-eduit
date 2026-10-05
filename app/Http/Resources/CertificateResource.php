<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'issue_date' => $this->issue_date,
            'hash_validation' => $this->hash_validation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'final_exam_score' => $this->final_exam_score,
            'course' => $this->when($this->relationLoaded('course'), function () {
                return [
                    'id' => $this->course->id,
                    'title' => $this->course->title,
                    'total_duration' => $this->course->total_duration,
                    'modules_count' => $this->course->modules_count,
                    'difficulty_level' => $this->course->difficulty_level,
                    'category' => $this->course->category,
                ];
            }),
        ];
    }
}