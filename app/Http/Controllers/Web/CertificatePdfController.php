<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateDocumentService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class CertificatePdfController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CertificateDocumentService $certificateDocuments,
    ) {}

    public function preview(Request $request, int $id): View
    {
        if (! config('certificate.preview_enabled', true)) {
            abort(404);
        }

        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        $this->authorize('view', $certificate);

        return view('certificates.pdf', $this->certificateDocuments->buildViewData($certificate));
    }

    public function previewImage(Request $request, int $id): BinaryFileResponse
    {
        if (! config('certificate.preview_enabled', true)) {
            abort(404);
        }

        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        $this->authorize('view', $certificate);

        try {
            $absolutePath = $this->certificateDocuments->previewImageAbsolutePath($certificate);
        } catch (\Throwable $e) {
            report($e);
            abort(503, 'Nao foi possivel gerar a pre-visualizacao do certificado.');
        }

        return response()->file($absolutePath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function download(Request $request, int $id): Response
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        $this->authorize('view', $certificate);

        $courseTitle = $certificate->course?->title ?? 'Curso';
        $slug = Str::slug($courseTitle) ?: 'curso';
        $filename = 'certificado-'.$slug.'-'.$certificate->id.'.pdf';

        return $this->certificateDocuments->makePdfResponse($certificate, $filename);
    }
}
