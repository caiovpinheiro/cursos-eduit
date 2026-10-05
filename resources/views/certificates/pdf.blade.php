{{-- Ajuste top/left/font-size abaixo para alinhar à arte em public/images/certificates/background.png --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Charis+SIL:ital,wght@0,400;0,700;1,400;1,700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        @page { margin: 0; }
        .charis-sil-regular {
            font-family: "Charis SIL", serif;
            font-weight: 400;
            font-style: normal;
        }

        .charis-sil-bold {
            font-family: "Charis SIL", serif;
            font-weight: 700;
            font-style: normal;
        }

        .charis-sil-regular-italic {
            font-family: "Charis SIL", serif;
            font-weight: 400;
            font-style: italic;
        }

        .charis-sil-bold-italic {
            font-family: "Charis SIL", serif;
            font-weight: 700;
            font-style: italic;
        }

        .inter-400 {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #181a4d;
        }
        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            @if($backgroundDataUri)
            background-image: url('{{ $backgroundDataUri }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            @else
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            @endif
        }
        .layer {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            padding: 0 24mm;
        }
        .student-name {
            top: 82mm;
            font-size: 36px;
            /* font-weight: 700; */
            letter-spacing: 0.02em;
        }

        .course-line {
            top: 106mm;
            font-size: 27px;
            font-weight: 600;
            line-height: 1.35;
            color: #1e6882;
        }

        .meta-line {
            top: 131mm;
            font-size: 18px;
            font-weight: 600;
            color: #525379;
        }

        .date-issue-line {
            top: 140.5mm;
            font-size: 18px;
            color: #525379;
        }

        .date-issue-line span {
            background-color: white;
            padding: 5px;
        }

        .footer-line {
            bottom: 14mm;
            font-size: 8px;
            line-height: 1.4;
            text-align: right;
        }

        .certificate-qr img {
            display: block;
            margin-left: auto;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
        }

        .certificate-qr-caption {
            margin-top: 4px;
            margin-right: 16px;
            font-size: 7px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="layer student-name inter-400">{{ $studentName }}</div>
        <div class="layer course-line charis-sil-regular">"{{ $courseTitle }}"</div>
        <div class="layer meta-line inter-400">
            @if($workloadLabel)
                <span>{{ $workloadLabel }}</span>
            @endif
        </div>
        <div class="layer date-issue-line inter-400">
            <span>São Paulo, {{ $issueDateFormatted }}</span>
        </div>
        <div class="layer footer-line inter-400 certificate-qr">
            @if(!empty($validationQrDataUri))
                <img src="{{ $validationQrDataUri }}" alt="" width="{{ $qrSize }}" height="{{ $qrSize }}">
                <div class="certificate-qr-caption inter-400">Validação do certificado</div>
            @else
                <div style="font-size:8px;word-break:break-all;">{{ $validationUrl }}</div>
            @endif
        </div>
    </div>
</body>
</html>
