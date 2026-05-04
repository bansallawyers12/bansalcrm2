<?php

use App\Http\Controllers\Elite\EliteEmailController;
use App\Http\Middleware\LogEliteInboundWebhookRejections;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Education Elite email
|--------------------------------------------------------------------------
|
| POST /elite/emails — SendGrid Inbound Parse (public, CSRF exempt, optional ?secret=)
| GET /elite/emails — Inbox UI (public; drafts require admin session)
|
*/

Route::prefix('elite')->name('elite.')->group(function () {
    Route::post('/emails', [EliteEmailController::class, 'store'])
        ->middleware([
            LogEliteInboundWebhookRejections::class,
            'throttle:600,1',
        ])
        ->name('emails.store');

    Route::get('/emails', [EliteEmailController::class, 'index'])->name('emails.index');
    Route::get('/emails/inbox', [EliteEmailController::class, 'inbox'])->name('emails.inbox');
    Route::get('/emails/sent', [EliteEmailController::class, 'sent'])->name('emails.sent');
    Route::get('/emails/drafts', [EliteEmailController::class, 'drafts'])->name('emails.drafts');
});