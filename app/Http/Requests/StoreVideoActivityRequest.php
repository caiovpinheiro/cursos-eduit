<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:video',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
            'video.description' => 'nullable|string',
            'video.transcript' => 'nullable|string',
            'video.link' => 'required|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'O ID do curso é obrigatório.',
            'course_id.exists' => 'O curso selecionado não existe.',
            'type.required' => 'O tipo de atividade é obrigatório.',
            'type.in' => 'O tipo deve ser "video".',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'order.min' => 'A ordem deve ser pelo menos 1.',
            'video.link.required' => 'O link do vídeo é obrigatório.',
            'video.link.url' => 'O link deve ser uma URL válida.',
            'video.link.max' => 'O link não pode ter mais de 500 caracteres.',
        ];
    }
}

