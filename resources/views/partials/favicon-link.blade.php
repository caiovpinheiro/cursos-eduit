{{-- URL absoluta a partir de APP_URL (evita http atrás de proxy / asset() com esquema errado) --}}
<link rel="icon" href="{{ rtrim(config('app.url'), '/') }}/images/favicon.png" type="image/png" sizes="any">
