<?php

use App\Http\Controllers\Elite\EliteEmailController;
use App\Http\Middleware\LogEliteInboundWebhookRejections;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Education Elite email
|--------------------------------------------------------------------------
|
| POST /emails/elite — legacy inbound webhook (optional). Primary inbound: SES → S3 → ses:sync-inbound.
| All GET routes — admin login required (UI, JSON inbox/sent/drafts, attachment downloads for img src)
|
| Legacy POST /elite/emails kept for old webhook URLs.
|
*/

$eliteInboundMiddleware = [
    LogEliteInboundWebhookRejections::class,
    'throttle:600,1',
];

Route::prefix('emails')->name('elite.')->group(function () use ($eliteInboundMiddleware) {
    Route::post('/elite', [EliteEmailController::class, 'store'])
        ->middleware($eliteInboundMiddleware)
        ->name('emails.store');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/elite/attachments/{attachment}', [EliteEmailController::class, 'attachment'])
            ->name('emails.attachment');

        Route::get('/elite/{eliteEmail}/message-body', [EliteEmailController::class, 'messageBody'])
            ->name('emails.message-body');

        Route::get('/elite', [EliteEmailController::class, 'index'])->name('emails.index');
        Route::get('/elite/inbox', [EliteEmailController::class, 'inbox'])->name('emails.inbox');
        Route::get('/elite/sent', [EliteEmailController::class, 'sent'])->name('emails.sent');
        Route::get('/elite/drafts', [EliteEmailController::class, 'drafts'])->name('emails.drafts');
    });
});

Route::post('/elite/emails', [EliteEmailController::class, 'store'])
    ->middleware($eliteInboundMiddleware);
