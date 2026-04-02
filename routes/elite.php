<?php

use App\Http\Controllers\Elite\EliteEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Education Elite inbound email (public webhook + inbox UI)
|--------------------------------------------------------------------------
*/

Route::prefix('elite')->name('elite.')->group(function () {
    Route::get('/emails', [EliteEmailController::class, 'index'])->name('emails.index');
    Route::get('/emails/inbox', [EliteEmailController::class, 'inbox'])->name('emails.inbox');
    Route::post('/emails', [EliteEmailController::class, 'store'])->name('emails.store');
});
