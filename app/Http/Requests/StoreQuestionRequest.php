<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'statement' => 'required|string',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_option' => 'required|in:a,b,c,d',
            'order' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'statement.required' => 'O enunciado da questão é obrigatório.',
            'option_a.required' => 'A alternativa A é obrigatória.',
            'option_a.max' => 'A alternativa A não pode ter mais de 500 caracteres.',
            'option_b.required' => 'A alternativa B é obrigatória.',
            'option_b.max' => 'A alternativa B não pode ter mais de 500 caracteres.',
            'option_c.required' => 'A alternativa C é obrigatória.',
            'option_c.max' => 'A alternativa C não pode ter mais de 500 caracteres.',
            'option_d.required' => 'A alternativa D é obrigatória.',
            'option_d.max' => 'A alternativa D não pode ter mais de 500 caracteres.',
            'correct_option.required' => 'A alternativa correta é obrigatória.',
            'correct_option.in' => 'A alternativa correta deve ser a, b, c ou d.',
            'order.min' => 'A ordem deve ser pelo menos 1.',
        ];
    }
}

