<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pré-visualização HTML (alinhamento da arte)
    |--------------------------------------------------------------------------
    |
    | Rota autenticada: GET /certificates/{id}/preview (não há link no site).
    | Defina false em produção se não quiser expor a pré-visualização.
    |
    */
    'preview_enabled' => env('CERTIFICATE_PREVIEW_ENABLED', true),

    'preview_image' => [
        'disk' => env('CERTIFICATE_PREVIEW_DISK', 'public'),
        'dir' => env('CERTIFICATE_PREVIEW_DIR', 'certificate-previews'),
        'dpi' => (int) env('CERTIFICATE_PREVIEW_DPI', 150),
    ],

    /*
    |--------------------------------------------------------------------------
    | Arte de fundo do certificado (PDF / export como imagem)
    |--------------------------------------------------------------------------
    |
    | Caminho relativo a `public/` ou caminho absoluto.
    | Exporte a primeira página do PDF de referência como PNG ou JPG
    | (ex.: public/images/certificates/background.png) e ajuste as posições
    | em resources/views/certificates/pdf.blade.php se o texto não coincidir.
    |
    */
    'background' => env('CERTIFICATE_BACKGROUND', 'images/certificates/background.png'),

    'paper' => env('CERTIFICATE_PAPER', 'a4'),

    'orientation' => env('CERTIFICATE_ORIENTATION', 'landscape'),

    /*
    |--------------------------------------------------------------------------
    | QR Code (validação)
    |--------------------------------------------------------------------------
    |
    | Tamanho em pixels do QR gerado (PNG embutido no PDF).
    |
    */
    'qr_size' => (int) env('CERTIFICATE_QR_SIZE', 112),

];
