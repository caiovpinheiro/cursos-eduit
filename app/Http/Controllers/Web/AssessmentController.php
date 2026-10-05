<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\FinalExamActivity;
use App\Models\Question;
use App\Models\QuizActivity;
use App\Models\QuizAttempt;
use App\Models\UserActivity;
use App\Models\UserCourse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Em chamadas via Livewire, `redirect()` pode retornar o Redirector do Livewire, não apenas RedirectResponse.
     *
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Features\SupportRedirects\Redirector
     */
    public function submitQuiz(Request $request, int $quizId)
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'in:a,b,c,d'],
        ]);

        $quiz = QuizActivity::findOrFail($quizId);
        $activity = Activity::where('activityable_id', $quizId)
            ->where('activityable_type', QuizActivity::class)
            ->firstOrFail();

        $this->ensureEnrollment((int) $request->user()->id, (int) $activity->course_id);
        $questions = Question::where('questionable_id', $quizId)
            ->where('questionable_type', QuizActivity::class)
            ->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['quiz' => 'Este quiz nao possui questoes.']);
        }

        $result = $this->calculateScore($questions, $request->input('answers', []));
        $attemptNumber = QuizAttempt::where('user_id', $request->user()->id)
            ->where('questionable_id', $quizId)
            ->where('questionable_type', QuizActivity::class)
            ->count() + 1;

        $passingScore = (float) ($quiz->passing_score ?? 70);
        $passed = $result['score'] >= $passingScore;

        QuizAttempt::create([
            'user_id' => $request->user()->id,
            'questionable_id' => $quizId,
            'questionable_type' => QuizActivity::class,
            'attempt_number' => $attemptNumber,
            'answers' => $request->input('answers', []),
            'total_questions' => $result['total'],
            'correct_answers' => $result['correct'],
            'score' => $result['score'],
            'passed' => $passed,
            'submitted_at' => now(),
        ]);

        if ($passed) {
            UserActivity::updateOrCreate(
                ['user_id' => $request->user()->id, 'activity_id' => $activity->id],
                ['completed' => true, 'completed_at' => now()]
            );
        }

        return redirect()
            ->route('web.courses.player', ['id' => $activity->course_id, 'activity' => $activity->id])
            ->with('status', $passed ? 'Quiz enviado com sucesso.' : 'Quiz enviado. Voce pode tentar novamente.')
            ->with('assessment_feedback', [
                'type' => 'quiz',
                'activityable_id' => $quizId,
                'score' => $result['score'],
                'correct' => $result['correct'],
                'total' => $result['total'],
                'passed' => $passed,
                'details' => $result['details'],
            ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Features\SupportRedirects\Redirector
     */
    public function submitFinalExam(Request $request, int $examId)
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'in:a,b,c,d'],
        ]);

        $exam = FinalExamActivity::findOrFail($examId);
        $activity = Activity::where('activityable_id', $examId)
            ->where('activityable_type', FinalExamActivity::class)
            ->firstOrFail();

        $this->ensureEnrollment((int) $request->user()->id, (int) $activity->course_id);

        $previousAttempts = QuizAttempt::where('user_id', $request->user()->id)
            ->where('questionable_id', $examId)
            ->where('questionable_type', FinalExamActivity::class)
            ->count();

        if ($previousAttempts >= $exam->max_attempts) {
            return back()->withErrors([
                'exam' => "Voce atingiu o limite de tentativas ({$exam->max_attempts}).",
            ]);
        }

        $questions = Question::where('questionable_id', $examId)
            ->where('questionable_type', FinalExamActivity::class)
            ->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['exam' => 'Esta prova nao possui questoes.']);
        }

        $result = $this->calculateScore($questions, $request->input('answers', []));
        $attemptNumber = $previousAttempts + 1;
        $passed = $result['score'] >= (float) $exam->passing_score;

        QuizAttempt::create([
            'user_id' => $request->user()->id,
            'questionable_id' => $examId,
            'questionable_type' => FinalExamActivity::class,
            'attempt_number' => $attemptNumber,
            'answers' => $request->input('answers', []),
            'total_questions' => $result['total'],
            'correct_answers' => $result['correct'],
            'score' => $result['score'],
            'passed' => $passed,
            'submitted_at' => now(),
        ]);

        if ($passed) {
            UserActivity::updateOrCreate(
                ['user_id' => $request->user()->id, 'activity_id' => $activity->id],
                ['completed' => true, 'completed_at' => now()]
            );
        }

        return redirect()
            ->route('web.courses.player', ['id' => $activity->course_id, 'activity' => $activity->id])
            ->with('status', $passed ? 'Prova enviada e aprovada.' : 'Prova enviada. Voce pode tentar novamente.')
            ->with('assessment_feedback', [
                'type' => 'final_exam',
                'activityable_id' => $examId,
                'score' => $result['score'],
                'correct' => $result['correct'],
                'total' => $result['total'],
                'passed' => $passed,
                'attempts_remaining' => $exam->max_attempts - $attemptNumber,
                'details' => $result['details'],
            ]);
    }

    private function ensureEnrollment(int $userId, int $courseId): void
    {
        $enrollment = UserCourse::where('user_id', $userId)->where('course_id', $courseId)->first();
        abort_if(! $enrollment, 403);
    }

    /**
     * @param array<int, string> $answers
     * @return array{total:int,correct:int,score:float,details:array<int,array{question_id:int,selected_option:?string,correct_option:string,is_correct:bool}>}
     */
    private function calculateScore($questions, array $answers): array
    {
        $correct = 0;
        $details = [];
        foreach ($questions as $question) {
            $selected = $answers[(string) $question->id] ?? null;
            $isCorrect = $selected === $question->correct_option;
            if ($isCorrect) {
                $correct++;
            }
            $details[] = [
                'question_id' => (int) $question->id,
                'selected_option' => $selected,
                'correct_option' => (string) $question->correct_option,
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

