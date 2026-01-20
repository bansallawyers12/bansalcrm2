<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\SignatureDashboardController;
use App\Http\Controllers\PublicDocumentController;

/*
|--------------------------------------------------------------------------
| Document Signature Routes
|--------------------------------------------------------------------------
|
| WORKFLOW:
| 1. Admin prepares document for signing (CRUD operations)
| 2. Admin sends signing link via email to client
| 3. Client receives email with link: /sign/{id}/{token}
| 4. Client signs document (no login - token validated)
| 5. Client sees thank you page & downloads signed document
| 6. Admin views completed document in admin panel
|
*/

/*
|--------------------------------------------------------------------------
| PUBLIC DOCUMENT SIGNING ROUTES
|--------------------------------------------------------------------------
| No authentication required - access controlled by token validation
*/

// Public Signing Interface
Route::get('/sign/{id}/{token}', [PublicDocumentController::class, 'sign'])
    ->name('public.documents.sign');

Route::post('/documents/{document}/sign', [PublicDocumentController::class, 'submitSignatures'])
    ->name('public.documents.submitSignatures');

// Public Document Viewing
Route::get('/documents/{id}/page/{page}', [PublicDocumentController::class, 'getPage'])
    ->name('public.documents.page');

Route::get('/documents/{id}/info', [PublicDocumentController::class, 'getDocumentInfo'])
    ->name('public.documents.info');

Route::get('/documents/{id?}', [PublicDocumentController::class, 'index'])
    ->name('public.documents.index');

// Public Download & Thank You
Route::get('/documents/{id}/download-signed', [PublicDocumentController::class, 'downloadSigned'])
    ->name('public.documents.download.signed');

Route::get('/documents/thankyou/{id?}', [PublicDocumentController::class, 'thankyou'])
    ->name('public.documents.thankyou');

// Public Reminder
Route::post('/documents/{document}/send-reminder', [PublicDocumentController::class, 'sendReminder'])
    ->name('public.documents.sendReminder');

/*
|--------------------------------------------------------------------------
| ADMIN SIGNATURE DASHBOARD ROUTES
|--------------------------------------------------------------------------
| Requires admin authentication
*/

Route::middleware('auth:admin')->group(function () {

    // Signature Dashboard Routes
    Route::prefix('signatures')->group(function () {
        Route::get('/', [SignatureDashboardController::class, 'index'])->name('signatures.index');
        Route::get('/create', [SignatureDashboardController::class, 'create'])->name('signatures.create');
        Route::post('/', [SignatureDashboardController::class, 'store'])->name('signatures.store');
        Route::post('/suggest-association', [SignatureDashboardController::class, 'suggestAssociation'])->name('signatures.suggest-association');
        
        // Bulk actions
        Route::post('/bulk-archive', [SignatureDashboardController::class, 'bulkArchive'])->name('signatures.bulk-archive');
        Route::post('/bulk-void', [SignatureDashboardController::class, 'bulkVoid'])->name('signatures.bulk-void');
        Route::post('/bulk-resend', [SignatureDashboardController::class, 'bulkResend'])->name('signatures.bulk-resend');
        
        Route::get('/{id}', [SignatureDashboardController::class, 'show'])->name('signatures.show');
        Route::get('/{id}/edit', [SignatureDashboardController::class, 'edit'])->name('signatures.edit');
        Route::post('/{id}/save-fields', [SignatureDashboardController::class, 'saveSignatureFields'])->name('signatures.save-fields');
        Route::post('/{id}/reminder', [SignatureDashboardController::class, 'sendReminder'])->name('signatures.reminder');
        Route::post('/{id}/cancel', [SignatureDashboardController::class, 'cancelSignature'])->name('signatures.cancel');
        Route::post('/{id}/send', [SignatureDashboardController::class, 'sendForSignature'])->name('signatures.send');
        Route::get('/{id}/copy-link', [SignatureDashboardController::class, 'copyLink'])->name('signatures.copy-link');
        
        // Association management
        Route::post('/{id}/associate', [SignatureDashboardController::class, 'associate'])->name('signatures.associate');
        Route::post('/{id}/detach', [SignatureDashboardController::class, 'detach'])->name('signatures.detach');
    });

});
