<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminConsole\Sms\SmsWebhookController;

/*
|--------------------------------------------------------------------------
| SMS Webhook Routes
|--------------------------------------------------------------------------
|
| Webhook routes for Twilio and Cellcast (no authentication).
|
*/

Route::prefix('webhooks/sms')->name('webhooks.sms.')->middleware('web')->group(function () {
    Route::post('/twilio/status', [SmsWebhookController::class, 'twilioStatus'])->name('twilio.status');
    Route::post('/twilio/incoming', [SmsWebhookController::class, 'twilioIncoming'])->name('twilio.incoming');
    Route::post('/cellcast/status', [SmsWebhookController::class, 'cellcastStatus'])->name('cellcast.status');
    Route::post('/cellcast/incoming', [SmsWebhookController::class, 'cellcastIncoming'])->name('cellcast.incoming');
});
