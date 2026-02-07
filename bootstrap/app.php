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
        //
    })->create();
