<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CertificateDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function buildViewData(Certificate $certificate): array
    {
        $backgroundPath = config('certificate.background', '');
        $path = $backgroundPath !== '' && str_starts_with($backgroundPath, '/')
            ? $backgroundPath
            : public_path($backgroundPath);

        $backgroundDataUri = null;
        if (is_file($path) && is_readable($path)) {
            $mime = @mime_content_type($path) ?: 'image/png';
            $raw = @file_get_contents($path);
            if ($raw !== false) {
                $backgroundDataUri = 'data:'.$mime.';base64,'.base64_encode($raw);
            }
        }

        $courseTitle = $certificate->course?->title ?? 'Curso';
        $studentName = $certificate->user?->name ?? 'Aluno';

        $examScore = $certificate->final_exam_score;
        $finalExamLine = null;
        if (isset($examScore['score']) && $examScore['score'] !== null) {
            $finalExamLine = 'Prova final: '.number_format((float) $examScore['score'], 0).'%. '
                .($examScore['correct_answers'] ?? '?').'/'.($examScore['total_questions'] ?? '?').' questões';
        }

        $validationUrl = route('web.certificates.validate', $certificate->uuid);
        $qrSize = max(64, min(256, (int) config('certificate.qr_size', 112)));

        return [
            'certificate' => $certificate,
            'courseTitle' => $courseTitle,
            'studentName' => $studentName,
            'issueDateFormatted' => $certificate->issue_date
                ? $certificate->issue_date->copy()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y')
                : '',
            'workloadLabel' => $this->formatWorkloadLabel($certificate->course?->workload),
            'validationUrl' => $validationUrl,
            'validationQrDataUri' => $this->buildValidationQrDataUri($validationUrl, $qrSize),
            'qrSize' => $qrSize,
            'backgroundDataUri' => $backgroundDataUri,
            'finalExamLine' => $finalExamLine,
        ];
    }

    public function makePdfResponse(Certificate $certificate, string $downloadFilename): Response
    {
        $pdf = Pdf::loadView('certificates.pdf', $this->buildViewData($certificate));
        $pdf->setPaper(config('certificate.paper', 'a4'), config('certificate.orientation', 'landscape'));

        return $pdf->download($downloadFilename);
    }

    public function previewImageRelativePath(Certificate $certificate): string
    {
        $dir = trim((string) config('certificate.preview_image.dir', 'certificate-previews'), '/');

        return $dir.'/'.$certificate->id.'.jpg';
    }

    public function ensurePreviewImage(Certificate $certificate): string
    {
        $diskName = (string) config('certificate.preview_image.disk', 'public');
        $disk = Storage::disk($diskName);
        $relativePath = $this->previewImageRelativePath($certificate);

        if ($disk->exists($relativePath)) {
            return $relativePath;
        }

        $jpeg = $this->renderPdfFirstPageAsJpeg($certificate);
        $disk->put($relativePath, $jpeg);

        return $relativePath;
    }

    public function deletePreviewImage(Certificate $certificate): void
    {
        $diskName = (string) config('certificate.preview_image.disk', 'public');
        $relativePath = $this->previewImageRelativePath($certificate);
        Storage::disk($diskName)->delete($relativePath);
    }

    public function previewImageAbsolutePath(Certificate $certificate): string
    {
        $relativePath = $this->ensurePreviewImage($certificate);
        $diskName = (string) config('certificate.preview_image.disk', 'public');

        return Storage::disk($diskName)->path($relativePath);
    }

    private function renderPdfFirstPageAsJpeg(Certificate $certificate): string
    {
        if (! $this->canGeneratePreviewImages()) {
            throw new \RuntimeException(
                'Geracao de preview indisponivel: instale poppler-utils (pdftoppm) no servidor.'
            );
        }

        $pdf = Pdf::loadView('certificates.pdf', $this->buildViewData($certificate));
        $pdf->setPaper(config('certificate.paper', 'a4'), config('certificate.orientation', 'landscape'));
        $pdfBinary = $pdf->output();

        $tmpdir = sys_get_temp_dir();
        $pdfPath = $tmpdir.'/cert_pdf_'.uniqid('', true).'.pdf';
        $outputPrefix = $tmpdir.'/cert_img_'.uniqid('', true);

        try {
            if (file_put_contents($pdfPath, $pdfBinary) === false) {
                throw new \RuntimeException('Nao foi possivel gravar o PDF temporario.');
            }

            $dpi = max(72, min(300, (int) config('certificate.preview_image.dpi', 150)));
            $command = sprintf(
                'pdftoppm -jpeg -singlefile -r %d %s %s 2>&1',
                $dpi,
                escapeshellarg($pdfPath),
                escapeshellarg($outputPrefix)
            );

            $output = [];
            $exitCode = 0;
            exec($command, $output, $exitCode);

            $jpegPath = $outputPrefix.'.jpg';

            if ($exitCode !== 0 || ! is_file($jpegPath)) {
                throw new \RuntimeException(
                    'pdftoppm falhou: '.implode("\n", $output)
                );
            }

            $bytes = file_get_contents($jpegPath);

            if ($bytes === false || $bytes === '') {
                throw new \RuntimeException('Imagem JPEG gerada esta vazia.');
            }

            return $bytes;
        } finally {
            @unlink($pdfPath);
            @unlink($outputPrefix.'.jpg');
        }
    }

    public function canGeneratePreviewImages(): bool
    {
        static $available = null;

        if ($available !== null) {
            return $available;
        }

        $which = shell_exec('command -v pdftoppm 2>/dev/null');

        $available = is_string($which) && trim($which) !== '';

        return $available;
    }

    private function formatWorkloadLabel(?int $workloadHours): ?string
    {
        if ($workloadHours === null || $workloadHours <= 0) {
            return null;
        }

        return $workloadHours.' '.($workloadHours === 1 ? 'hora' : 'horas');
    }

    private function buildValidationQrDataUri(string $validationUrl, int $size): ?string
    {
        try {
            $qrCode = QrCode::create($validationUrl)
                ->setSize($size)
                ->setMargin(4);

            $writer = new PngWriter();

            return $writer->write($qrCode)->getDataUri();
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
