<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        
        $data = [
            'id' => $this->id,
            'statement' => $this->statement,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Only admins can see the correct answer
        if ($user && $user->isAdmin()) {
            $data['correct_option'] = $this->correct_option;
            $data['questionable_id'] = $this->questionable_id;
            $data['questionable_type'] = $this->questionable_type;
        }

        return $data;
    }
}

