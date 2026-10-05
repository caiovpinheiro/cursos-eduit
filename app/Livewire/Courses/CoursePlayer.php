<?php

namespace App\Livewire\Courses;

use App\Http\Controllers\Web\AssessmentController;
use App\Models\Activity;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\FinalExamActivity;
use App\Models\Question;
use App\Models\QuizActivity;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\CourseProgressService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CoursePlayer extends Component
{
    public Course $course;

    public bool $sidebarCollapsed = false;

    #[Url(as: 'activity', except: '')]
    public string $activityId = '';

    /** Modo questão a questão: "1" quando o aluno clicou em Começar (quiz ou prova final). */
    #[Url(as: 'play', except: '')]
    public string $assessmentPlay = '';

    public int $assessmentStep = 0;

    /** @var array<int|string, string> */
    public array $assessmentAnswers = [];

    public function mount(int $id): void
    {
        $this->course = Course::with(['activities.activityable'])->findOrFail($id);

        /** @var User $user */
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403);
    }

    public function updatedActivityId(): void
    {
        $this->resetAssessmentPlayState();
    }

    public function updatedAssessmentPlay(string $value): void
    {
        if ($value === '') {
            $this->assessmentStep = 0;
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarCollapsed = ! $this->sidebarCollapsed;
    }

    public function startAssessmentPlay(): void
    {
        $this->assessmentPlay = '1';
        $this->assessmentStep = 0;
        $this->assessmentAnswers = [];
    }

    public function submitAssessment(int $activityableId): mixed
    {
        $activity = Activity::where('course_id', $this->course->id)
            ->where('activityable_id', $activityableId)
            ->whereIn('activityable_type', [QuizActivity::class, FinalExamActivity::class])
            ->firstOrFail();

        return $activity->activityable_type === QuizActivity::class
            ? $this->submitQuizAnswers($activityableId)
            : $this->submitExamAnswers($activityableId);
    }

    public function submitQuizAnswers(int $quizId): mixed
    {
        Activity::where('course_id', $this->course->id)
            ->where('activityable_id', $quizId)
            ->where('activityable_type', QuizActivity::class)
            ->firstOrFail();

        $questions = Question::where('questionable_id', $quizId)
            ->where('questionable_type', QuizActivity::class)
            ->orderBy('order')
            ->get();

        $lastIdx = max(0, $questions->count() - 1);
        if ($this->assessmentStep < $lastIdx) {
            $this->assessmentNext($lastIdx);

            return null;
        }

        $rules = [];
        foreach ($questions as $q) {
            $rules['assessmentAnswers.'.$q->id] = ['required', 'in:a,b,c,d'];
        }
        $this->validate($rules);

        $request = request();
        $request->merge(['answers' => $this->assessmentAnswers]);

        return app(AssessmentController::class)->submitQuiz($request, $quizId);
    }

    public function submitExamAnswers(int $examId): mixed
    {
        Activity::where('course_id', $this->course->id)
            ->where('activityable_id', $examId)
            ->where('activityable_type', FinalExamActivity::class)
            ->firstOrFail();

        $questions = Question::where('questionable_id', $examId)
            ->where('questionable_type', FinalExamActivity::class)
            ->orderBy('order')
            ->get();

        $lastIdx = max(0, $questions->count() - 1);
        if ($this->assessmentStep < $lastIdx) {
            $this->assessmentNext($lastIdx);

            return null;
        }

        $rules = [];
        foreach ($questions as $q) {
            $rules['assessmentAnswers.'.$q->id] = ['required', 'in:a,b,c,d'];
        }
        $this->validate($rules);

        $request = request();
        $request->merge(['answers' => $this->assessmentAnswers]);

        return app(AssessmentController::class)->submitFinalExam($request, $examId);
    }

    public function assessmentNext(int $lastIndex): void
    {
        if ($this->assessmentStep < $lastIndex) {
            $this->assessmentStep++;
        }
    }

    public function assessmentPrev(): void
    {
        if ($this->assessmentStep > 0) {
            $this->assessmentStep--;
        }
    }

    private function resetAssessmentPlayState(): void
    {
        $this->assessmentPlay = '';
        $this->assessmentStep = 0;
        $this->assessmentAnswers = [];
    }

    #[Layout('layouts.app')]
    public function render(CourseProgressService $progressService)
    {
        /** @var User $user */
        $user = Auth::user();
        $enrollment = $progressService->getEnrollment($user, $this->course->id);
        abort_unless($enrollment, 403);

        $selectedActivity = $this->resolveSelectedActivity();
        $progress = $progressService->buildProgressPayload($enrollment, $this->course->id, $user);

        $completedActivityIds = UserActivity::where('user_id', $user->id)
            ->where('completed', true)
            ->whereHas('activity', function ($query): void {
                $query->where('course_id', $this->course->id);
            })
            ->pluck('activity_id')
            ->toArray();

        $nonExamActivities = $this->course->activities->filter(function (Activity $activity): bool {
            return $activity->activityable_type !== 'App\\Models\\FinalExamActivity';
        });
        $allNonExamCompleted = $nonExamActivities->every(function (Activity $activity) use ($completedActivityIds): bool {
            return in_array($activity->id, $completedActivityIds, true);
        });

        $activityStateMap = [];
        foreach ($this->course->activities as $activity) {
            $isFinalExam = $activity->activityable_type === 'App\\Models\\FinalExamActivity';
            $unlocked = ! $isFinalExam || $allNonExamCompleted;
            $activityStateMap[$activity->id] = [
                'unlocked' => $unlocked,
                'locked_reason' => $unlocked ? null : 'Conclua os demais modulos para desbloquear a prova final.',
            ];
        }

        $attempts = collect();
        if ($selectedActivity && in_array($selectedActivity->activityable_type, [
            'App\\Models\\QuizActivity',
            'App\\Models\\FinalExamActivity',
        ], true)) {
            $attempts = QuizAttempt::where('user_id', $user->id)
                ->where('questionable_id', $selectedActivity->activityable_id)
                ->where('questionable_type', $selectedActivity->activityable_type)
                ->orderByDesc('submitted_at')
                ->get();
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->first();

        $activitiesOrdered = $this->course->activities->sortBy('order')->values();
        $totalActivities = $activitiesOrdered->count();
        $completedCount = $activitiesOrdered->filter(function (Activity $a) use ($completedActivityIds): bool {
            return in_array($a->id, $completedActivityIds, true);
        })->count();
        $progressPercent = $totalActivities > 0
            ? (int) round(100 * $completedCount / $totalActivities)
            : 0;

        $activityMeta = [];
        foreach ($activitiesOrdered as $activity) {
            $activityMeta[$activity->id] = $this->activityMetaFor($activity, $progressService);
        }

        $nextActivity = $this->computeNextActivity($activitiesOrdered, $selectedActivity, $activityStateMap);

        $videoEmbedUrl = null;
        if ($selectedActivity && $selectedActivity->activityable_type === 'App\\Models\\VideoActivity') {
            $link = $selectedActivity->activityable->link ?? '';
            $videoEmbedUrl = self::resolveVideoEmbedUrl($link);
        }

        $playerTitle = $selectedActivity
            ? $selectedActivity->title.' · '.$this->course->title
            : $this->course->title;

        $assessmentFeedback = session('assessment_feedback');

        return view('livewire.courses.course-player', [
            'selectedActivity' => $selectedActivity,
            'completedActivityIds' => $completedActivityIds,
            'attempts' => $attempts,
            'progress' => $progress,
            'activityStateMap' => $activityStateMap,
            'certificate' => $certificate,
            'activitiesOrdered' => $activitiesOrdered,
            'totalActivities' => $totalActivities,
            'completedCount' => $completedCount,
            'progressPercent' => $progressPercent,
            'activityMeta' => $activityMeta,
            'nextActivity' => $nextActivity,
            'videoEmbedUrl' => $videoEmbedUrl,
            'assessmentFeedback' => $assessmentFeedback,
            'feedbackMatches' => $this->assessmentFeedbackMatches($selectedActivity, $assessmentFeedback),
            'assessmentPlayActive' => $this->assessmentPlay !== '',
        ])->title($playerTitle);
    }

    /**
     * @param  array<string, mixed>|null  $feedback
     */
    private function assessmentFeedbackMatches(?Activity $activity, mixed $feedback): bool
    {
        if (! $activity || ! is_array($feedback)) {
            return false;
        }

        $type = $feedback['type'] ?? '';
        $aid = (int) ($feedback['activityable_id'] ?? 0);

        if ($type === 'quiz' && $activity->activityable_type === 'App\\Models\\QuizActivity') {
            return $aid === (int) $activity->activityable_id;
        }

        if ($type === 'final_exam' && $activity->activityable_type === 'App\\Models\\FinalExamActivity') {
            return $aid === (int) $activity->activityable_id;
        }

        return false;
    }

    private function resolveSelectedActivity(): ?Activity
    {
        if ($this->activityId !== '') {
            $activity = $this->course->activities->firstWhere('id', (int) $this->activityId);
            if ($activity instanceof Activity) {
                return $activity;
            }
        }

        return $this->course->activities->sortBy('order')->first();
    }

    /**
     * @param  Collection<int, Activity>  $ordered
     */
    private function computeNextActivity(Collection $ordered, ?Activity $selected, array $activityStateMap): ?Activity
    {
        if (! $selected) {
            return null;
        }

        $found = false;
        foreach ($ordered as $act) {
            if ($found) {
                if ($activityStateMap[$act->id]['unlocked'] ?? true) {
                    return $act;
                }

                continue;
            }
            if ($act->id === $selected->id) {
                $found = true;
            }
        }

        return null;
    }

    private function activityMetaFor(Activity $activity, CourseProgressService $progressService): array
    {
        $short = class_basename($activity->activityable_type);
        $typeLabel = match ($short) {
            'ArticleActivity' => 'Leitura',
            'SupportMaterialActivity' => 'Material',
            'VideoActivity' => 'Vídeo',
            'EmbedContentActivity' => 'Conteúdo',
            'QuizActivity' => 'Quiz',
            'FinalExamActivity' => 'Prova final',
            'MiniGameActivity' => 'Atividade interativa',
            default => 'Atividade',
        };
        $badgeVariant = match ($short) {
            'VideoActivity' => 'video',
            'ArticleActivity', 'SupportMaterialActivity' => 'read',
            'QuizActivity' => 'quiz',
            'FinalExamActivity' => 'exam',
            'EmbedContentActivity' => 'embed',
            'MiniGameActivity' => 'game',
            default => 'read',
        };

        $min = $progressService->activityDurationMinutes($activity);

        return [
            'type_label' => $typeLabel,
            'type_short' => $short,
            'badge_variant' => $badgeVariant,
            'duration_min' => $min,
            'duration_label' => $progressService->formatActivityDurationLabel($activity),
        ];
    }

    /**
     * YouTube ou Vimeo (e URLs já no formato embed). Retorna URL segura para iframe.
     */
    public static function resolveVideoEmbedUrl(?string $link): ?string
    {
        if ($link === null || trim($link) === '') {
            return null;
        }

        $link = trim($link);

        $yt = self::youtubeEmbedUrlFromLink($link);
        if ($yt !== null) {
            return $yt;
        }

        if (preg_match('~^https?://(www\.)?youtube\.com/embed/[a-zA-Z0-9_-]{11}~i', $link)) {
            return $link;
        }

        if (preg_match('~(?:^https?:)?//player\.vimeo\.com/video/(\d+)~i', $link, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        if (preg_match('~vimeo\.com/video/(\d+)~i', $link, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        if (preg_match('~vimeo\.com/(?:channels/[^/]+/|groups/[^/]+/videos/|)(\d+)~i', $link, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        if (preg_match('~vimeo\.com/(\d+)~i', $link, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null;
    }

    private static function youtubeEmbedUrlFromLink(string $link): ?string
    {
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})~', $link, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return null;
    }
}
