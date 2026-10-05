<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Display a listing of user's certificates
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $certificates = Certificate::where('user_id', Auth::id())
            ->with(['course'])
            ->orderBy('issue_date', 'desc')
            ->paginate($perPage);

        return response()->json($certificates);
    }

    /**
     * Display the specified certificate
     */
    public function show(Certificate $certificate): JsonResponse
    {
        // Ensure user can only view their own certificates
        if ($certificate->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized access to certificate',
            ], 403);
        }

        return response()->json([
            'certificate' => $certificate->load(['course', 'user']),
        ]);
    }

    /**
     * Get certificate for a specific course
     */
    public function getByCourse(Request $request, $courseId): JsonResponse
    {
        $user = $request->user();
        
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->with(['course', 'user'])
            ->first();
        
        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found for this course'
            ], 404);
        }
        
        $this->authorize('view', $certificate);
        
        return response()->json([
            'certificate' => $certificate,
        ]);
    }
}
