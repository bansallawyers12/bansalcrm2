<?php

use App\Http\Controllers\Elite\EliteEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Education Elite email
|--------------------------------------------------------------------------
|
| POST /elite/emails — SendGrid Inbound Parse (public, CSRF exempt, optional ?secret=)
| GET /elite/emails — Inbox UI (auth:admin)
|
*/

Route::prefix('elite')->name('elite.')->group(function () {
    Route::post('/emails', [EliteEmailController::class, 'store'])
        ->middleware('throttle:600,1')
        ->name('emails.store');

    Route::get('/emails', [EliteEmailController::class, 'index'])->name('emails.index');
    Route::get('/emails/inbox', [EliteEmailController::class, 'inbox'])->name('emails.inbox');
    Route::get('/emails/sent', [EliteEmailController::class, 'sent'])->name('emails.sent');
    Route::get('/emails/drafts', [EliteEmailController::class, 'drafts'])->name('emails.drafts');
});