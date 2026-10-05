<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'activity_type_id' => 'required|exists:activity_types,id',
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'activityable_id' => 'required|integer',
            'activityable_type' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'O ID do curso é obrigatório.',
            'course_id.exists' => 'O curso selecionado não existe.',
            'activity_type_id.required' => 'O tipo de atividade é obrigatório.',
            'activity_type_id.exists' => 'O tipo de atividade selecionado não existe.',
            'title.required' => 'O título da atividade é obrigatório.',
            'title.max' => 'O título da atividade não pode ter mais de 255 caracteres.',
            'order.required' => 'A ordem da atividade é obrigatória.',
            'order.min' => 'A ordem deve ser pelo menos 0.',
        ];
    }
}