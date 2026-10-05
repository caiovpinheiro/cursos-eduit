<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizActivity;
use App\Models\FinalExamActivity;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QuestionController extends Controller
{
    use AuthorizesRequests;

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
        
        // Check if user can view this questionable's activity
        $activity = $questionable->activity;
        $this->authorize('view', $activity);
        
        $questions = $questionable->questions()->ordered()->get();
        
        return response()->json([
            'type' => $type,
            'id' => $id,
            'questions' => QuestionResource::collection($questions),
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
        
        // Check if user can create questions (admin only)
        $this->authorize('create', Question::class);
        
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
            'questions' => QuestionResource::collection($createdQuestions),
        ], 201);
    }

    /**
     * Display the specified question
     */
    public function show(Request $request, int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        $this->authorize('view', $question);
        
        return response()->json([
            'question' => new QuestionResource($question),
        ]);
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        $this->authorize('update', $question);
        
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
            'question' => new QuestionResource($question->fresh()),
        ]);
    }

    /**
     * Remove the specified question
     */
    public function destroy(int $questionId): JsonResponse
    {
        $question = Question::findOrFail($questionId);
        $this->authorize('delete', $question);
        
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
        
        // Check if user can update (admin only)
        $activity = $questionable->activity;
        $this->authorize('update', $activity);
        
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

