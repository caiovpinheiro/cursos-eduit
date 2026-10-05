<?php

namespace App\Logging;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class SecurityAuditLogger
{
    /**
     * @param  'info'|'warning'|'error'|'debug'  $level
     * @param  array<string, mixed>  $context
     */
    public static function log(string $level, string $event, Request $request, array $context = []): void
    {
        $baseContext = [
            'request_id' => (string) Str::uuid(),
            'route' => $request->route()?->getName() ?? $request->path(),
            'ip' => $request->ip(),
        ];
        $merged = array_merge($baseContext, $context);

        try {
            Log::channel('security')->log($level, $event, $merged);
        } catch (\Throwable $e) {
            report($e);
            try {
                Log::log($level, '[security-audit] '.$event, $merged);
            } catch (\Throwable) {
                // Evita quebrar o fluxo (ex.: logout) se o log de fallback também falhar.
            }
        }
    }
}
