<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
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
            
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/agent.php'));
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
        
        // CSRF Token Exceptions for AJAX routes
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'admin/update_visit_purpose',
            'admin/update_visit_comment',
            'admin/attend_session',
            'admin/complete_session',
            'admin/update_task_comment',
            'admin/update_task_description',
            'admin/update_task_status',
            'admin/update_task_priority',
            'admin/updateduedate',
            'admin/application/checklistupload',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
