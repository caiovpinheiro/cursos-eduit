<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'order' => $this->order,
            'activity_type' => new ActivityTypeResource($this->whenLoaded('activityType')),
            'activityable' => $this->when($this->activityable, function () {
                return [
                    'type' => class_basename($this->activityable_type),
                    'data' => $this->activityable,
                ];
            }),
            'course_id' => $this->course_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}