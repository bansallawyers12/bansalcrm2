<?php

use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\FollowupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\ProductsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*********************General Function for Both (Front-end & Back-end) ***********************/
/* Route::post('/get_states', 'HomeController@getStates');
Route::post('/get_product_views', 'HomeController@getProductViews');
Route::post('/get_product_other_info', 'HomeController@getProductOtherInformation');
Route::post('/delete_action', 'HomeController@deleteAction')->middleware('auth');
 */


Route::get('/clear-cache', function() {

    Artisan::call('config:clear');
	Artisan::call('view:clear');
   $exitCode = Artisan::call('route:clear');
   $exitCode = Artisan::call('route:cache');
     /* $exitCode = \Artisan::call('BirthDate:birthdate');
        $output = \Artisan::output();
        return $output;  */
    // return what you want
});

/*********************Exception Handling ***********************/
Route::match(['get', 'post'], '/exception', 'ExceptionController@index')->name('exception');
// Route::get('/import', 'TestController@import');
/*********************Front Panel Start ***********************/
//Coming Soon
// Route::get('/coming_soon', 'HomeController@coming_soon')->name('coming_soon');	

// Route::get('page/{slug}', 'HomeController@Page')->name('page.slug'); 

// Route::get('/enquiry', 'HomeController@enquiry')->name('enquiry');
// Route::post('/enquiry/store', 'HomeController@store')->name('enquiry.store');

// Route::get('sicaptcha', 'HomeController@sicaptcha')->name('sicaptcha'); 
// Route::get('invoice/secure/{slug}', 'InvoiceController@invoice')->name('invoice');   
// Route::get('/invoice/download/{id}', 'InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download'); 
// Route::get('/invoice/print/{id}', 'InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print');  
//Login and Register
// Legacy user authentication routes removed - system uses admin/agent authentication only

// Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
// //Forgot Password 
/* Route::get('/forgot_password', 'HomeController@forgotPassword')->name('forgot_password');	
Route::post('/forgot_password', 'HomeController@forgotPassword')->name('forgot_password');	

//Reset Link
Route::get('/reset_link/{token}', 'HomeController@resetLink')->name('reset_link');	
Route::post('/reset_link', 'HomeController@resetLink')->name('reset_link');	 */
 
//Review Panel
// Route::post('/add_review', 'DashboardController@addReview')->name('dashboard.add_review');
		
//Shipping Info 			
// Route::get('/address', 'DashboardController@address')->name('dashboard.address');
// Route::post('/address', 'DashboardController@address')->name('dashboard.address');

//Thankyou Page
// Route::get('/thankyou', 'PaymentController@thankyou')->name('payment.thankyou');

// //Inner Dashboard 
// Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
// Route::get('dashboard/view_order_summary/{id}', 'DashboardController@viewOrderSummary')->name('dashboard.view_order_summary');
// Route::get('/view_test_series_order/{id}', 'DashboardController@viewTestSeriesOrder')->name('dashboard.view_test_series_order');
// Route::post('/logout', 'DashboardController@logout')->name('logout');

// //Other Functions
// Route::get('/change_password', 'DashboardController@changePassword')->name('change_password');
// Route::post('/change_password', 'DashboardController@changePassword')->name('change_password');			
// Route::get('/edit_profile', 'DashboardController@editProfile')->name('dashboard.edit_profile');
// Route::post('/edit_profile', 'DashboardController@editProfile')->name('dashboard.edit_profile');

// Frontend website routes removed - methods deleted from HomeController
// Removed routes: index, testimonial, ourservices, servicesdetail, search_result, contactus, 
// contact, stripe, stripePost, bookappointment, getdatetime, refresh_captcha, myprofile

// Route::get('page/{slug}', 'HomeController@Page')->name('page.slug'); 
// Route::get('sicaptcha', 'HomeController@sicaptcha')->name('sicaptcha');    
// Route::get('invoice/secure/{slug}', 'InvoiceController@invoice')->name('invoice');   
// Route::get('/invoice/download/{id}', 'InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download'); 
// Route::get('/invoice/print/{id}', 'InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print');

//Thank you page after email verification - REMOVED (HomeController deleted, will be recreated in future)
//Route::get('thankyou', 'HomeController@thankyou')->name('thankyou');

//Root login routes - same as admin login
Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('login');
Route::post('/', 'Auth\AdminLoginController@login');

// Admin login routes (keep separate for /admin path access)
Route::get('/admin', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin', 'Auth\AdminLoginController@login');
Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm');
Route::post('/admin/login', 'Auth\AdminLoginController@login');
Route::post('/admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

/*---------------Agent Route-------------------*/
// Agent routes disabled - agents don't have login access (they exist only as records)
// require __DIR__ . '/agent.php';
/*********************Admin Panel Start ***********************/
	
	//General
        Route::get('/dashboard', 'Admin\AdminController@dashboard')->name('dashboard');
		Route::get('/get_customer_detail', 'Admin\AdminController@CustomerDetail')->name('get_customer_detail');
		Route::get('/my_profile', 'Admin\AdminController@myProfile')->name('my_profile');
		Route::post('/my_profile', 'Admin\AdminController@myProfile');
		Route::get('/change_password', 'Admin\AdminController@change_password')->name('change_password');
		Route::post('/change_password', 'Admin\AdminController@change_password');
		Route::get('/sessions', 'Admin\AdminController@sessions')->name('sessions');
		Route::post('/sessions', 'Admin\AdminController@sessions'); 
		Route::post('/update_action', 'Admin\AdminController@updateAction');
		Route::post('/approved_action', 'Admin\AdminController@approveAction');
		Route::post('/process_action', 'Admin\AdminController@processAction');
		Route::post('/archive_action', 'Admin\AdminController@archiveAction');
		Route::post('/declined_action', 'Admin\AdminController@declinedAction');
		Route::post('/delete_action', 'Admin\AdminController@deleteAction');
         Route::post('/delete_slot_action', 'Admin\AdminController@deleteSlotAction');
		Route::post('/move_action', 'Admin\AdminController@moveAction');
		
		
		Route::post('/add_ckeditior_image', 'Admin\AdminController@addCkeditiorImage')->name('add_ckeditior_image');
		Route::post('/get_chapters', 'Admin\AdminController@getChapters')->name('get_chapters');
		// NOTE: website_setting routes have been removed - website_settings table has been dropped
		// Route::get('/website_setting', 'Admin\AdminController@websiteSetting')->name('website_setting');
		// Route::post('/website_setting', 'Admin\AdminController@websiteSetting');
		Route::post('/get_states', 'Admin\AdminController@getStates');
		Route::get('/settings/taxes/returnsetting', 'Admin\AdminController@returnsetting')->name('returnsetting');
		Route::post('/settings/taxes/savereturnsetting', 'Admin\AdminController@returnsetting')->name('savereturnsetting');
		// NOTE: Tax rate routes have been removed (taxrates, taxrates/create, taxrates/store, taxrates/edit)
		// These routes were related to the tax_rates table which has been dropped
		Route::get('/getsubcategories', 'Admin\AdminController@getsubcategories');
		Route::get('/getproductbranch', 'Admin\AdminController@getproductbranch');
		Route::get('/getservicemodal', [ServicesController::class, 'servicemodal']);
		Route::get('/getassigneeajax', 'Admin\AdminController@getassigneeajax');
		Route::get('/getpartnerajax', 'Admin\AdminController@getpartnerajax');
	Route::get('/checkclientexist', 'Admin\AdminController@checkclientexist');
/*CRM route start*/
	
	Route::get('/users', [UserController::class, 'index'])->name('users.index');
		Route::get('/users/create', [UserController::class, 'create'])->name('users.create'); 
		Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
		Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
		Route::get('/users/view/{id}', [UserController::class, 'view'])->name('users.view');
		Route::post('/users/edit', [UserController::class, 'edit']);
		Route::post('/users/savezone', [UserController::class, 'savezone']);
		
		Route::get('/users/active', [UserController::class, 'active'])->name('users.active');
		Route::get('/users/inactive', [UserController::class, 'inactive'])->name('users.inactive'); 
		Route::get('/users/invited', [UserController::class, 'invited'])->name('users.invited');  
		
	Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
	Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create'); 
	Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
	Route::get('/staff/edit/{id}', [StaffController::class, 'edit'])->name('staff.edit');
	Route::post('/staff/edit', [StaffController::class, 'edit']);
	
	// Customer routes removed - legacy travel system feature
	
	Route::get('/users/clientlist', [UserController::class, 'clientlist'])->name('users.clientlist');
		Route::get('/users/createclient', [UserController::class, 'createclient'])->name('users.createclient'); 
		Route::post('/users/storeclient', [UserController::class, 'storeclient'])->name('users.storeclient'); 
		Route::get('/users/editclient/{id}', [UserController::class, 'editclient'])->name('users.editclient');
		Route::post('/users/editclient', [UserController::class, 'editclient']); 
		
		Route::post('/followup/store', [FollowupController::class, 'store'])->name('followup.store'); 
		Route::get('/followup/list', [FollowupController::class, 'index'])->name('followup.index'); 
		Route::post('/followup/compose', [FollowupController::class, 'compose'])->name('followup.compose'); 
		 
		Route::get('/usertype', 'Admin\UsertypeController@index')->name('usertype.index');
		Route::get('/usertype/create', 'Admin\UsertypeController@create')->name('usertype.create');  		
		Route::post('/usertype/store', 'Admin\UsertypeController@store')->name('usertype.store');
		Route::get('/usertype/edit/{id}', 'Admin\UsertypeController@edit')->name('usertype.edit');
		Route::post('/usertype/edit', 'Admin\UsertypeController@edit');
		
		Route::get('/userrole', 'Admin\UserroleController@index')->name('userrole.index');
		Route::get('/userrole/create', 'Admin\UserroleController@create')->name('userrole.create');  
		Route::post('/userrole/store', 'Admin\UserroleController@store')->name('userrole.store');
		Route::get('/userrole/edit/{id}', 'Admin\UserroleController@edit')->name('userrole.edit');
		Route::post('/userrole/edit', 'Admin\UserroleController@edit');
		
	//Services Start
		Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
		Route::get('/services/create', [ServicesController::class, 'create'])->name('services.create'); 
		Route::post('/services/store', [ServicesController::class, 'store'])->name('services.store');
		Route::get('/services/edit/{id}', [ServicesController::class, 'edit'])->name('services.edit');
		Route::post('/services/edit', [ServicesController::class, 'edit']);
			     
	  //Manage Contacts Start   
		Route::get('/contact', [ContactController::class, 'index'])->name('managecontact.index'); 
		Route::get('/contact/create', [ContactController::class, 'create'])->name('managecontact.create');
		Route::post('/managecontact/store', [ContactController::class, 'store'])->name('managecontact.store');
		Route::post('/contact/add', [ContactController::class, 'add'])->name('managecontact.add');
		Route::get('/contact/edit/{id}', [ContactController::class, 'edit'])->name('managecontact.edit');
		Route::post('/contact/edit', [ContactController::class, 'edit']);
		Route::post('/contact/storeaddress', [ContactController::class, 'storeaddress']);
		 
	//Leads Start - Updated to modern syntax
	Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');  
	Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
	Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');   
	Route::post('/leads/assign', [LeadController::class, 'assign'])->name('leads.assign');    
	Route::get('/leads/detail/{id}', [ClientsController::class, 'leaddetail'])->name('leads.detail');  // Lead detail page (uses client detail view)
	// Removed broken edit routes - leads now use detail page for viewing/editing
	Route::get('/leads/notes/delete/{id}', [LeadController::class, 'leaddeleteNotes']);
	Route::get('/get-notedetail', [LeadController::class, 'getnotedetail']);
	Route::post('/followup/update', [FollowupController::class, 'followupupdate']);
	Route::get('/leads/convert', [LeadController::class, 'convertoClient']);
	Route::get('/leads/pin/{id}', [LeadController::class, 'leadPin']); 	
		//Invoices Start    
		
		// Removed routes for deleted views: lists, email, invoicebyid, history, reminder
		// Route::get('/invoice/lists/{id}', 'Admin\InvoiceController@lists')->name('invoice.lists');  
		Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit')->name('invoice.edit');  
		Route::post('/invoice/edit', 'Admin\InvoiceController@edit');  
		Route::get('/invoice/create', 'Admin\InvoiceController@create')->name('invoice.create');   
		Route::post('/invoice/store', 'Admin\InvoiceController@store')->name('invoice.store'); 
		Route::get('/invoice/detail', 'Admin\InvoiceController@detail')->name('invoice.detail'); 
		// Route::get('/invoice/email/{id}', 'Admin\InvoiceController@email')->name('invoice.email'); 
		// Route::post('/invoice/email', 'Admin\InvoiceController@email'); 
		Route::get('/invoice/editpayment', 'Admin\InvoiceController@editpayment')->name('invoice.editpayment'); 
		// Route::get('/invoice/invoicebyid', 'Admin\InvoiceController@invoicebyid')->name('invoice.invoicebyid'); 
		// Route::get('/invoice/history', 'Admin\InvoiceController@history')->name('invoice.history'); 
		Route::post('/invoice/paymentsave', 'Admin\InvoiceController@paymentsave')->name('invoice.paymentsave'); 
		Route::post('/invoice/editpaymentsave', 'Admin\InvoiceController@editpaymentsave')->name('invoice.editpaymentsave'); 
		Route::post('/invoice/addcomment', 'Admin\InvoiceController@addcomment')->name('invoice.addcomment'); 
		Route::post('/invoice/sharelink', 'Admin\InvoiceController@sharelink')->name('invoice.sharelink'); 
		Route::post('/invoice/disablelink', 'Admin\InvoiceController@disablelink')->name('invoice.disablelink'); 
		Route::get('/invoice/download/{id}', 'Admin\InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download'); 
		Route::get('/invoice/exportall', 'Admin\InvoiceController@exportall')->name('invoice.exportall'); 
		Route::get('/invoice/printall', 'Admin\InvoiceController@customer_invoice_printall')->name('invoice.customer_invoice_printall'); 
		Route::get('/invoice/print/{id}', 'Admin\InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print'); 
		// Route::get('/invoice/reminder/{id}', 'Admin\InvoiceController@reminder')->name('invoice.reminder'); 
		// Route::post('/invoice/reminder', 'Admin\InvoiceController@reminder'); 
		Route::post('/invoice/attachfile', 'Admin\InvoiceController@attachfile')->name('invoice.attachfile'); 
		Route::get('/invoice/getattachfile', 'Admin\InvoiceController@getattachfile')->name('invoice.getattachfile'); 
		Route::get('/invoice/removeattachfile', 'Admin\InvoiceController@removeattachfile')->name('invoice.removeattachfile'); 
		Route::get('/invoice/attachfileemail', 'Admin\InvoiceController@attachfileemail')->name('invoice.attachfileemail'); 
	  //Manage Api key 
	 // Route::get('/api-key', 'Admin\ApiController@index')->name('apikey.index');
	  //Manage Api key  
				      
	//Email Templates Pages
		Route::get('/email_templates', 'Admin\EmailTemplateController@index')->name('email.index');
		Route::get('/email_templates/create', 'Admin\EmailTemplateController@create')->name('email.create');
		Route::post('/email_templates/store', 'Admin\EmailTemplateController@store')->name('email.store');
		Route::get('/edit_email_template/{id}', 'Admin\EmailTemplateController@editEmailTemplate')->name('edit_email_template');
		Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate');	
		
	//SEO Tool
		Route::get('/edit_seo/{id}', 'Admin\AdminController@editSeo')->name('edit_seo');
		Route::post('/edit_seo', 'Admin\AdminController@editSeo');
		
	Route::get('/api-key', 'Admin\AdminController@editapi')->name('edit_api');
	Route::post('/api-key', 'Admin\AdminController@editapi');	
	
	//clients routes - moved to routes/clients.php (unified routes)
		// Keep get-templates and sendmail here as they use AdminController (not in clients.php)
		Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('clients.gettemplates');
		Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('clients.sendmail');
		
		//products Start   
		Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
		Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create'); 
		Route::post('/products/store', [ProductsController::class, 'store'])->name('products.store');
		Route::get('/products/edit/{id}', [ProductsController::class, 'edit'])->name('products.edit');
		Route::post('/products/edit', [ProductsController::class, 'edit']);
		Route::post('/products-import', [ProductsController::class, 'import'])->name('products.import');

		
		Route::get('/products/detail/{id}', [ProductsController::class, 'detail'])->name('products.detail');	 
		 Route::get('/products/get-recipients', [ProductsController::class, 'getrecipients'])->name('products.getrecipients');
		Route::get('/products/get-allclients', [ProductsController::class, 'getallclients'])->name('products.getallclients');
		
		//Partner Start
		Route::get('/partners', [PartnersController::class, 'index'])->name('partners.index');
		Route::get('/partners/create', [PartnersController::class, 'create'])->name('partners.create');  
		Route::post('/partners/store', [PartnersController::class, 'store'])->name('partners.store');
		Route::get('/partners/edit/{id}', [PartnersController::class, 'edit'])->name('partners.edit');
		Route::post('/partners/edit', [PartnersController::class, 'edit']);
		Route::get('/getpaymenttype', [PartnersController::class, 'getpaymenttype'])->name('partners.getpaymenttype');
		
		Route::get('/partners/detail/{id}', [PartnersController::class, 'detail'])->name('partners.detail');	 
		 Route::get('/partners/get-recipients', [PartnersController::class, 'getrecipients'])->name('partners.getrecipients');
		Route::get('/partners/get-allclients', [PartnersController::class, 'getallclients'])->name('partners.getallclients');
	
		//Branch Start
		Route::get('/branch', 'Admin\BranchesController@index')->name('branch.index'); 
		Route::get('/branch/create', 'Admin\BranchesController@create')->name('branch.create');  
		Route::post('/branch/store', 'Admin\BranchesController@store')->name('branch.store');
		Route::get('/branch/edit/{id}', 'Admin\BranchesController@edit')->name('branch.edit');
		Route::get('/branch/view/{id}', 'Admin\BranchesController@view')->name('branch.userview');
		Route::get('/branch/view/client/{id}', 'Admin\BranchesController@viewclient')->name('branch.clientview'); 
		Route::post('/branch/edit', 'Admin\BranchesController@edit');
		 
		 
		
		//Agent Start   
	/* 	Route::get('/agents', 'Admin\AgentController@index')->name('agents.index');
		Route::get('/agents/create', 'Admin\AgentController@create')->name('agents.create'); 
		Route::post('/agents/store', 'Admin\AgentController@store')->name('agents.store');
		
		Route::post('/agents/edit', 'Admin\AgentController@edit')->name('agents.edit'); */
		
		Route::get('/agents/active', 'Admin\AgentController@active')->name('agents.active'); 
		Route::get('/agents/inactive', 'Admin\AgentController@inactive')->name('agents.inactive');  
		Route::get('/agents/show/{id}', 'Admin\AgentController@show')->name('agents.show'); 
		Route::get('/agents/create', 'Admin\AgentController@create')->name('agents.create'); 
		Route::get('/agents/import', 'Admin\AgentController@import')->name('agents.import'); 
		Route::post('/agents/store', 'Admin\AgentController@store')->name('agents.store'); 
		Route::get('/agent/detail/{id}', 'Admin\AgentController@detail')->name('agents.detail'); 
		Route::post('/agents/savepartner', 'Admin\AgentController@savepartner'); 
		 Route::get('/agents/edit/{id}', 'Admin\AgentController@edit')->name('agents.edit');
		 Route::post('/agents/edit', 'Admin\AgentController@edit');
		 Route::get('/agents/import/business', 'Admin\AgentController@businessimport');
		 Route::get('/agents/import/individual', 'Admin\AgentController@individualimport');
		//Task System Removed - Database tables preserved (tasks, task_logs, to_do_groups)
		// Removed on: December 2025 - System was inactive for 16+ months
		
		//General Invoice Start 
		Route::get('/invoice/general-invoice', 'Admin\InvoiceController@general_invoice')->name('invoice.general-invoice'); 
		
		Route::get('/applications/detail/{id}', 'Admin\ApplicationsController@detail')->name('applications.detail'); 	 
		Route::post('/interested-service', [ClientsController::class, 'interestedService']); 	 
		Route::post('/edit-interested-service', [ClientsController::class, 'editinterestedService']); 	 
		Route::get('/get-services', [ClientsController::class, 'getServices']); 	 
		Route::post('/upload-mail', [ClientsController::class, 'uploadmail']); 	 
		Route::post('/mail/enhance', [ClientsController::class, 'enhanceMessage'])->name('mail.enhance');
		Route::post('/updatefollowupschedule', [ClientsController::class, 'updatefollowupschedule']); 
  
        Route::get('/pinnote', [ClientsController::class, 'pinnote']); 	 
  	    Route::get('/pinactivitylog', [ClientsController::class, 'pinactivitylog']);
  
		Route::get('/getintrestedservice', [ClientsController::class, 'getintrestedservice']); 	 
		Route::post('/application/saleforcastservice', [ClientsController::class, 'saleforcastservice']);
		Route::get('/getintrestedserviceedit', [ClientsController::class, 'getintrestedserviceedit']); 	 
		Route::post('/saveeducation', 'Admin\EducationController@store'); 	 
		Route::post('/editeducation', 'Admin\EducationController@edit'); 	 
		Route::get('/get-educations', 'Admin\EducationController@geteducations'); 	 
		Route::get('/getEducationdetail', 'Admin\EducationController@getEducationdetail'); 	 
			 
		Route::get('/delete-education', 'Admin\EducationController@deleteeducation'); 	 
		Route::post('/edit-test-scores', 'Admin\EducationController@edittestscores'); 	 
		Route::post('/other-test-scores', 'Admin\EducationController@othertestscores'); 	 
		Route::post('/create-invoice', 'Admin\InvoiceController@createInvoice'); 	 
		Route::get('/application/invoice/{client_id}/{application}/{invoice_type}', 'Admin\InvoiceController@getInvoice'); 	 
		Route::get('/invoice/view/{id}', 'Admin\InvoiceController@show'); 	 
		Route::get('/invoice/preview/{id}', 'Admin\InvoiceController@getinvoicespdf'); 	 
		Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit'); 	 
		Route::post('/invoice/general-store', 'Admin\InvoiceController@generalStore'); 	 
		Route::get('/invoice/delete-payment', 'Admin\InvoiceController@deletepayment'); 	 
		Route::post('/invoice/payment-store', 'Admin\InvoiceController@invoicepaymentstore'); 	 
		Route::get('/get-invoices', 'Admin\InvoiceController@getinvoices'); 	 
		Route::get('/get-invoices-pdf', 'Admin\InvoiceController@getinvoicespdf'); 	 
		Route::get('/delete-invoice', 'Admin\InvoiceController@deleteinvoice'); 	 
		Route::post('/invoice/general-edit', 'Admin\InvoiceController@updategeninvoices'); 	 
		Route::post('/invoice/com-store', 'Admin\InvoiceController@updatecominvoices'); 
		Route::get('/invoice/paid', 'Admin\InvoiceController@paid')->name('invoice.paid');  	 
		Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('invoice.unpaid');  	 
		//Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('invoice.unpaid');  
		Route::get('/invoice/', 'Admin\InvoiceController@index')->name('invoice.index');	
		Route::get('/payment/view/{id}', 'Admin\AccountController@preview');	

	
		Route::get('/getapplicationdetail', 'Admin\ApplicationsController@getapplicationdetail');		
		Route::get('/updatestage', 'Admin\ApplicationsController@updatestage');		
		Route::get('/completestage', 'Admin\ApplicationsController@completestage');		
		Route::get('/updatebackstage', 'Admin\ApplicationsController@updatebackstage');		
		Route::get('/get-applications-logs', 'Admin\ApplicationsController@getapplicationslogs');		
		Route::get('/get-applications', 'Admin\ApplicationsController@getapplications');		
		Route::post('/create-app-note', 'Admin\ApplicationsController@addNote');		
		Route::get('/getapplicationnotes', 'Admin\ApplicationsController@getapplicationnotes');		
		Route::post('/application-sendmail', 'Admin\ApplicationsController@applicationsendmail');		
		Route::get('/application/updateintake', 'Admin\ApplicationsController@updateintake');		
		Route::get('/application/updatedates', 'Admin\ApplicationsController@updatedates');		
		Route::get('/application/updateexpectwin', 'Admin\ApplicationsController@updateexpectwin');		
		Route::get('/application/getapplicationbycid', 'Admin\ApplicationsController@getapplicationbycid');		
		Route::post('/application/spagent_application', 'Admin\ApplicationsController@spagent_application');		
		Route::post('/application/sbagent_application', 'Admin\ApplicationsController@sbagent_application');		
		Route::post('/application/application_ownership', 'Admin\ApplicationsController@application_ownership');		
		Route::post('/application/saleforcast', 'Admin\ApplicationsController@saleforcast');		
		Route::get('/superagent', 'Admin\ApplicationsController@superagent');		
		Route::get('/subagent', 'Admin\ApplicationsController@subagent');		
		Route::get('/showproductfee', 'Admin\ApplicationsController@showproductfee');		
		Route::post('/applicationsavefee', 'Admin\ApplicationsController@applicationsavefee');		
		Route::get('/application/export/pdf/{id}', 'Admin\ApplicationsController@exportapplicationpdf'); 
		Route::post('/add-checklists', 'Admin\ApplicationsController@addchecklists'); 
		Route::post('/application/checklistupload', 'Admin\ApplicationsController@checklistupload'); 
		Route::get('/deleteapplicationdocs', 'Admin\ApplicationsController@deleteapplicationdocs'); 
		Route::get('/application/publishdoc', 'Admin\ApplicationsController@publishdoc'); 


		//Account Start
		Route::get('/payment', 'Admin\AccountController@payment')->name('account.payment');
		Route::get('/income-sharing/payables/unpaid', 'Admin\AccountController@payableunpaid')->name('account.payableunpaid');
		Route::get('/income-sharing/payables/paid', 'Admin\AccountController@payablepaid')->name('account.payablepaid');
		Route::post('/income-payment-store', 'Admin\AccountController@incomepaymentstore');
		Route::get('/revert-payment', 'Admin\AccountController@revertpayment');
		Route::get('/income-sharing/receivables/unpaid', 'Admin\AccountController@receivableunpaid')->name('account.receivableunpaid');
		Route::get('/income-sharing/receivables/paid', 'Admin\AccountController@receivablepaid')->name('account.receivablepaid');
		Route::get('/group-invoice/unpaid', 'Admin\InvoiceController@unpaidgroupinvoice')->name('invoice.unpaidgroupinvoice');
		Route::get('/group-invoice/paid', 'Admin\InvoiceController@paidgroupinvoice')->name('invoice.paidgroupinvoice');
		Route::get('/group-invoice/create', 'Admin\InvoiceController@creategroupinvoice')->name('invoice.creategroupinvoice'); 
		Route::get('/invoice-schedules', 'Admin\InvoiceController@invoiceschedules')->name('invoice.invoiceschedules'); 
		Route::post('/paymentschedule', 'Admin\InvoiceController@paymentschedule')->name('invoice.paymentschedule'); 
		Route::post('/setup-paymentschedule', 'Admin\InvoiceController@setuppaymentschedule'); 
		Route::post('/editpaymentschedule', 'Admin\InvoiceController@editpaymentschedule')->name('invoice.editpaymentschedule'); 
		Route::get('/scheduleinvoicedetail', 'Admin\InvoiceController@scheduleinvoicedetail'); 
		Route::get('/addscheduleinvoicedetail', 'Admin\InvoiceController@addscheduleinvoicedetail'); 
		Route::get('/get-all-paymentschedules', 'Admin\InvoiceController@getallpaymentschedules'); 
		Route::get('/deletepaymentschedule', 'Admin\InvoiceController@deletepaymentschedule'); 
		Route::get('/applications/preview-schedules/{id}', 'Admin\InvoiceController@apppreviewschedules'); 
		
		// NOTE: Feature configuration routes (Product Type, Partner Type, Visa Type, etc.) have been moved to routes/adminconsole.php
		// Those routes now use the AdminConsole namespace and are accessible at /adminconsole/* paths
		// The duplicate routes that were here (lines 436-532) have been removed to prevent conflicts and errors
		
		Route::post('/partner/saveagreement', [PartnersController::class, 'saveagreement']);
		Route::post('/partner/create-contact', [PartnersController::class, 'createcontact']);
		Route::get('/get-contacts', [PartnersController::class, 'getcontacts']);
		Route::get('/deletecontact', [PartnersController::class, 'deletecontact']);
		Route::get('/getcontactdetail', [PartnersController::class, 'getcontactdetail']);
		Route::post('/partners-import', [PartnersController::class, 'import'])->name('partners.import');
		
		Route::post('/partner/create-branch', [PartnersController::class, 'createbranch']);
		Route::get('/get-branches', [PartnersController::class, 'getbranch']);
		Route::get('/getbranchdetail', [PartnersController::class, 'getbranchdetail']);
		Route::get('/deletebranch', [PartnersController::class, 'deletebranch']);
		
		Route::post('/saveotherinfo', [ProductsController::class, 'saveotherinfo']);
		Route::get('/product/getotherinfo', [ProductsController::class, 'getotherinfo']);
		Route::get('/get-all-fees', [ProductsController::class, 'getallfees']);
		Route::post('/savefee', [ProductsController::class, 'savefee']);
		
		Route::get('/getfeeoptionedit', [ProductsController::class, 'editfee']);
		Route::post('/editfee', [ProductsController::class, 'editfeeform']);
		Route::get('/deletefee', [ProductsController::class, 'deletefee']);
		
		
		// Task system removed - December 2025
		// Route::post('/partner/addtask', [PartnersController::class, 'addtask']);
		// Route::get('/partner/get-tasks', [PartnersController::class, 'gettasks']);
		// Route::get('/partner/get-task-detail', [PartnersController::class, 'taskdetail']);
		// Route::post('/partner/savecomment', [PartnersController::class, 'savecomment']);
		// Task system removed - December 2025
		// Route::get('/change-task-status', [PartnersController::class, 'changetaskstatus']);
		// Route::get('/change-task-priority', [PartnersController::class, 'changetaskpriority']);
		
		Route::post('/promotion/store', 'Admin\PromotionController@store');
		Route::post('/promotion/edit', 'Admin\PromotionController@edit');
		Route::get('/get-promotions', 'Admin\PromotionController@getpromotions');
		Route::get('/getpromotioneditform', 'Admin\PromotionController@getpromotioneditform');
		Route::get('/change-promotion-status', 'Admin\PromotionController@changepromotionstatus');
		
		
		//Applications Start    
		Route::get('/applications', 'Admin\ApplicationsController@index')->name('applications.index');  
		Route::get('/applications/create', 'Admin\ApplicationsController@create')->name('applications.create');  
		Route::post('/discontinue_application', 'Admin\ApplicationsController@discontinue_application');  
		Route::post('/revert_application', 'Admin\ApplicationsController@revert_application');  
		Route::post('/applications-import', 'Admin\ApplicationsController@import')->name('applications.import');
		//Route::post('/product-type/store', 'Admin\ProductTypeController@store')->name('feature.producttype.store');   
		//Route::get('/product-type/edit/{id}', 'Admin\ProductTypeController@edit')->name('feature.producttype.edit');
		//Route::post('/product-type/edit', 'Admin\ProductTypeController@edit')->name('feature.producttype.edit');
		Route::get('/office-visits', 'Admin\OfficeVisitController@index')->name('officevisits.index');  
		Route::get('/office-visits/waiting', 'Admin\OfficeVisitController@waiting')->name('officevisits.waiting');  
		Route::get('/office-visits/attending', 'Admin\OfficeVisitController@attending')->name('officevisits.attending');  
		Route::get('/office-visits/completed', 'Admin\OfficeVisitController@completed')->name('officevisits.completed'); 
		Route::get('/office-visits/archived', 'Admin\OfficeVisitController@archived')->name('officevisits.archived');   
		Route::get('/office-visits/create', 'Admin\OfficeVisitController@create')->name('officevisits.create'); 
		Route::post('/checkin', 'Admin\OfficeVisitController@checkin');	
		Route::get('/get-checkin-detail', 'Admin\OfficeVisitController@getcheckin');	
		Route::post('/update_visit_purpose', 'Admin\OfficeVisitController@update_visit_purpose');	
		Route::post('/update_visit_comment', 'Admin\OfficeVisitController@update_visit_comment');	
		Route::post('/attend_session', 'Admin\OfficeVisitController@attend_session');	
		Route::post('/complete_session', 'Admin\OfficeVisitController@complete_session');	
		Route::get('/office-visits/change_assignee', 'Admin\OfficeVisitController@change_assignee');  
		//Route::post('/agents/store', 'Admin\AgentController@store')->name('agents.store'); 
		//Route::get('/agent/detail/{id}', 'Admin\AgentController@detail')->name('agents.detail'); 
		
		// Enquiries/Queries routes removed - feature not in use
		//Route::get('/enquiries', 'Admin\EnquireController@index')->name('enquiries.index'); 
		//Route::get('/enquiries/archived-enquiries', 'Admin\EnquireController@Archived')->name('enquiries.archived'); 
		//Route::get('/enquiries/covertenquiry/{id}', 'Admin\EnquireController@covertenquiry'); 
		//Route::get('/enquiries/archived/{id}', 'Admin\EnquireController@archivedenquiry'); 
	
		//Audit Logs Start   
		Route::get('/audit-logs', 'Admin\AuditLogController@index')->name('auditlogs.index');
		
		//Reports Start   
		Route::get('/report/client', 'Admin\ReportController@client')->name('reports.client');
		Route::get('/report/application', 'Admin\ReportController@application')->name('reports.application');
		Route::get('/report/invoice', 'Admin\ReportController@invoice')->name('reports.invoice');
		Route::get('/report/office-visit', 'Admin\ReportController@office_visit')->name('reports.office-visit');
		Route::get('/report/sale-forecast/application', 'Admin\ReportController@saleforecast_application')->name('reports.saleforecast-application');  
		Route::get('/report/sale-forecast/interested-service', 'Admin\ReportController@interested_service')->name('reports.interested-service');
		// Task system reports removed - December 2025
		// Route::get('/report/task/personal-task-report', 'Admin\ReportController@personal_task')->name('reports.personal-task-report');
		// Route::get('/report/task/office-task-report', 'Admin\ReportController@office_task')->name('reports.office-task-report'); 
		Route::get('/reports/visaexpires', 'Admin\ReportController@visaexpires'); 
		Route::get('/followup-dates', 'Admin\ReportController@followupdates'); 
		Route::get('/reports/agreementexpires', 'Admin\ReportController@agreementexpires');
		Route::get('/report/noofpersonofficevisit', 'Admin\ReportController@noofpersonofficevisit')->name('reports.noofpersonofficevisit');


		Route::post('/save_tag', [ClientsController::class, 'save_tag']); 	 
		
		// NOTE: Email and CRM Email Template routes have been moved to routes/adminconsole.php
		// Those routes now use the AdminConsole namespace and are accessible at /adminconsole/* paths
		// The duplicate routes that were here (lines 626-637) have been removed to prevent conflicts and errors
		
		Route::get('/gen-settings', 'Admin\AdminController@gensettings')->name('gensettings.index');
		Route::post('/gen-settings/update', 'Admin\AdminController@gensettingsupdate')->name('gensettings.update');
		
		Route::get('/fetch-notification', 'Admin\AdminController@fetchnotification');
		Route::get('/fetch-messages', 'Admin\AdminController@fetchmessages');
	    Route::get('/upload-checklists', 'Admin\UploadChecklistController@index')->name('upload_checklists.index');
		Route::post('/upload-checklists/store', 'Admin\UploadChecklistController@store')->name('upload_checklistsupload');		
		Route::get('/teams', 'Admin\TeamController@index')->name('teams.index');
		Route::get('/teams/edit/{id}', 'Admin\TeamController@edit')->name('teams.edit');
		Route::post('/teams/edit', 'Admin\TeamController@edit');
		Route::post('/teams/store', 'Admin\TeamController@store')->name('teamsupload');	
		Route::get('/all-notifications', 'Admin\AdminController@allnotification')->name('notifications.index');
		Route::post('/notifications/mark-read', 'Admin\AdminController@markNotificationAsRead')->name('notifications.mark-read');
		Route::post('/notifications/mark-all-read', 'Admin\AdminController@markAllNotificationsAsRead')->name('notifications.mark-all-read');	
		
		// Action module
		Route::get('/action', 'Admin\ActionController@index')->name('action.index'); // Main Action page
        Route::get('/action/list','Admin\ActionController@getList')->name('action.list'); // DataTable data
        Route::get('/action/completed', 'Admin\ActionController@completed')->name('action.completed'); //completed actions
        Route::get('/action/assigned-by-me', 'Admin\ActionController@assignedByMe')->name('action.assigned_by_me'); //assigned by me
        Route::get('/action/assigned-to-me', 'Admin\ActionController@assignedToMe')->name('action.assigned_to_me'); //assigned to me

        Route::post('/action/task-complete', 'Admin\ActionController@markComplete'); //update task to be completed
        Route::post('/action/task-incomplete', 'Admin\ActionController@markIncomplete'); //update task to be not completed

        Route::delete('/action/destroy-by-me/{note_id}', 'Admin\ActionController@destroyByMe')->name('action.destroy_by_me'); //delete assigned by me
        Route::delete('/action/destroy-to-me/{note_id}', 'Admin\ActionController@destroyToMe')->name('action.destroy_to_me'); //delete assigned to me
        Route::delete('/action/destroy/{note_id}', 'Admin\ActionController@destroy')->name('action.destroy'); //delete action
        Route::delete('/action/destroy-completed/{note_id}', 'Admin\ActionController@destroyCompleted')->name('action.destroy_completed'); //delete completed action
        
        Route::post('/action/assignee-list', 'Admin\ActionController@getAssigneeList'); //get assignee list

        //Total person waiting and total activity counter
        Route::get('/fetch-InPersonWaitingCount', 'Admin\AdminController@fetchInPersonWaitingCount');
        Route::get('/fetch-TotalActivityCount', 'Admin\AdminController@fetchTotalActivityCount');
        //For email and contact number uniqueness
        Route::post('/is_email_unique', 'Admin\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'Admin\LeadController@is_contactno_unique');
        
        // Client routes moved to routes/clients.php (unified routes)
  
        Route::post('/application/updateStudentId', 'Admin\ApplicationsController@updateStudentId');
  
        Route::get('/showproductfeelatest', 'Admin\ApplicationsController@showproductfeelatest');
		Route::post('/applicationsavefeelatest', 'Admin\ApplicationsController@applicationsavefeelatest');
  
        // NOTE: Document Checklist routes have been moved to routes/adminconsole.php
		// Those routes now use the AdminConsole namespace and are accessible at /adminconsole/* paths
		// The duplicate routes that were here (lines 686-690) have been removed to prevent conflicts and errors
  
  		// Client document routes moved to routes/clients.php (unified routes)
  
        //inactive partners
        Route::get('/partners-inactive', [PartnersController::class, 'inactivePartnerList'])->name('partners.inactive');
        Route::post('/partner_change_to_inactive', 'Admin\AdminController@partnerChangeToInactive');
        Route::post('/partner_change_to_active', 'Admin\AdminController@partnerChangeToActive');
  
  
       //Partner Student Invoice
        Route::get('/partners/savepartnerstudentinvoice/{id}', [PartnersController::class, 'savepartnerstudentinvoice'])->name('partners.savepartnerstudentinvoice');
        Route::post('/partners/savepartnerstudentinvoice', [PartnersController::class, 'savepartnerstudentinvoice'])->name('partners.savepartnerstudentinvoice.update');
        Route::post('/partners/getTopReceiptValInDB', [PartnersController::class, 'getTopReceiptValInDB'])->name('partners.getTopReceiptValInDB');
        Route::post('/partners/getEnrolledStudentList', [PartnersController::class, 'getEnrolledStudentList'])->name('partners.getEnrolledStudentList');


        //Partner Student Record Invoice
        Route::get('/partners/savepartnerrecordinvoice/{id}', [PartnersController::class, 'savepartnerrecordinvoice'])->name('partners.savepartnerrecordinvoice');
        Route::post('/partners/savepartnerrecordinvoice', [PartnersController::class, 'savepartnerrecordinvoice'])->name('partners.savepartnerrecordinvoice.update');

        //Partner Student Record payment
        Route::get('/partners/savepartnerrecordpayment/{id}', [PartnersController::class, 'savepartnerrecordpayment'])->name('partners.savepartnerrecordpayment');
        Route::post('/partners/savepartnerrecordpayment', [PartnersController::class, 'savepartnerrecordpayment'])->name('partners.savepartnerrecordpayment.update');
        Route::post('/partners/getRecordedInvoiceList', [PartnersController::class, 'getRecordedInvoiceList'])->name('partners.getRecordedInvoiceList');
        //update student status
        Route::post('/partners/update-student-status', [PartnersController::class, 'updateStudentStatus'])->name('partners.updateStudentStatus');

        //get student info
        Route::post('/partners/getStudentInfo', [PartnersController::class, 'getStudentInfo'])->name('partners.getStudentInfo');
        Route::post('/partners/getStudentCourseInfo', [PartnersController::class, 'getStudentCourseInfo'])->name('partners.getStudentCourseInfo');

        Route::post('/partners/getTopInvoiceValInDB', [PartnersController::class, 'getTopInvoiceValInDB'])->name('partners.getTopInvoiceValInDB');
        Route::get('/partners/printpreviewcreateinvoice/{id}', [PartnersController::class, 'printpreviewcreateinvoice']); //Create Student Invoice print preview

        Route::post('/partners/updateInvoiceSentOptionToYes', [PartnersController::class, 'updateInvoiceSentOptionToYes'])->name('partners.updateInvoiceSentOptionToYes');
        Route::post('/partners/getInfoByInvoiceId', [PartnersController::class, 'getInfoByInvoiceId'])->name('partners.getInfoByInvoiceId');

  Route::post('/partners/getEnrolledStudentListInEditMode', [PartnersController::class, 'getEnrolledStudentListInEditMode'])->name('partners.getEnrolledStudentListInEditMode');
  
  Route::post('/partners/deleteStudentRecordByInvoiceId', [PartnersController::class, 'deleteStudentRecordByInvoiceId'])->name('partners.deleteStudentRecordByInvoiceId');
        Route::post('/partners/deleteStudentRecordInvoiceByInvoiceId', [PartnersController::class, 'deleteStudentRecordInvoiceByInvoiceId'])->name('partners.deleteStudentRecordInvoiceByInvoiceId');
        Route::post('/partners/deleteStudentPaymentInvoiceByInvoiceId', [PartnersController::class, 'deleteStudentPaymentInvoiceByInvoiceId'])->name('partners.deleteStudentPaymentInvoiceByInvoiceId');

  
       //partner inbox and sent email
        Route::post('/upload-partner-fetch-mail', [PartnersController::class, 'uploadpartnerfetchmail']);
        Route::post('/upload-partner-sent-fetch-mail', [PartnersController::class, 'uploadpartnersentfetchmail']);
  
        //Applications overdue
		Route::get('/applications-overdue', 'Admin\ApplicationsController@overdueApplicationList')->name('applications.overdue');
  
       //Applications finalize
		Route::get('/applications-finalize', 'Admin\ApplicationsController@finalizeApplicationList')->name('applications.finalize');
  
        //partner assign user
        Route::post('/partners/followup_partner/store_partner', [PartnersController::class, 'followupstore_partner']);
        //Route::get('/get-partner-activities', [PartnersController::class, 'partnerActivities')->name('partners.activities');
  
        // Client routes moved to routes/clients.php (unified routes): fetchClientContactNo
  
        //Fetch all contact list of any partner at create note popup at partner detail page
        Route::post('/partners/fetchPartnerContactNo', [PartnersController::class, 'fetchPartnerContactNo']);
  
        //update student application overall status
        Route::post('/partners/update-student-application-overall-status', [PartnersController::class, 'updateStudentApplicationOverallStatus'])->name('partners.updateStudentApplicationOverallStatus');
  
  
        //Add Note To Student
        Route::post('/add-student-note', [PartnersController::class, 'addstudentnote'])->name('partners.addstudentnote');
        //Fetch all partner activity logs
        Route::get('/get-partner-activities', [PartnersController::class, 'activities'])->name('partners.activities');

  
  
        //Update student application commission percentage
        Route::get('/partners/updatecommissionpercentage/{partner_id}', [PartnersController::class, 'updatecommissionpercentage'])->name('partners.updatecommissionpercentage');

        //Update student application commission claimed and other
        Route::get('/partners/updatecommissionclaimed/{partner_id}', [PartnersController::class, 'updatecommissionclaimed'])->name('partners.updatecommissionclaimed');
  
        //Note deadline task complete
        Route::post('/update-note-deadline-completed', 'Admin\AdminController@updatenotedeadlinecompleted');
        //Note deadline extend
        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');
  
  
        //Application Refund
        Route::post('/refund_application', 'Admin\ApplicationsController@refund_application');

        //save student note
        Route::post('/partners/save-student-note', [PartnersController::class, 'saveStudentNote'])->name('partners.saveStudentNote');
  
       //Get partner notes
        Route::get('/get-partner-notes', [PartnersController::class, 'getPartnerNotes'])->name('partners.getPartnerNotes');
  
  
        //admin send msg
        //Twilio api
        Route::post('/verify/is-phone-verify-or-not', 'Admin\SMSTwilioController@isPhoneVerifyOrNot')->name('verify.is-phone-verify-or-not');
        //Route::get('/show-form', 'Admin\SMSTwilioController@showForm')->name('sms.form');
        //Route::post('/send-sms', 'Admin\SMSTwilioController@sendSMS')->name('send.sms');
        Route::post('/verify/send-code', 'Admin\SMSTwilioController@sendVerificationCode')->name('verify.send-code');
        Route::post('/verify/check-code', 'Admin\SMSTwilioController@verifyCode')->name('verify.check-code');



        //Cellcast api
        //Route::post('/verify/is-phone-verify-or-not', 'Admin\SmsController@isPhoneVerifyOrNot')->name('verify.is-phone-verify-or-not');
        //Route::post('/verify/send-code', 'Admin\SmsController@sendVerificationCode')->name('verify.send-code');
        //Route::post('/verify/check-code', 'Admin\SmsController@verifyCode')->name('verify.check-code');
        Route::get('/sms', 'Admin\SmsController@showForm')->name('sms.form');
        Route::post('/sms', 'Admin\SmsController@send')->name('sms.send');
        Route::get('/sms/status/{messageId}', 'Admin\SmsController@checkStatus')->name('sms.status');
        Route::get('/sms/responses', 'Admin\SmsController@getResponses')->name('sms.responses');

  
       // Client routes moved to routes/clients.php (unified routes): sendmsg, is_greview_mail_sent, mail/enhance, download-document
  
       //partner document upload
        Route::post('/upload-partner-document-upload', [PartnersController::class, 'uploadpartnerdocumentupload']);

	// Include unified client routes (accessible by admin only)
	// These routes use /clients/* instead of /admin/clients/*
	require __DIR__ . '/clients.php';

	//Email verify and client self-update routes - REMOVED (HomeController deleted, will be recreated in future)
    //Route::post('email-verify', 'HomeController@emailVerify')->name('emailVerify');
    //Route::get('email-verify-token/{token}', 'HomeController@emailVerifyToken')->name('emailVerifyToken');

    //Client edit form link in send email - REMOVED (HomeController deleted, will be recreated in future)
    //Route::get('/verify-dob/{encoded_id}', 'HomeController@showDobForm');
    //Route::post('/verify-dob', 'HomeController@verifyDob');
    //Route::get('/editclient/{id}', 'HomeController@editclient')->name('editclient');
    //Route::get('/editclient/{encoded_id}', 'HomeController@editClient')->middleware('checkDobSession');
	//Route::post('/editclient', 'HomeController@editclient')->name('editclient');


	//Route::get('/pr-points', 'PRPointsController@index')->name('pr-points.index');
    //Route::post('/pr-points/calculate', 'PRPointsController@calculate')->name('pr-points.calculate');

// Frontend Website - Dynamic Pages Route (Commented out)
// Route::get('/{slug}', 'HomeController@Page')->name('page.slug');
// Auth::routes(); // Removed - already defined above

//Home route - REMOVED (HomeController deleted, will be recreated in future)
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');