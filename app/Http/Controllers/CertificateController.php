<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Http\Resources\CertificateResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CertificateController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of certificates
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Certificate::class);
        
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        // Students only see their own certificates
        $query = Certificate::with(['course']);
        
        if ($user->isStudent()) {
            $query->where('user_id', $user->id);
        }
        
        $certificates = $query->orderBy('issue_date', 'desc')
            ->paginate($perPage);

        return CertificateResource::collection($certificates)->response();
    }

    /**
     * Display the specified certificate
     */
    public function show(Request $request, $id): JsonResponse
    {
        $certificate = Certificate::with(['course'])->findOrFail($id);
        $this->authorize('view', $certificate);

        return response()->json([
            'certificate' => new CertificateResource($certificate),
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
            ->with(['course'])
            ->first();
        
        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found for this course',
                'code' => 'certificate_not_found',
            ], 404);
        }
        
        $this->authorize('view', $certificate);
        
        return response()->json([
            'certificate' => new CertificateResource($certificate),
        ]);
    }
}

