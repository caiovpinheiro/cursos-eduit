@php
    $certificateId = $certificateId ?? ($certificate->id ?? null);
    $variant = $variant ?? 'full';
    $previewEnabled = (bool) config('certificate.preview_enabled', true);
    $imageUrl = $certificateId ? route('web.certificates.preview-image', $certificateId) : null;
    $courseTitle = $courseTitle ?? ($certificate->course->title ?? 'Certificado');
@endphp

@if ($previewEnabled && $imageUrl)
    <div @class(['cert-preview', 'cert-preview--'.$variant])>
        <img
            class="cert-preview-img"
            src="{{ $imageUrl }}"
            alt="Pré-visualização do certificado: {{ $courseTitle }}"
            loading="lazy"
            decoding="async"
        >
    </div>
@else
    <div @class(['cert-preview', 'cert-preview--'.$variant, 'cert-preview--unavailable'])>
        <div class="cert-preview-placeholder">
            <span>Pré-visualização indisponível</span>
        </div>
    </div>
@endif
