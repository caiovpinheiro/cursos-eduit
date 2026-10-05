<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseEnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function __invoke(Request $request, int $id, CourseEnrollmentService $enrollmentService): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $result = $enrollmentService->enroll($request->user(), $course);

        return redirect()
            ->route('web.courses.show', $course->id)
            ->with(
                'status',
                $result['created']
                    ? 'Matricula realizada com sucesso.'
                    : 'Voce ja esta matriculado neste curso.'
            );
    }
}
