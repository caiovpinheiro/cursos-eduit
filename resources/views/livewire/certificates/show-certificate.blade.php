<div>
    <p style="margin: 0 0 10px;">
        <a class="muted" href="{{ route('web.certificates.index') }}">&larr; Voltar para certificados</a>
    </p>

    <section class="card">
        <div class="cert-show-layout">
            <div>
                @include('partials.certificate-preview-embed', [
                    'certificate' => $certificate,
                    'certificateId' => $certificate->id,
                    'courseTitle' => $certificate->course->title ?? 'Certificado',
                    'variant' => 'full',
                ])
            </div>
            <div>
                <!-- <span class="badge badge-success" style="margin-bottom: 10px;">Conquista desbloqueada</span> -->
                <h1 class="page-title" style="margin-top: 10px; margin-bottom: 6px;">{{ $certificate->course->title ?? 'Certificado' }}</h1>
                <p class="muted" style="margin: 0 0 20px;">Emitido em {{ optional($certificate->issue_date)->format('d/m/Y H:i') }}</p>
                <!-- <p style="margin: 0 0 8px;"><strong>UUID:</strong> {{ $certificate->uuid }}</p> -->
                <!-- <p style="margin: 0 0 10px;">
                    <strong>Validacao pública:</strong>
                    <a href="{{ route('web.certificates.validate', $certificate->uuid) }}">
                        {{ route('web.certificates.validate', $certificate->uuid) }}
                    </a>
                </p> -->
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                    <a class="btn btn-primary" href="{{ route('web.certificates.pdf', $certificate->id) }}">Baixar PDF</a>
                    <a class="btn" href="{{ route('web.certificates.validate', $certificate->uuid) }}" target="_blank" rel="noopener noreferrer">
                        Abrir validacao
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
