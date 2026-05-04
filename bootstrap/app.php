<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));
            
            // Agent routes disabled - agents don't have login access (they exist only as records)
            // Route::middleware('web')
            //     ->namespace('App\Http\Controllers')
            //     ->group(base_path('routes/agent.php'));
            
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/adminconsole.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/sms.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/elite.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'checkDobSession' => \App\Http\Middleware\CheckDobSession::class,
        ]);
        
        // CSRF Token Exceptions for AJAX routes and webhooks
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'webhooks/sms/*',
            'elite/emails',
            'update_visit_purpose',
            'update_visit_comment',
            'attend_session',
            'complete_session',
            'update_task_comment',
            'update_task_description',
            'update_task_status',
            'update_task_priority',
            'updateduedate',
            'application/checklistupload',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Laravel's ValidatePostSize runs before route middleware — log 413 here, not only in route middleware.
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request): ?\Symfony\Component\HttpFoundation\Response {
            $is413 = false;
            if ($e instanceof PostTooLargeException) {
                $is413 = true;
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $is413 = $e->getStatusCode() === 413;
            }
            if (! $is413 || ! $request->is('elite/emails') || ! $request->isMethod('POST')) {
                return null;
            }

            $postMaxIni = ini_get('post_max_size');
            $parseIniBytes = static function (?string $val): int {
                if ($val === null || $val === '') {
                    return 0;
                }
                $val = trim($val);
                if (is_numeric($val)) {
                    return (int) $val;
                }
                $metric = strtoupper(substr($val, -1));
                $n = (int) $val;

                return match ($metric) {
                    'K' => $n * 1024,
                    'M' => $n * 1048576,
                    'G' => $n * 1073741824,
                    default => $n,
                };
            };
            $postMaxBytes = $parseIniBytes(is_string($postMaxIni) ? $postMaxIni : null);

            Log::warning('elite.inbound.payload_too_large', [
                'reason_code' => 'payload_too_large',
                'source' => 'exception_render',
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
                'forwarded_for' => $request->header('X-Forwarded-For'),
                'content_length_header' => $request->header('Content-Length'),
                'content_length_server' => $request->server('CONTENT_LENGTH'),
                'php_post_max_size' => is_string($postMaxIni) ? $postMaxIni : null,
                'php_post_max_bytes' => $postMaxBytes,
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'user_agent' => $request->userAgent() !== null ? substr((string) $request->userAgent(), 0, 200) : null,
            ]);

            return null;
        });
    })->create();
