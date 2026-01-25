<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Client\ClientController;
use App\Http\Controllers\Admin\Client\ClientActionController;
use App\Http\Controllers\Admin\Client\ClientNoteController;
use App\Http\Controllers\Admin\Client\ClientActivityController;
use App\Http\Controllers\Admin\Client\ClientApplicationController;
use App\Http\Controllers\Admin\Client\ClientServiceController;
use App\Http\Controllers\Admin\Client\ClientDocumentController;
use App\Http\Controllers\Admin\Client\DocumentCategoryController;
use App\Http\Controllers\Admin\Client\ClientMessagingController;
use App\Http\Controllers\Admin\Client\ClientReceiptController;
use App\Http\Controllers\Admin\Client\ClientMergeController;

/*
|--------------------------------------------------------------------------
| Unified Client Routes
|--------------------------------------------------------------------------
|
| Routes accessible by admin users only (auth:admin)
| All routes use the unified route names (clients.*) instead of admin.clients.*
|
*/

Route::middleware(['auth:admin'])->group(function() {
    
    // Main CRUD routes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/edit/{id}', [ClientController::class, 'edit'])->name('clients.edit');
    Route::post('/clients/edit', [ClientController::class, 'edit'])->name('clients.update');
    
    // Export/Import routes
    Route::get('/clients/export/{id}', [ClientController::class, 'export'])->name('clients.export');
    Route::post('/clients/import', [ClientController::class, 'import'])->name('clients.import');
    
    // Fallback route: redirect GET requests to edit without ID back to clients list
    Route::get('/clients/edit', function() {
        return redirect()->route('clients.index')->with('error', 'Please select a client to edit');
    });
    
    // Detail routes
    Route::get('/clients/detail/{id}/application/{applicationId}', [ClientController::class, 'clientdetail'])->name('clients.detail.application');
    Route::get('/clients/detail/{id}/{tab?}', [ClientController::class, 'clientdetail'])->name('clients.detail');
    Route::get('/leads/detail/{id}/application/{applicationId}', [ClientController::class, 'leaddetail'])->name('leads.detail.application');
    Route::get('/leads/detail/{id}/{tab?}', [ClientController::class, 'leaddetail'])->name('leads.detail');
    
    // Status views
    Route::get('/archived', [ClientController::class, 'archived'])->name('clients.archived');
    
    // Follow-up routes
    Route::post('/clients/followup/store', [ClientActionController::class, 'followupstore'])->name('clients.followup.store');
    Route::post('/clients/followup_application/store_application', [ClientActionController::class, 'followupstore_application'])->name('clients.followup.store_application');
    Route::post('/clients/followup/retagfollowup', [ClientActionController::class, 'retagfollowup'])->name('clients.followup.retagfollowup');
    Route::post('/clients/personalfollowup/store', [ClientActionController::class, 'personalfollowup'])->name('clients.personalfollowup.store');
    Route::post('/clients/updatefollowup/store', [ClientActionController::class, 'updatefollowup'])->name('clients.updatefollowup.store');
    Route::post('/clients/reassignfollowup/store', [ClientActionController::class, 'reassignfollowupstore'])->name('clients.reassignfollowup.store');
    
    // Client management
    Route::get('/clients/changetype/{id}/{type}', [ClientController::class, 'changetype'])->name('clients.changetype');
    Route::get('/clients/removetag', [ClientController::class, 'removetag'])->name('clients.removetag');
    Route::get('/clients/change_assignee', [ClientController::class, 'change_assignee'])->name('clients.change_assignee');
    Route::get('/change-client-status', [ClientController::class, 'updateclientstatus'])->name('clients.updateclientstatus');
    Route::post('/save_tag', [ClientController::class, 'save_tag'])->name('clients.save_tag');
    
    // AJAX routes - recipient/search
    Route::get('/clients/get-recipients', [ClientController::class, 'getrecipients'])->name('clients.getrecipients');
    Route::get('/clients/get-onlyclientrecipients', [ClientController::class, 'getonlyclientrecipients'])->name('clients.getonlyclientrecipients');
    Route::get('/clients/get-allclients', [ClientController::class, 'getallclients'])
        ->name('clients.getallclients')
        ->middleware('throttle:60,1');
    
    // AJAX routes - notes
    Route::post('/create-note', [ClientNoteController::class, 'createnote'])->name('clients.createnote');
    Route::get('/getnotedetail', [ClientNoteController::class, 'getnotedetail'])->name('clients.getnotedetail');
    Route::get('/deletenote', [ClientNoteController::class, 'deletenote'])->name('clients.deletenote');
    Route::get('/viewnotedetail', [ClientNoteController::class, 'viewnotedetail'])->name('clients.viewnotedetail');
    Route::get('/viewapplicationnote', [ClientNoteController::class, 'viewapplicationnote'])->name('clients.viewapplicationnote');
    Route::get('/get-notes', [ClientNoteController::class, 'getnotes'])->name('clients.getnotes');
    Route::get('/pinnote', [ClientNoteController::class, 'pinnote'])->name('clients.pinnote');
    
    // AJAX routes - activities
    Route::get('/get-activities', [ClientActivityController::class, 'activities'])->name('clients.activities');
    Route::get('/deleteactivitylog', [ClientActivityController::class, 'deleteactivitylog'])->name('clients.deleteactivitylog');
    Route::post('/not-picked-call', [ClientActivityController::class, 'notpickedcall'])->name('clients.notpickedcall');
    Route::get('/pinactivitylog', [ClientActivityController::class, 'pinactivitylog'])->name('clients.pinactivitylog');
    
    // AJAX routes - applications
    Route::get('/get-application-lists', [ClientApplicationController::class, 'getapplicationlists'])->name('clients.getapplicationlists');
    Route::post('/saveapplication', [ClientApplicationController::class, 'saveapplication'])->name('clients.saveapplication');
    Route::get('/convertapplication', [ClientApplicationController::class, 'convertapplication'])->name('clients.convertapplication');
    Route::get('/deleteservices', [ClientApplicationController::class, 'deleteservices'])->name('clients.deleteservices');
    Route::post('/savetoapplication', [ClientServiceController::class, 'savetoapplication'])->name('clients.savetoapplication');
    
    // AJAX routes - documents
    Route::post('/upload-document', [ClientDocumentController::class, 'uploaddocument'])->name('clients.uploaddocument');
    Route::get('/deletedocs', [ClientDocumentController::class, 'deletedocs'])->name('clients.deletedocs');
    Route::post('/renamedoc', [ClientDocumentController::class, 'renamedoc'])->name('clients.renamedoc');
    Route::get('/document/download/pdf/{id}', [ClientDocumentController::class, 'downloadpdf'])->name('clients.downloadpdf');
    
    // AJAX routes - services (interested services removed)
    // Route::post('/interested-service', [ClientServiceController::class, 'interestedService'])->name('clients.interested-service');
    // Route::post('/edit-interested-service', [ClientServiceController::class, 'editinterestedService'])->name('clients.edit-interested-service');
    // Route::get('/get-services', [ClientServiceController::class, 'getServices'])->name('clients.get-services');
    Route::post('/upload-mail', [ClientMessagingController::class, 'uploadmail'])->name('clients.uploadmail');
    // Route::get('/getintrestedservice', [ClientServiceController::class, 'getintrestedservice'])->name('clients.getintrestedservice');
    // Route::post('/application/saleforcastservice', [ClientServiceController::class, 'saleforcastservice'])->name('clients.saleforcastservice');
    // Route::get('/getintrestedserviceedit', [ClientServiceController::class, 'getintrestedserviceedit'])->name('clients.getintrestedserviceedit');
    
    // Session/Check-in routes
    Route::post('/clients/update-session-completed', [ClientController::class, 'updatesessioncompleted'])->name('clients.updatesessioncompleted');
    
    // Email/Contact routes
    Route::post('/clients/update-email-verified', [ClientMessagingController::class, 'updateemailverified'])->name('clients.updateemailverified');
    Route::post('/email-verify', [ClientMessagingController::class, 'emailVerify'])->name('emailVerify');
    Route::post('/clients/fetchClientContactNo', [ClientMessagingController::class, 'fetchClientContactNo'])->name('clients.fetchClientContactNo');
    Route::post('/sendmsg', [ClientMessagingController::class, 'sendmsg'])->name('clients.sendmsg');
    Route::post('/is_greview_mail_sent', [ClientMessagingController::class, 'isgreviewmailsent'])->name('clients.isgreviewmailsent');
    Route::post('/mail/enhance', [ClientMessagingController::class, 'enhanceMessage'])->name('clients.enhanceMessage');
    
    // Address routes
    Route::post('/address_auto_populate', [ClientController::class, 'address_auto_populate'])->name('clients.address_auto_populate');
    
    // Service taken routes
    Route::post('/client/createservicetaken', [ClientServiceController::class, 'createservicetaken'])->name('clients.createservicetaken');
    Route::post('/client/removeservicetaken', [ClientServiceController::class, 'removeservicetaken'])->name('clients.removeservicetaken');
    Route::post('/client/getservicetaken', [ClientServiceController::class, 'getservicetaken'])->name('clients.getservicetaken');
    
    // Tag routes
    Route::get('/gettagdata', [ClientServiceController::class, 'gettagdata'])->name('clients.gettagdata');
    
    // Account/Receipt routes (Admin only - but accessible via unified route)
    Route::get('/clients/saveaccountreport/{id}', [ClientReceiptController::class, 'saveaccountreport'])->name('clients.saveaccountreport');
    Route::post('/clients/saveaccountreport', [ClientReceiptController::class, 'saveaccountreport'])->name('clients.saveaccountreport.update');
    Route::post('/clients/getTopReceiptValInDB', [ClientReceiptController::class, 'getTopReceiptValInDB'])->name('clients.getTopReceiptValInDB');
    Route::get('/clients/printpreview/{id}', [ClientReceiptController::class, 'printpreview'])->name('clients.printpreview');
    Route::post('/clients/getClientReceiptInfoById', [ClientReceiptController::class, 'getClientReceiptInfoById'])->name('clients.getClientReceiptInfoById');
    Route::get('/clients/clientreceiptlist', [ClientReceiptController::class, 'clientreceiptlist'])->name('clients.clientreceiptlist');
    Route::post('/validate_receipt', [ClientReceiptController::class, 'validate_receipt'])->name('clients.validate_receipt');
    
    // Commission Report
    Route::get('/commissionreport', [ClientReceiptController::class, 'commissionreport'])->name('clients.commissionreport');
    Route::post('/commissionreport/list', [ClientReceiptController::class, 'getcommissionreport'])->name('clients.getcommissionreport');
    
    // Document checklist routes
    Route::post('/add-alldocchecklist', [ClientDocumentController::class, 'addalldocchecklist'])->name('clients.addalldocchecklist');
    Route::post('/upload-alldocument', [ClientDocumentController::class, 'uploadalldocument'])->name('clients.uploadalldocument');
    Route::post('/notuseddoc', [ClientDocumentController::class, 'notuseddoc'])->name('clients.notuseddoc');
    Route::post('/renamechecklistdoc', [ClientDocumentController::class, 'renamechecklistdoc'])->name('clients.renamechecklistdoc');
    Route::post('/verifydoc', [ClientDocumentController::class, 'verifydoc'])->name('clients.verifydoc');
    Route::get('/deletealldocs', [ClientDocumentController::class, 'deletealldocs'])->name('clients.deletealldocs');
    Route::post('/renamealldoc', [ClientDocumentController::class, 'renamealldoc'])->name('clients.renamealldoc');
    Route::post('/backtodoc', [ClientDocumentController::class, 'backtodoc'])->name('clients.backtodoc');
    
    // Download document (allow GET fallback for non-JS links)
    Route::match(['get', 'post'], '/download-document', [ClientDocumentController::class, 'download_document'])
        ->name('clients.download_document');
    
    // Bulk upload routes for Documents tab
    Route::post('/documents/bulk-upload', [ClientDocumentController::class, 'bulkUploadDocuments'])->name('clients.documents.bulkUpload');
    Route::post('/documents/get-auto-checklist-matches', [ClientDocumentController::class, 'getAutoChecklistMatches'])->name('clients.documents.getAutoChecklistMatches');
    
    // Document Category routes (AJAX)
    Route::get('/document-categories/get', [DocumentCategoryController::class, 'getCategories'])->name('clients.documentcategories.get');
    Route::post('/document-categories/store', [DocumentCategoryController::class, 'store'])->name('clients.documentcategories.store');
    Route::post('/document-categories/update/{id}', [DocumentCategoryController::class, 'update'])->name('clients.documentcategories.update');
    Route::delete('/document-categories/{id}', [DocumentCategoryController::class, 'destroy'])->name('clients.documentcategories.destroy');
    Route::get('/document-categories/documents', [DocumentCategoryController::class, 'getDocuments'])->name('clients.documentcategories.documents');
    
    // Merge records
    Route::post('/merge_records', [ClientMergeController::class, 'merge_records'])->name('clients.merge_records');
    
    // Email V2 routes (separate from legacy email system)
    Route::prefix('email-v2')->name('email-v2.')->group(function() {
        Route::post('/upload-inbox', [\App\Http\Controllers\CRM\EmailUploadV2Controller::class, 'uploadInboxEmails'])->name('upload.inbox');
        Route::post('/upload-sent', [\App\Http\Controllers\CRM\EmailUploadV2Controller::class, 'uploadSentEmails'])->name('upload.sent');
        Route::get('/check-service', [\App\Http\Controllers\CRM\EmailUploadV2Controller::class, 'checkPythonService'])->name('check.service');
        Route::post('/filter-emails', [\App\Http\Controllers\CRM\EmailQueryV2Controller::class, 'filterEmails'])->name('filter.emails');
        Route::post('/filter-sentemails', [\App\Http\Controllers\CRM\EmailQueryV2Controller::class, 'filterSentEmails'])->name('filter.sentemails');
        
        // Email Labels routes
        Route::prefix('labels')->name('labels.')->group(function() {
            Route::get('/', [\App\Http\Controllers\CRM\EmailLabelV2Controller::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\CRM\EmailLabelV2Controller::class, 'store'])->name('store');
            Route::post('/apply', [\App\Http\Controllers\CRM\EmailLabelV2Controller::class, 'apply'])->name('apply');
            Route::delete('/remove', [\App\Http\Controllers\CRM\EmailLabelV2Controller::class, 'remove'])->name('remove');
        });
        
        // Email Attachments routes
        Route::prefix('attachments')->name('attachments.')->group(function() {
            Route::get('/{id}/download', [\App\Http\Controllers\CRM\MailReportAttachmentController::class, 'download'])->name('download');
            Route::get('/{id}/preview', [\App\Http\Controllers\CRM\MailReportAttachmentController::class, 'preview'])->name('preview');
            Route::get('/email/{mailReportId}/download-all', [\App\Http\Controllers\CRM\MailReportAttachmentController::class, 'downloadAll'])->name('download-all');
        });
    });
});

