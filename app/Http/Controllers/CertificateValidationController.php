<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CertificateValidationController extends Controller
{
    /**
     * Validate a certificate by UUID (public endpoint)
     */
    public function validate(Request $request, string $uuid): JsonResponse
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['user', 'course'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found',
            ], 404);
        }

        // Verify hash validation
        $expectedHash = $this->generateValidationHash($certificate);
        $isValid = hash_equals($expectedHash, $certificate->hash_validation);

        if (!$isValid) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate validation failed',
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'certificate' => [
                'uuid' => $certificate->uuid,
                'student_name' => $certificate->user->name,
                'course_title' => $certificate->course->title,
                'course_total_duration' => $certificate->course->total_duration,
                'issue_date' => $certificate->issue_date,
            ],
        ]);
    }

    /**
     * Generate validation hash for certificate
     */
    private function generateValidationHash(Certificate $certificate): string
    {
        $data = [
            'uuid' => $certificate->uuid,
            'user_id' => $certificate->user_id,
            'course_id' => $certificate->course_id,
            'issue_date' => $certificate->issue_date->toISOString(),
        ];

        return hash('sha256', json_encode($data) . config('app.key'));
    }
}
