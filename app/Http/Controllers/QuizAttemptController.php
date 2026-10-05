<?php

namespace App\Http\Controllers;

use App\Models\QuizActivity;
use App\Models\FinalExamActivity;
use App\Models\Activity;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\UserCourse;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    /**
     * Submit quiz answers
     */
    public function submitQuiz(Request $request, $quizId): JsonResponse
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.selected_option' => 'required|string|in:a,b,c,d',
        ]);

        $quiz = QuizActivity::findOrFail($quizId);
        $activity = Activity::where('activityable_id', $quizId)
            ->where('activityable_type', 'App\\Models\\QuizActivity')
            ->firstOrFail();

        $user = $request->user();

        // Verificar se está matriculado no curso
        $enrollment = UserCourse::where('user_id', $user->id)
            ->where('course_id', $activity->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You are not enrolled in this course'
            ], 403);
        }

        // Buscar todas as questões do quiz
        $questions = Question::where('questionable_id', $quizId)
            ->where('questionable_type', 'App\\Models\\QuizActivity')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'message' => 'This quiz has no questions'
            ], 400);
        }

        // Calcular nota
        $result = $this->calculateScore($questions, $request->answers);

        // Contar tentativas anteriores
        $attemptNumber = QuizAttempt::where('user_id', $user->id)
            ->where('questionable_id', $quizId)
            ->where('questionable_type', 'App\\Models\\QuizActivity')
            ->count() + 1;

        // Criar registro da tentativa
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'questionable_id' => $quizId,
            'questionable_type' => 'App\\Models\\QuizActivity',
            'attempt_number' => $attemptNumber,
            'answers' => $request->answers,
            'total_questions' => $result['total'],
            'correct_answers' => $result['correct'],
            'score' => $result['score'],
            'passed' => true, // Quiz sempre passa (não tem nota de corte)
            'submitted_at' => now(),
        ]);

        // Marcar atividade como concluída automaticamente
        UserActivity::updateOrCreate(
            [
                'user_id' => $user->id,
                'activity_id' => $activity->id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'attempt' => [
                'id' => $attempt->id,
                'attempt_number' => $attempt->attempt_number,
                'total_questions' => $attempt->total_questions,
                'correct_answers' => $attempt->correct_answers,
                'score' => $attempt->score,
                'passed' => true,
                'submitted_at' => $attempt->submitted_at,
            ],
            'details' => $result['details'],
        ], 201);
    }

    /**
     * Submit final exam answers
     */
    public function submitFinalExam(Request $request, $examId): JsonResponse
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.selected_option' => 'required|string|in:a,b,c,d',
        ]);

        $exam = FinalExamActivity::findOrFail($examId);
        $activity = Activity::where('activityable_id', $examId)
            ->where('activityable_type', 'App\\Models\\FinalExamActivity')
            ->firstOrFail();

        $user = $request->user();

        // Verificar se está matriculado no curso
        $enrollment = UserCourse::where('user_id', $user->id)
            ->where('course_id', $activity->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You are not enrolled in this course'
            ], 403);
        }

        // Verificar número de tentativas anteriores
        $previousAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('questionable_id', $examId)
            ->where('questionable_type', 'App\\Models\\FinalExamActivity')
            ->count();

        if ($previousAttempts >= $exam->max_attempts) {
            return response()->json([
                'message' => "You have reached the maximum number of attempts ({$exam->max_attempts})"
            ], 403);
        }

        // Buscar todas as questões da prova
        $questions = Question::where('questionable_id', $examId)
            ->where('questionable_type', 'App\\Models\\FinalExamActivity')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'message' => 'This exam has no questions'
            ], 400);
        }

        // Calcular nota
        $result = $this->calculateScore($questions, $request->answers);
        
        $attemptNumber = $previousAttempts + 1;
        $passed = $result['score'] >= $exam->passing_score;

        // Criar registro da tentativa
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'questionable_id' => $examId,
            'questionable_type' => 'App\\Models\\FinalExamActivity',
            'attempt_number' => $attemptNumber,
            'answers' => $request->answers,
            'total_questions' => $result['total'],
            'correct_answers' => $result['correct'],
            'score' => $result['score'],
            'passed' => $passed,
            'submitted_at' => now(),
        ]);

        // Se passou, marcar atividade como concluída
        if ($passed) {
            UserActivity::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'completed' => true,
                    'completed_at' => now(),
                ]
            );
        }

        return response()->json([
            'message' => $passed ? 'Final exam passed!' : 'Final exam not passed. Try again.',
            'attempt' => [
                'id' => $attempt->id,
                'attempt_number' => $attempt->attempt_number,
                'total_questions' => $attempt->total_questions,
                'correct_answers' => $attempt->correct_answers,
                'score' => $attempt->score,
                'passing_score' => $exam->passing_score,
                'passed' => $passed,
                'attempts_remaining' => $exam->max_attempts - $attemptNumber,
                'submitted_at' => $attempt->submitted_at,
            ],
            'details' => $result['details'],
        ], 201);
    }

    /**
     * Get quiz attempts
     */
    public function getQuizAttempts(Request $request, $quizId): JsonResponse
    {
        $user = $request->user();

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('questionable_id', $quizId)
            ->where('questionable_type', 'App\\Models\\QuizActivity')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'attempts' => $attempts->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'total_questions' => $attempt->total_questions,
                    'correct_answers' => $attempt->correct_answers,
                    'score' => $attempt->score,
                    'submitted_at' => $attempt->submitted_at,
                ];
            }),
            'total_attempts' => $attempts->count(),
            'best_score' => $attempts->max('score'),
        ]);
    }

    /**
     * Get final exam attempts
     */
    public function getFinalExamAttempts(Request $request, $examId): JsonResponse
    {
        $user = $request->user();
        $exam = FinalExamActivity::findOrFail($examId);

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('questionable_id', $examId)
            ->where('questionable_type', 'App\\Models\\FinalExamActivity')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'attempts' => $attempts->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'total_questions' => $attempt->total_questions,
                    'correct_answers' => $attempt->correct_answers,
                    'score' => $attempt->score,
                    'passed' => $attempt->passed,
                    'submitted_at' => $attempt->submitted_at,
                ];
            }),
            'total_attempts' => $attempts->count(),
            'max_attempts' => $exam->max_attempts,
            'attempts_remaining' => $exam->max_attempts - $attempts->count(),
            'passing_score' => $exam->passing_score,
            'best_score' => $attempts->max('score'),
            'has_passed' => $attempts->where('passed', true)->isNotEmpty(),
        ]);
    }

    /**
     * Calculate score based on answers
     */
    private function calculateScore($questions, $answers): array
    {
        $answersByQuestionId = collect($answers)->keyBy('question_id');
        $correct = 0;
        $details = [];

        foreach ($questions as $question) {
            $userAnswer = $answersByQuestionId->get($question->id);
            $isCorrect = $userAnswer && $userAnswer['selected_option'] === $question->correct_option;
            
            if ($isCorrect) {
                $correct++;
            }

            $details[] = [
                'question_id' => $question->id,
                'selected_option' => $userAnswer['selected_option'] ?? null,
                'is_correct' => $isCorrect,
            ];
        }

        $total = $questions->count();
        $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'correct' => $correct,
            'score' => $score,
            'details' => $details,
        ];
    }
}

