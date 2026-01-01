<?php

/*
|--------------------------------------------------------------------------
| Admin Console Routes
|--------------------------------------------------------------------------
|
| This file contains routes specifically for the Admin Console section,
| which is separate from the main admin panel. These routes handle
| feature management functionality like Product Types, Profiles, etc.
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('adminconsole')->middleware('auth:admin')->group(function() {
    
    //Product Type Routes
    Route::get('/product-type', 'AdminConsole\ProductTypeController@index')->name('adminconsole.producttype.index');  
    Route::get('/product-type/create', 'AdminConsole\ProductTypeController@create')->name('adminconsole.producttype.create');  
    Route::post('/product-type/store', 'AdminConsole\ProductTypeController@store')->name('adminconsole.producttype.store');   
    Route::get('/product-type/edit/{id}', 'AdminConsole\ProductTypeController@edit')->name('adminconsole.producttype.edit');
    Route::post('/product-type/edit', 'AdminConsole\ProductTypeController@edit');
    
    //Profile Routes
    Route::get('/profiles', 'AdminConsole\ProfileController@index')->name('adminconsole.profiles.index');  
    Route::get('/profiles/create', 'AdminConsole\ProfileController@create')->name('adminconsole.profiles.create');  
    Route::post('/profiles/store', 'AdminConsole\ProfileController@store')->name('adminconsole.profiles.store');  
    Route::get('/profiles/edit/{id}', 'AdminConsole\ProfileController@edit')->name('adminconsole.profiles.edit');
    Route::post('/profiles/edit', 'AdminConsole\ProfileController@edit');
    
    //Partner Type Routes
    Route::get('/partner-type', 'AdminConsole\PartnerTypeController@index')->name('adminconsole.partnertype.index');  
    Route::get('/partner-type/create', 'AdminConsole\PartnerTypeController@create')->name('adminconsole.partnertype.create');  
    Route::post('/partner-type/store', 'AdminConsole\PartnerTypeController@store')->name('adminconsole.partnertype.store');   
    Route::get('/partner-type/edit/{id}', 'AdminConsole\PartnerTypeController@edit')->name('adminconsole.partnertype.edit');
    Route::post('/partner-type/edit', 'AdminConsole\PartnerTypeController@edit');
    
    //Visa Type Routes
    Route::get('/visa-type', 'AdminConsole\VisaTypeController@index')->name('adminconsole.visatype.index');  
    Route::get('/visa-type/create', 'AdminConsole\VisaTypeController@create')->name('adminconsole.visatype.create');  
    Route::post('/visa-type/store', 'AdminConsole\VisaTypeController@store')->name('adminconsole.visatype.store');     
    Route::get('/visa-type/edit/{id}', 'AdminConsole\VisaTypeController@edit')->name('adminconsole.visatype.edit');
    Route::post('/visa-type/edit', 'AdminConsole\VisaTypeController@edit');
    
    //Master Category Routes
    Route::get('/master-category', 'AdminConsole\MasterCategoryController@index')->name('adminconsole.mastercategory.index');  
    Route::get('/master-category/create', 'AdminConsole\MasterCategoryController@create')->name('adminconsole.mastercategory.create');  
    Route::post('/master-category/store', 'AdminConsole\MasterCategoryController@store')->name('adminconsole.mastercategory.store');     
    Route::get('/master-category/edit/{id}', 'AdminConsole\MasterCategoryController@edit')->name('adminconsole.mastercategory.edit');
    Route::post('/master-category/edit', 'AdminConsole\MasterCategoryController@edit');
    
    //Lead Service Routes
    Route::get('/lead-service', 'AdminConsole\LeadServiceController@index')->name('adminconsole.leadservice.index');  
    Route::get('/lead-service/create', 'AdminConsole\LeadServiceController@create')->name('adminconsole.leadservice.create');  
    Route::post('/lead-service/store', 'AdminConsole\LeadServiceController@store')->name('adminconsole.leadservice.store');     
    Route::get('/lead-service/edit/{id}', 'AdminConsole\LeadServiceController@edit')->name('adminconsole.leadservice.edit');
    Route::post('/lead-service/edit', 'AdminConsole\LeadServiceController@edit');
    
    // NOTE: Tax routes have been removed
    // TaxController and taxes table have been dropped
    
    //Subject Area Routes
    Route::get('/subjectarea', 'AdminConsole\SubjectAreaController@index')->name('adminconsole.subjectarea.index');  
    Route::get('/subjectarea/create', 'AdminConsole\SubjectAreaController@create')->name('adminconsole.subjectarea.create');  
    Route::post('/subjectarea/store', 'AdminConsole\SubjectAreaController@store')->name('adminconsole.subjectarea.store');  
    Route::get('/subjectarea/edit/{id}', 'AdminConsole\SubjectAreaController@edit')->name('adminconsole.subjectarea.edit');
    Route::post('/subjectarea/edit', 'AdminConsole\SubjectAreaController@edit');
    
    //Subject Routes
    Route::get('/subject', 'AdminConsole\SubjectController@index')->name('adminconsole.subject.index');
    Route::get('/subject/create', 'AdminConsole\SubjectController@create')->name('adminconsole.subject.create');  
    Route::post('/subject/store', 'AdminConsole\SubjectController@store')->name('adminconsole.subject.store');  
    Route::get('/subject/edit/{id}', 'AdminConsole\SubjectController@edit')->name('adminconsole.subject.edit');
    Route::post('/subject/edit', 'AdminConsole\SubjectController@edit');
    
    //Source Routes
    Route::get('/source', 'AdminConsole\SourceController@index')->name('adminconsole.source.index');  
    Route::get('/source/create', 'AdminConsole\SourceController@create')->name('adminconsole.source.create');  
    Route::post('source/store', 'AdminConsole\SourceController@store')->name('adminconsole.source.store');     
    Route::get('/source/edit/{id}', 'AdminConsole\SourceController@edit')->name('adminconsole.source.edit');
    Route::post('/source/edit', 'AdminConsole\SourceController@edit');
    
    //Tags Routes
    Route::get('/tags', 'AdminConsole\TagController@index')->name('adminconsole.tags.index');  
    Route::get('/tags/create', 'AdminConsole\TagController@create')->name('adminconsole.tags.create');  
    Route::post('tags/store', 'AdminConsole\TagController@store')->name('adminconsole.tags.store');     
    Route::get('/tags/edit/{id}', 'AdminConsole\TagController@edit')->name('adminconsole.tags.edit');
    Route::post('/tags/edit', 'AdminConsole\TagController@edit');
    
    //Checklist Routes
    Route::get('/checklist', 'AdminConsole\ChecklistController@index')->name('adminconsole.checklist.index');  
    Route::get('/checklist/create', 'AdminConsole\ChecklistController@create')->name('adminconsole.checklist.create');  
    Route::post('checklist/store', 'AdminConsole\ChecklistController@store')->name('adminconsole.checklist.store');     
    Route::get('/checklist/edit/{id}', 'AdminConsole\ChecklistController@edit')->name('adminconsole.checklist.edit');
    Route::post('/checklist/edit', 'AdminConsole\ChecklistController@edit')->name('adminconsole.checklist.update');
    
    //FeeType Routes
    Route::get('/feetype', 'AdminConsole\FeeTypeController@index')->name('adminconsole.feetype.index');  
    Route::get('/feetype/create', 'AdminConsole\FeeTypeController@create')->name('adminconsole.feetype.create');  
    Route::post('feetype/store', 'AdminConsole\FeeTypeController@store')->name('adminconsole.feetype.store');     
    Route::get('/feetype/edit/{id}', 'AdminConsole\FeeTypeController@edit')->name('adminconsole.feetype.edit');
    Route::post('/feetype/edit', 'AdminConsole\FeeTypeController@edit')->name('adminconsole.feetype.update');
    
    //Workflow Routes
    Route::get('/workflow', 'AdminConsole\WorkflowController@index')->name('adminconsole.workflow.index');  
    Route::get('/workflow/create', 'AdminConsole\WorkflowController@create')->name('adminconsole.workflow.create');  
    Route::post('workflow/store', 'AdminConsole\WorkflowController@store')->name('adminconsole.workflow.store');     
    Route::get('/workflow/edit/{id}', 'AdminConsole\WorkflowController@edit')->name('adminconsole.workflow.edit');
    Route::get('/workflow/deactivate-workflow/{id}', 'AdminConsole\WorkflowController@deactivateWorkflow')->name('adminconsole.workflow.deactivate');
    Route::get('/workflow/activate-workflow/{id}', 'AdminConsole\WorkflowController@activateWorkflow')->name('adminconsole.workflow.activate');
    Route::post('/workflow/edit', 'AdminConsole\WorkflowController@edit')->name('adminconsole.workflow.update');
    
    //Email Routes
    Route::get('/emails', 'AdminConsole\EmailController@index')->name('adminconsole.emails.index');  
    Route::get('/emails/create', 'AdminConsole\EmailController@create')->name('adminconsole.emails.create');  
    Route::post('emails/store', 'AdminConsole\EmailController@store')->name('adminconsole.emails.store');     
    Route::get('/emails/edit/{id}', 'AdminConsole\EmailController@edit')->name('adminconsole.emails.edit');
    Route::post('/emails/edit', 'AdminConsole\EmailController@edit')->name('adminconsole.emails.update');
    
    //Crm Email Template Routes
    Route::get('/crm_email_template', 'AdminConsole\CrmEmailTemplateController@index')->name('adminconsole.crmemailtemplate.index');  
    Route::get('/crm_email_template/create', 'AdminConsole\CrmEmailTemplateController@create')->name('adminconsole.crmemailtemplate.create');  
    Route::post('crm_email_template/store', 'AdminConsole\CrmEmailTemplateController@store')->name('adminconsole.crmemailtemplate.store');     
    Route::get('/crm_email_template/edit/{id}', 'AdminConsole\CrmEmailTemplateController@edit')->name('adminconsole.crmemailtemplate.edit');
    Route::post('/crm_email_template/edit', 'AdminConsole\CrmEmailTemplateController@edit')->name('adminconsole.crmemailtemplate.update');
    
    //Document Checklist Routes
    Route::get('/documentchecklist', 'AdminConsole\DocumentChecklistController@index')->name('adminconsole.documentchecklist.index');
    Route::get('/documentchecklist/create', 'AdminConsole\DocumentChecklistController@create')->name('adminconsole.documentchecklist.create');
    Route::post('/documentchecklist/store', 'AdminConsole\DocumentChecklistController@store')->name('adminconsole.documentchecklist.store');
    Route::get('/documentchecklist/edit/{id}', 'AdminConsole\DocumentChecklistController@edit')->name('adminconsole.documentchecklist.edit');
    Route::post('/documentchecklist/edit', 'AdminConsole\DocumentChecklistController@edit')->name('adminconsole.documentchecklist.update');
    
});
