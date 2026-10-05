<div>
    <section class="card card-soft" style="margin-bottom: 14px;">
        <h1 class="page-title" style="margin-bottom: 4px;">Meus certificados</h1>
        <p class="muted" style="margin: 0;">Acompanhe suas conquistas e valide seus certificados.</p>
    </section>

    @if($certificates->count() > 0)
        <section class="grid-3">
            @foreach($certificates as $certificate)
                <article class="card">
                    <a class="cert-list-card-thumb" href="{{ route('web.certificates.show', $certificate->id) }}">
                        @include('partials.certificate-preview-embed', [
                            'certificate' => $certificate,
                            'certificateId' => $certificate->id,
                            'courseTitle' => $certificate->course->title ?? 'Curso',
                            'variant' => 'thumb',
                        ])
                    </a>
                    <!-- <span class="badge badge-success" style="margin-bottom: 8px;">Certificado</span> -->
                    <h2 style="margin: 0 0 8px 0; font-size: 18px;">
                        <a href="{{ route('web.certificates.show', $certificate->id) }}">
                            {{ $certificate->course->title ?? 'Curso' }}
                        </a>
                    </h2>
                    <p class="muted" style="margin: 0 0 6px;">Emitido em {{ optional($certificate->issue_date)->format('d/m/Y H:i') }}</p>
                    <!-- <p class="muted" style="margin: 0;">UUID: {{ $certificate->uuid }}</p> -->
                    <div style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                        <a class="btn btn-primary" href="{{ route('web.certificates.show', $certificate->id) }}">Ver certificado</a>
                        <a class="btn" href="{{ route('web.certificates.pdf', $certificate->id) }}">Baixar PDF</a>
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <article class="card">
            <h3 style="margin-top: 0;">Nenhum certificado ainda</h3>
            <p class="muted" style="margin-bottom: 0;">Conclua cursos para desbloquear seus certificados.</p>
        </article>
    @endif

    <div style="margin-top: 16px;">{{ $certificates->links() }}</div>
</div>
