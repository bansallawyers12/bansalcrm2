<?php

use App\Http\Controllers\Elite\EliteEmailController;
use App\Http\Middleware\LogEliteInboundWebhookRejections;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Education Elite email
|--------------------------------------------------------------------------
|
| POST /elite/emails — SendGrid Inbound Parse only: MUST stay public (no auth), CSRF exempt, optional ?secret=
| All GET routes — admin login required (UI, JSON inbox/sent/drafts, attachment downloads for img src)
|
*/

Route::prefix('elite')->name('elite.')->group(function () {
    Route::post('/emails', [EliteEmailController::class, 'store'])
        ->middleware([
            LogEliteInboundWebhookRejections::class,
            'throttle:600,1',
        ])
        ->name('emails.store');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/emails/attachments/{attachment}', [EliteEmailController::class, 'attachment'])
            ->name('emails.attachment');

        Route::get('/emails', [EliteEmailController::class, 'index'])->name('emails.index');
        Route::get('/emails/inbox', [EliteEmailController::class, 'inbox'])->name('emails.inbox');
        Route::get('/emails/sent', [EliteEmailController::class, 'sent'])->name('emails.sent');
        Route::get('/emails/drafts', [EliteEmailController::class, 'drafts'])->name('emails.drafts');
    });
});