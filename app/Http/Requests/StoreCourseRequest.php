<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
        $coverMimes = (string) config('course.cover_upload.mimes', 'jpg,jpeg,png,webp');
        $coverMaxKb = (int) config('course.cover_upload.max_kb', 2048);

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'cover_image' => "required|image|mimes:{$coverMimes}|max:{$coverMaxKb}",
            'workload' => 'required|integer|min:1',
            'modules_count' => 'nullable|integer|min:0',
            'difficulty_level' => 'nullable|in:iniciante,intermediario,avancado',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
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
            'title.required' => 'O título do curso é obrigatório.',
            'title.max' => 'O título do curso não pode ter mais de 255 caracteres.',
            'description.required' => 'A descrição do curso é obrigatória.',
            'short_description.max' => 'A descrição curta não pode ter mais de 500 caracteres.',
            'cover_image.required' => 'A imagem de capa é obrigatória.',
            'cover_image.image' => 'O arquivo de capa deve ser uma imagem válida.',
            'cover_image.mimes' => 'A imagem de capa deve estar em um formato permitido.',
            'cover_image.max' => 'A imagem de capa excede o tamanho máximo permitido.',
            'workload.required' => 'A carga horária é obrigatória.',
            'workload.min' => 'A carga horária deve ser pelo menos 1 hora.',
            'modules_count.min' => 'A quantidade de módulos deve ser pelo menos 0.',
            'difficulty_level.in' => 'O nível de dificuldade deve ser: iniciante, intermediário ou avançado.',
            'category.max' => 'A categoria não pode ter mais de 100 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.min' => 'O preço deve ser maior ou igual a 0.',
            'promotional_price.min' => 'O preço promocional deve ser maior ou igual a 0.',
            'discount_percentage.min' => 'O desconto deve ser maior ou igual a 0%.',
            'discount_percentage.max' => 'O desconto não pode ser maior que 100%.',
        ];
    }
}