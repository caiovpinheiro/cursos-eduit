<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuizActivity;
use App\Models\FinalExamActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    /**
     * Display questions for a quiz or final exam
     */
    public function index(Request $request, string $type, int $id): JsonResponse
    {
        $questionable = $this->getQuestionable($type, $id);
        
        if (!$questionable) {
            return response()->json([
                'message' => 'Quiz or Final Exam not found',
            ], 404);
        }
        
        $questions = $questionable->questions()->ordered()->get();
        
        return response()->json([
            'type' => $type,
            'id' => $id,
            'questions' => $questions,
        ]);
    }

    /**
     * Store one or more questions
     */
    public function store(Request $request, string $type, int $id): JsonResponse
    {
        $questionable = $this->getQuestionable($type, $id);
        
        if (!$questionable) {
            return response()->json([
                'message' => 'Quiz or Final Exam not found',
            ], 404);
        }
        
        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.statement' => 'required|string',
            'questions.*.option_a' => 'required|string|max:500',
            'questions.*.option_b' => 'required|string|max:500',
            'questions.*.option_c' => 'required|string|max:500',
            'questions.*.option_d' => 'required|string|max:500',
            'questions.*.correct_option' => 'required|in:a,b,c,d',
            'questions.*.order' => 'nullable|integer|min:1',
        ]);
        
        $createdQuestions = [];
        $currentMaxOrder = $questionable->questions()->max('order') ?? 0;
        
        foreach ($validated['questions'] as $questionData) {
            $order = $questionData['order'] ?? ++$currentMaxOrder;
        
        $question = Question::create([
            'questionable_id' => $id,
            'questionable_type' => get_class($questionable),
                'statement' => $questionData['statement'],
                'option_a' => $questionData['option_a'],
                'option_b' => $questionData['option_b'],
                'option_c' => $questionData['option_c'],
                'option_d' => $questionData['option_d'],
                'correct_option' => $questionData['correct_option'],
            'order' => $order,
        ]);
            
            $createdQuestions[] = $question;
        }
        
        $count = count($createdQuestions);
        $message = $count === 1 
            ? 'Question created successfully' 
            : "{$count} questions created successfully";
        
        return response()->json([
            'message' => $message,
            'questions' => $createdQuestions,
        ], 201);
    }

    /**
     * Display the specified question
     */
    public function show(int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        
        return response()->json([
            'question' => $question,
        ]);
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        
        $validated = $request->validate([
            'statement' => 'sometimes|required|string',
            'option_a' => 'sometimes|required|string|max:500',
            'option_b' => 'sometimes|required|string|max:500',
            'option_c' => 'sometimes|required|string|max:500',
            'option_d' => 'sometimes|required|string|max:500',
            'correct_option' => 'sometimes|required|in:a,b,c,d',
            'order' => 'sometimes|required|integer|min:1',
        ]);
        
        $question->update($validated);
        
        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question->fresh(),
        ]);
    }

    /**
     * Remove the specified question
     */
    public function destroy(int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        $question->delete();
        
        return response()->json([
            'message' => 'Question deleted successfully',
        ]);
    }

    /**
     * Reorder questions
     */
    public function reorder(Request $request, string $type, int $id): JsonResponse
    {
        $questionable = $this->getQuestionable($type, $id);
        
        if (!$questionable) {
            return response()->json([
                'message' => 'Quiz or Final Exam not found',
            ], 404);
        }
        
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.order' => 'required|integer|min:1',
        ]);
        
        foreach ($request->questions as $questionData) {
            $question = Question::findOrFail($questionData['id']);
            
            // Ensure question belongs to this quiz/exam
            if ($question->questionable_id != $id || $question->questionable_type != get_class($questionable)) {
                return response()->json([
                    'message' => 'Question does not belong to this quiz/exam',
                ], 422);
            }
            
            $question->order = $questionData['order'];
            $question->save();
        }
        
        return response()->json([
            'message' => 'Questions reordered successfully',
        ]);
    }

    /**
     * Get the questionable model (Quiz or FinalExam)
     */
    private function getQuestionable(string $type, int $id)
    {
        switch ($type) {
            case 'quiz':
                return QuizActivity::find($id);
                
            case 'final_exam':
                return FinalExamActivity::find($id);
                
            default:
                return null;
        }
    }
}

