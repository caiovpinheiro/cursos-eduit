<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\ActivityCompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ActivityCompletionController extends Controller
{
    public function store(Request $request, int $activityId, ActivityCompletionService $activityCompletionService): RedirectResponse
    {
        try {
            $activityCompletionService->complete($request->user(), $activityId);

            return $this->redirectAfterActivityAction($request, $activityId, 'Atividade marcada como concluida.');
        } catch (ValidationException $exception) {
            return $this->redirectAfterActivityAction(
                $request,
                $activityId,
                collect($exception->errors())->flatten()->first() ?? 'Nao foi possivel concluir a atividade.'
            );
        }
    }

    public function destroy(Request $request, int $activityId, ActivityCompletionService $activityCompletionService): RedirectResponse
    {
        try {
            $activityCompletionService->incomplete($request->user(), $activityId);

            return $this->redirectAfterActivityAction($request, $activityId, 'Atividade desmarcada.');
        } catch (ValidationException $exception) {
            return $this->redirectAfterActivityAction(
                $request,
                $activityId,
                collect($exception->errors())->flatten()->first() ?? 'Nao foi possivel desmarcar a atividade.'
            );
        }
    }

    private function redirectAfterActivityAction(Request $request, int $activityId, string $status): RedirectResponse
    {
        $activity = Activity::findOrFail($activityId);

        if ($request->input('redirect_to') === 'player') {
            return redirect()
                ->route('web.courses.player', [
                    'id' => $activity->course_id,
                    'activity' => $activity->id,
                ])
                ->with('status', $status);
        }

        return redirect()
            ->route('web.courses.show', $activity->course_id)
            ->with('status', $status);
    }
}
