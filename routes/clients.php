<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientsController;

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
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    // REMOVED: Direct client creation - clients must be created via lead conversion
    // Route::get('/clients/create', [ClientsController::class, 'create'])->name('clients.create');
    // Route::post('/clients/store', [ClientsController::class, 'store'])->name('clients.store');
    Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');
    Route::post('/clients/edit', [ClientsController::class, 'edit'])->name('clients.update');
    
    // Fallback route: redirect GET requests to edit without ID back to clients list
    Route::get('/clients/edit', function() {
        return redirect()->route('clients.index')->with('error', 'Please select a client to edit');
    });
    
    // Detail routes
    Route::get('/clients/detail/{id}', [ClientsController::class, 'clientdetail'])->name('clients.detail');
    Route::get('/leads/detail/{id}', [ClientsController::class, 'leaddetail'])->name('leads.detail');
    
    // Status views
    Route::get('/prospects', [ClientsController::class, 'prospects'])->name('clients.prospects');
    Route::get('/archived', [ClientsController::class, 'archived'])->name('clients.archived');
    
    // Follow-up routes
    Route::post('/clients/followup/store', [ClientsController::class, 'followupstore'])->name('clients.followup.store');
    Route::post('/clients/followup_application/store_application', [ClientsController::class, 'followupstore_application'])->name('clients.followup.store_application');
    Route::post('/clients/followup/retagfollowup', [ClientsController::class, 'retagfollowup'])->name('clients.followup.retagfollowup');
    Route::post('/clients/personalfollowup/store', [ClientsController::class, 'personalfollowup'])->name('clients.personalfollowup.store');
    Route::post('/clients/updatefollowup/store', [ClientsController::class, 'updatefollowup'])->name('clients.updatefollowup.store');
    Route::post('/clients/reassignfollowup/store', [ClientsController::class, 'reassignfollowupstore'])->name('clients.reassignfollowup.store');
    Route::post('/updatefollowupschedule', [ClientsController::class, 'updatefollowupschedule'])->name('clients.updatefollowupschedule');
    
    // Client management
    Route::get('/clients/changetype/{id}/{type}', [ClientsController::class, 'changetype'])->name('clients.changetype');
    Route::get('/clients/removetag', [ClientsController::class, 'removetag'])->name('clients.removetag');
    Route::get('/clients/change_assignee', [ClientsController::class, 'change_assignee'])->name('clients.change_assignee');
    Route::get('/change-client-status', [ClientsController::class, 'updateclientstatus'])->name('clients.updateclientstatus');
    Route::post('/save_tag', [ClientsController::class, 'save_tag'])->name('clients.save_tag');
    
    // AJAX routes - recipient/search
    Route::get('/clients/get-recipients', [ClientsController::class, 'getrecipients'])->name('clients.getrecipients');
    Route::get('/clients/get-onlyclientrecipients', [ClientsController::class, 'getonlyclientrecipients'])->name('clients.getonlyclientrecipients');
    Route::get('/clients/get-allclients', [ClientsController::class, 'getallclients'])
        ->name('clients.getallclients')
        ->middleware('throttle:60,1');
    
    // AJAX routes - notes
    Route::post('/create-note', [ClientsController::class, 'createnote'])->name('clients.createnote');
    Route::get('/getnotedetail', [ClientsController::class, 'getnotedetail'])->name('clients.getnotedetail');
    Route::get('/deletenote', [ClientsController::class, 'deletenote'])->name('clients.deletenote');
    Route::get('/viewnotedetail', [ClientsController::class, 'viewnotedetail'])->name('clients.viewnotedetail');
    Route::get('/viewapplicationnote', [ClientsController::class, 'viewapplicationnote'])->name('clients.viewapplicationnote');
    Route::get('/get-notes', [ClientsController::class, 'getnotes'])->name('clients.getnotes');
    Route::get('/pinnote', [ClientsController::class, 'pinnote'])->name('clients.pinnote');
    
    // AJAX routes - activities
    Route::get('/get-activities', [ClientsController::class, 'activities'])->name('clients.activities');
    Route::get('/deleteactivitylog', [ClientsController::class, 'deleteactivitylog'])->name('clients.deleteactivitylog');
    Route::post('/not-picked-call', [ClientsController::class, 'notpickedcall'])->name('clients.notpickedcall');
    Route::get('/pinactivitylog', [ClientsController::class, 'pinactivitylog'])->name('clients.pinactivitylog');
    
    // AJAX routes - applications
    Route::get('/get-application-lists', [ClientsController::class, 'getapplicationlists'])->name('clients.getapplicationlists');
    Route::post('/saveapplication', [ClientsController::class, 'saveapplication'])->name('clients.saveapplication');
    Route::get('/convertapplication', [ClientsController::class, 'convertapplication'])->name('clients.convertapplication');
    Route::get('/deleteservices', [ClientsController::class, 'deleteservices'])->name('clients.deleteservices');
    Route::post('/savetoapplication', [ClientsController::class, 'savetoapplication'])->name('clients.savetoapplication');
    
    // AJAX routes - documents
    Route::post('/upload-document', [ClientsController::class, 'uploaddocument'])->name('clients.uploaddocument');
    Route::get('/deletedocs', [ClientsController::class, 'deletedocs'])->name('clients.deletedocs');
    Route::post('/renamedoc', [ClientsController::class, 'renamedoc'])->name('clients.renamedoc');
    Route::get('/document/download/pdf/{id}', [ClientsController::class, 'downloadpdf'])->name('clients.downloadpdf');
    
    // AJAX routes - services
    Route::post('/interested-service', [ClientsController::class, 'interestedService'])->name('clients.interested-service');
    Route::post('/edit-interested-service', [ClientsController::class, 'editinterestedService'])->name('clients.edit-interested-service');
    Route::get('/get-services', [ClientsController::class, 'getServices'])->name('clients.get-services');
    Route::post('/upload-mail', [ClientsController::class, 'uploadmail'])->name('clients.uploadmail');
    Route::get('/getintrestedservice', [ClientsController::class, 'getintrestedservice'])->name('clients.getintrestedservice');
    Route::post('/application/saleforcastservice', [ClientsController::class, 'saleforcastservice'])->name('clients.saleforcastservice');
    Route::get('/getintrestedserviceedit', [ClientsController::class, 'getintrestedserviceedit'])->name('clients.getintrestedserviceedit');
    
    // Session/Check-in routes
    Route::post('/clients/update-session-completed', [ClientsController::class, 'updatesessioncompleted'])->name('clients.updatesessioncompleted');
    
    // Email/Contact routes
    Route::post('/clients/update-email-verified', [ClientsController::class, 'updateemailverified'])->name('clients.updateemailverified');
    Route::post('/email-verify', [ClientsController::class, 'emailVerify'])->name('emailVerify');
    Route::get('/email-verify-token/{token}', [ClientsController::class, 'emailVerifyToken'])->name('emailVerifyToken');
    Route::get('/thankyou', [ClientsController::class, 'thankyou'])->name('emailVerify.thankyou');
    Route::post('/clients/fetchClientContactNo', [ClientsController::class, 'fetchClientContactNo'])->name('clients.fetchClientContactNo');
    Route::post('/sendmsg', [ClientsController::class, 'sendmsg'])->name('clients.sendmsg');
    Route::post('/is_greview_mail_sent', [ClientsController::class, 'isgreviewmailsent'])->name('clients.isgreviewmailsent');
    Route::post('/mail/enhance', [ClientsController::class, 'enhanceMessage'])->name('clients.enhanceMessage');
    
    // Address routes
    Route::post('/address_auto_populate', [ClientsController::class, 'address_auto_populate'])->name('clients.address_auto_populate');
    
    // Service taken routes
    Route::post('/client/createservicetaken', [ClientsController::class, 'createservicetaken'])->name('clients.createservicetaken');
    Route::post('/client/removeservicetaken', [ClientsController::class, 'removeservicetaken'])->name('clients.removeservicetaken');
    Route::post('/client/getservicetaken', [ClientsController::class, 'getservicetaken'])->name('clients.getservicetaken');
    
    // Tag routes
    Route::get('/gettagdata', [ClientsController::class, 'gettagdata'])->name('clients.gettagdata');
    
    // Account/Receipt routes (Admin only - but accessible via unified route)
    Route::get('/clients/saveaccountreport/{id}', [ClientsController::class, 'saveaccountreport'])->name('clients.saveaccountreport');
    Route::post('/clients/saveaccountreport', [ClientsController::class, 'saveaccountreport'])->name('clients.saveaccountreport.update');
    Route::post('/clients/getTopReceiptValInDB', [ClientsController::class, 'getTopReceiptValInDB'])->name('clients.getTopReceiptValInDB');
    Route::get('/clients/printpreview/{id}', [ClientsController::class, 'printpreview'])->name('clients.printpreview');
    Route::post('/clients/getClientReceiptInfoById', [ClientsController::class, 'getClientReceiptInfoById'])->name('clients.getClientReceiptInfoById');
    Route::get('/clients/clientreceiptlist', [ClientsController::class, 'clientreceiptlist'])->name('clients.clientreceiptlist');
    Route::post('/validate_receipt', [ClientsController::class, 'validate_receipt'])->name('clients.validate_receipt');
    
    // Commission Report
    Route::get('/commissionreport', [ClientsController::class, 'commissionreport'])->name('clients.commissionreport');
    Route::post('/commissionreport/list', [ClientsController::class, 'getcommissionreport'])->name('clients.getcommissionreport');
    
    // Document checklist routes
    Route::post('/add-alldocchecklist', [ClientsController::class, 'addalldocchecklist'])->name('clients.addalldocchecklist');
    Route::post('/upload-alldocument', [ClientsController::class, 'uploadalldocument'])->name('clients.uploadalldocument');
    Route::post('/notuseddoc', [ClientsController::class, 'notuseddoc'])->name('clients.notuseddoc');
    Route::post('/renamechecklistdoc', [ClientsController::class, 'renamechecklistdoc'])->name('clients.renamechecklistdoc');
    Route::post('/verifydoc', [ClientsController::class, 'verifydoc'])->name('clients.verifydoc');
    Route::get('/deletealldocs', [ClientsController::class, 'deletealldocs'])->name('clients.deletealldocs');
    Route::post('/renamealldoc', [ClientsController::class, 'renamealldoc'])->name('clients.renamealldoc');
    Route::post('/backtodoc', [ClientsController::class, 'backtodoc'])->name('clients.backtodoc');
    
    // Download document
    Route::post('/download-document', [ClientsController::class, 'download_document'])->name('clients.download_document');
    
    // Bulk upload routes for Documents tab
    Route::post('/documents/bulk-upload', [ClientsController::class, 'bulkUploadDocuments'])->name('clients.documents.bulkUpload');
    Route::post('/documents/get-auto-checklist-matches', [ClientsController::class, 'getAutoChecklistMatches'])->name('clients.documents.getAutoChecklistMatches');
    
    // Merge records
    Route::post('/merge_records', [ClientsController::class, 'merge_records'])->name('clients.merge_records');
});

