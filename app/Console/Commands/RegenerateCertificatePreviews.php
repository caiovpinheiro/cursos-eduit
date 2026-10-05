<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Services\CertificateDocumentService;
use Illuminate\Console\Command;

class RegenerateCertificatePreviews extends Command
{
    protected $signature = 'certificates:regenerate-previews {--id= : ID especifico do certificado}';

    protected $description = 'Gera ou regera as imagens JPEG de pre-visualizacao dos certificados';

    public function handle(CertificateDocumentService $documents): int
    {
        if (! $documents->canGeneratePreviewImages()) {
            $this->error('pdftoppm nao encontrado. Instale poppler-utils no servidor.');

            return self::FAILURE;
        }

        $query = Certificate::with(['user', 'course']);

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $certificates = $query->get();

        if ($certificates->isEmpty()) {
            $this->warn('Nenhum certificado encontrado.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($certificates->count());
        $bar->start();

        $ok = 0;
        $failed = 0;

        foreach ($certificates as $certificate) {
            try {
                $documents->deletePreviewImage($certificate);
                $documents->ensurePreviewImage($certificate);
                $ok++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Certificado #{$certificate->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Concluido: {$ok} gerado(s), {$failed} falha(s).");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
