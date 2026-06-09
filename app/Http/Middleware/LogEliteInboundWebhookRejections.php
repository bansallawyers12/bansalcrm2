<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Logs 403 / 413 (and diagnostic context) for POST /emails/elite (and legacy /elite/emails).
 *
 * abort(403) throws before a normal response is returned; wrapping $next in try/catch
 * ensures rejections still reach storage/logs.
 *
 * Note: Laravel ValidatePostSize (413) runs in the global middleware stack before this —
 * those cases are logged in bootstrap/app.php (elite.inbound.payload_too_large).
 */
class LogEliteInboundWebhookRejections
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (HttpExceptionInterface $e) {
            if ($this->isEliteInboundWebhook($request)) {
                $code = $e->getStatusCode();
                if ($code === 403) {
                    $this->logForbidden($request, $e->getMessage() ?: 'Forbidden', 'http_exception');
                } elseif ($code === 413) {
                    $this->logPayloadTooLarge($request, $e->getMessage() ?: 'Payload Too Large', 'http_exception');
                }
            }
            throw $e;
        }

        if ($this->isEliteInboundWebhook($request)) {
            $status = $response->getStatusCode();
            if ($status === 403) {
                $this->logForbidden($request, $this->reasonFromResponse($response), 'response');
            } elseif ($status === 413) {
                $this->logPayloadTooLarge($request, $this->reasonFromResponse($response, 'Payload Too Large'), 'response');
            }
        }

        return $response;
    }

    private function isEliteInboundWebhook(Request $request): bool
    {
        return $request->isMethod('POST') && ($request->is('emails/elite') || $request->is('elite/emails'));
    }

    /**
     * @param  'http_exception'|'response'  $source
     */
    private function logForbidden(Request $request, string $reason, string $source): void
    {
        $reasonCode = match (true) {
            str_contains($reason, 'Invalid inbound secret') => 'invalid_inbound_secret',
            default => 'forbidden',
        };

        Log::warning('elite.inbound.forbidden', [
            'source' => $source,
            'reason_code' => $reasonCode,
            'reason' => $reason,
            'ip' => $request->ip(),
            'forwarded_for' => $request->header('X-Forwarded-For'),
            'url_without_query' => $request->url(),
            'query_has_secret' => $request->query->has('secret'),
            'header_x_elite_present' => $request->header('X-Elite-Webhook-Secret') !== null,
            'user_agent' => $request->userAgent() !== null ? substr((string) $request->userAgent(), 0, 200) : null,
            'content_length' => $request->header('Content-Length'),
        ]);
    }

    /**
     * @param  'http_exception'|'response'  $source
     */
    private function logPayloadTooLarge(Request $request, string $reason, string $source): void
    {
        $postMaxIni = ini_get('post_max_size');

        Log::warning('elite.inbound.payload_too_large', [
            'reason_code' => 'payload_too_large',
            'source' => $source,
            'reason' => $reason,
            'ip' => $request->ip(),
            'forwarded_for' => $request->header('X-Forwarded-For'),
            'url_without_query' => $request->url(),
            'content_length_header' => $request->header('Content-Length'),
            'content_length_server' => $request->server('CONTENT_LENGTH'),
            'php_post_max_size' => is_string($postMaxIni) ? $postMaxIni : null,
            'php_upload_max_filesize' => ini_get('upload_max_filesize'),
            'user_agent' => $request->userAgent() !== null ? substr((string) $request->userAgent(), 0, 200) : null,
        ]);
    }

    private function reasonFromResponse(Response $response, string $fallback = 'Forbidden'): string
    {
        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return $fallback;
        }
        $decoded = json_decode($content, true);
        if (is_array($decoded) && isset($decoded['message']) && is_string($decoded['message'])) {
            return $decoded['message'];
        }
        if (strlen($content) <= 500) {
            return $content;
        }

        return $fallback;
    }
}
