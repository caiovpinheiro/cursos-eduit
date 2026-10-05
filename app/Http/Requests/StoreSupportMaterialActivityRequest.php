<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportMaterialActivityRequest extends FormRequest
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
            'type' => 'required|in:support_material',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
            'support_material.description' => 'nullable|string',
            'support_material.content_richtext' => 'required|string',
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
            'type.in' => 'O tipo deve ser "support_material".',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'order.min' => 'A ordem deve ser pelo menos 1.',
            'support_material.content_richtext.required' => 'O conteúdo do material de apoio é obrigatório.',
        ];
    }
}

