<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Activity;
use App\Models\FinalExamActivity;

class StoreFinalExamActivityRequest extends FormRequest
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
            'course_id' => [
                'required',
                'exists:courses,id',
                function ($attribute, $value, $fail) {
                    // Check if course already has a final exam
                    $existingFinalExam = Activity::where('course_id', $value)
                        ->where('activityable_type', FinalExamActivity::class)
                        ->exists();
                    
                    if ($existingFinalExam) {
                        $fail('Este curso já possui uma Prova Final. Não é possível cadastrar mais de uma por curso.');
                    }
                },
            ],
            'type' => 'required|in:final_exam',
            'title' => 'required|string|max:255',
            'final_exam.description' => 'nullable|string',
            'final_exam.max_attempts' => 'required|integer|min:1|max:3',
            'final_exam.duration_minutes' => 'required|integer|min:1',
            'final_exam.passing_score' => 'nullable|numeric|min:0|max:100',
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
            'type.in' => 'O tipo deve ser "final_exam".',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'final_exam.max_attempts.required' => 'O número máximo de tentativas é obrigatório.',
            'final_exam.max_attempts.min' => 'O número mínimo de tentativas é 1.',
            'final_exam.max_attempts.max' => 'O número máximo de tentativas é 3.',
            'final_exam.duration_minutes.required' => 'A duração é obrigatória.',
            'final_exam.duration_minutes.min' => 'A duração deve ser pelo menos 1 minuto.',
            'final_exam.passing_score.numeric' => 'A nota de corte deve ser um número.',
            'final_exam.passing_score.min' => 'A nota de corte deve ser no mínimo 0.',
            'final_exam.passing_score.max' => 'A nota de corte deve ser no máximo 100.',
        ];
    }
}

