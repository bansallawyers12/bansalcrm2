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

//Thank you page after email verification (KEEP - used by client self-update feature)
Route::get('thankyou', 'HomeController@thankyou')->name('thankyou');

//Root login routes - same as admin login
Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('login');
Route::post('/', 'Auth\AdminLoginController@login');

/*---------------Agent Route-------------------*/
require __DIR__ . '/agent.php';
/*********************Admin Panel Start ***********************/
Route::prefix('admin')->group(function() {
	
    //Login and Logout 
		Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
		Route::get('/login', 'Auth\AdminLoginController@showLoginForm');
		Route::post('/login', 'Auth\AdminLoginController@login');
		Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
	
	//General
        Route::get('/dashboard', 'Admin\AdminController@dashboard')->name('admin.dashboard');
		Route::get('/get_customer_detail', 'Admin\AdminController@CustomerDetail')->name('admin.get_customer_detail');
		Route::get('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile');
		Route::post('/my_profile', 'Admin\AdminController@myProfile');
		Route::get('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
		Route::post('/change_password', 'Admin\AdminController@change_password');
		Route::get('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions');
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
		Route::post('/get_chapters', 'Admin\AdminController@getChapters')->name('admin.get_chapters');
		Route::get('/website_setting', 'Admin\AdminController@websiteSetting')->name('admin.website_setting');
		Route::post('/website_setting', 'Admin\AdminController@websiteSetting');
		Route::post('/get_states', 'Admin\AdminController@getStates');
		Route::get('/settings/taxes/returnsetting', 'Admin\AdminController@returnsetting')->name('admin.returnsetting');
		Route::get('/settings/taxes/taxrates', 'Admin\AdminController@taxrates')->name('admin.taxrates');
		Route::get('/settings/taxes/taxrates/create', 'Admin\AdminController@taxratescreate')->name('admin.taxrates.create');
		Route::post('/settings/taxes/taxrates/store', 'Admin\AdminController@savetaxrate')->name('admin.taxrates.store');
		Route::get('/settings/taxes/taxrates/edit/{id}', 'Admin\AdminController@edittaxrates')->name('admin.edittaxrates');
		Route::post('/settings/taxes/taxrates/edit', 'Admin\AdminController@edittaxrates');
		Route::post('/settings/taxes/savereturnsetting', 'Admin\AdminController@returnsetting')->name('admin.savereturnsetting');
		Route::get('/getsubcategories', 'Admin\AdminController@getsubcategories');
		Route::get('/getproductbranch', 'Admin\AdminController@getproductbranch');
		Route::get('/getservicemodal', [ServicesController::class, 'servicemodal']);
		Route::get('/getassigneeajax', 'Admin\AdminController@getassigneeajax');
		Route::get('/getpartnerajax', 'Admin\AdminController@getpartnerajax');
	Route::get('/checkclientexist', 'Admin\AdminController@checkclientexist');
/*CRM route start*/
	
	Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
		Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create'); 
		Route::post('/users/store', [UserController::class, 'store'])->name('admin.users.store');
		Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
		Route::get('/users/view/{id}', [UserController::class, 'view'])->name('admin.users.view');
		Route::post('/users/edit', [UserController::class, 'edit']);
		Route::post('/users/savezone', [UserController::class, 'savezone']);
		
		Route::get('/users/active', [UserController::class, 'active'])->name('admin.users.active');
		Route::get('/users/inactive', [UserController::class, 'inactive'])->name('admin.users.inactive'); 
		Route::get('/users/invited', [UserController::class, 'invited'])->name('admin.users.invited');  
		
	Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index');
	Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff.create'); 
	Route::post('/staff/store', [StaffController::class, 'store'])->name('admin.staff.store');
	Route::get('/staff/edit/{id}', [StaffController::class, 'edit'])->name('admin.staff.edit');
	Route::post('/staff/edit', [StaffController::class, 'edit']);
	
	// Customer routes removed - legacy travel system feature
	
	Route::get('/users/clientlist', [UserController::class, 'clientlist'])->name('admin.users.clientlist');
		Route::get('/users/createclient', [UserController::class, 'createclient'])->name('admin.users.createclient'); 
		Route::post('/users/storeclient', [UserController::class, 'storeclient'])->name('admin.users.storeclient'); 
		Route::get('/users/editclient/{id}', [UserController::class, 'editclient'])->name('admin.users.editclient');
		Route::post('/users/editclient', [UserController::class, 'editclient']); 
		
		Route::post('/followup/store', [FollowupController::class, 'store'])->name('admin.followup.store'); 
		Route::get('/followup/list', [FollowupController::class, 'index'])->name('admin.followup.index'); 
		Route::post('/followup/compose', [FollowupController::class, 'compose'])->name('admin.followup.compose'); 
		 
		Route::get('/usertype', 'Admin\UsertypeController@index')->name('admin.usertype.index');
		Route::get('/usertype/create', 'Admin\UsertypeController@create')->name('admin.usertype.create');  		
		Route::post('/usertype/store', 'Admin\UsertypeController@store')->name('admin.usertype.store');
		Route::get('/usertype/edit/{id}', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');
		Route::post('/usertype/edit', 'Admin\UsertypeController@edit');
		
		Route::get('/userrole', 'Admin\UserroleController@index')->name('admin.userrole.index');
		Route::get('/userrole/create', 'Admin\UserroleController@create')->name('admin.userrole.create');  
		Route::post('/userrole/store', 'Admin\UserroleController@store')->name('admin.userrole.store');
		Route::get('/userrole/edit/{id}', 'Admin\UserroleController@edit')->name('admin.userrole.edit');
		Route::post('/userrole/edit', 'Admin\UserroleController@edit');
		
	//Services Start
		Route::get('/services', [ServicesController::class, 'index'])->name('admin.services.index');
		Route::get('/services/create', [ServicesController::class, 'create'])->name('admin.services.create'); 
		Route::post('/services/store', [ServicesController::class, 'store'])->name('admin.services.store');
		Route::get('/services/edit/{id}', [ServicesController::class, 'edit'])->name('admin.services.edit');
		Route::post('/services/edit', [ServicesController::class, 'edit']);
			     
	  //Manage Contacts Start   
		Route::get('/contact', [ContactController::class, 'index'])->name('admin.managecontact.index'); 
		Route::get('/contact/create', [ContactController::class, 'create'])->name('admin.managecontact.create');
		Route::post('/managecontact/store', [ContactController::class, 'store'])->name('admin.managecontact.store');
		Route::post('/contact/add', [ContactController::class, 'add'])->name('admin.managecontact.add');
		Route::get('/contact/edit/{id}', [ContactController::class, 'edit'])->name('admin.managecontact.edit');
		Route::post('/contact/edit', [ContactController::class, 'edit']);
		Route::post('/contact/storeaddress', [ContactController::class, 'storeaddress']);
		 
	//Leads Start - Updated to modern syntax
	Route::get('/leads', [LeadController::class, 'index'])->name('admin.leads.index');  
	Route::get('/leads/create', [LeadController::class, 'create'])->name('admin.leads.create');
	Route::post('/leads/store', [LeadController::class, 'store'])->name('admin.leads.store');   
	Route::post('/leads/assign', [LeadController::class, 'assign'])->name('admin.leads.assign');    
	Route::get('/leads/detail/{id}', [ClientsController::class, 'leaddetail'])->name('admin.leads.detail');  // Lead detail page (uses client detail view)
	// Removed broken edit routes - leads now use detail page for viewing/editing
	Route::get('/leads/notes/delete/{id}', [LeadController::class, 'leaddeleteNotes']);
	Route::get('/get-notedetail', [LeadController::class, 'getnotedetail']);
	Route::post('/followup/update', [FollowupController::class, 'followupupdate']);
	Route::get('/leads/convert', [LeadController::class, 'convertoClient']);
	Route::get('/leads/pin/{id}', [LeadController::class, 'leadPin']); 	
		//Invoices Start    
		
		// Removed routes for deleted views: lists, email, invoicebyid, history, reminder
		// Route::get('/invoice/lists/{id}', 'Admin\InvoiceController@lists')->name('admin.invoice.lists');  
		Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit')->name('admin.invoice.edit');  
		Route::post('/invoice/edit', 'Admin\InvoiceController@edit');  
		Route::get('/invoice/create', 'Admin\InvoiceController@create')->name('admin.invoice.create');   
		Route::post('/invoice/store', 'Admin\InvoiceController@store')->name('admin.invoice.store'); 
		Route::get('/invoice/detail', 'Admin\InvoiceController@detail')->name('admin.invoice.detail'); 
		// Route::get('/invoice/email/{id}', 'Admin\InvoiceController@email')->name('admin.invoice.email'); 
		// Route::post('/invoice/email', 'Admin\InvoiceController@email'); 
		Route::get('/invoice/editpayment', 'Admin\InvoiceController@editpayment')->name('admin.invoice.editpayment'); 
		// Route::get('/invoice/invoicebyid', 'Admin\InvoiceController@invoicebyid')->name('admin.invoice.invoicebyid'); 
		// Route::get('/invoice/history', 'Admin\InvoiceController@history')->name('admin.invoice.history'); 
		Route::post('/invoice/paymentsave', 'Admin\InvoiceController@paymentsave')->name('admin.invoice.paymentsave'); 
		Route::post('/invoice/editpaymentsave', 'Admin\InvoiceController@editpaymentsave')->name('admin.invoice.editpaymentsave'); 
		Route::post('/invoice/addcomment', 'Admin\InvoiceController@addcomment')->name('admin.invoice.addcomment'); 
		Route::post('/invoice/sharelink', 'Admin\InvoiceController@sharelink')->name('admin.invoice.sharelink'); 
		Route::post('/invoice/disablelink', 'Admin\InvoiceController@disablelink')->name('admin.invoice.disablelink'); 
		Route::get('/invoice/download/{id}', 'Admin\InvoiceController@customer_invoice_download')->name('admin.invoice.customer_invoice_download'); 
		Route::get('/invoice/exportall', 'Admin\InvoiceController@exportall')->name('admin.invoice.exportall'); 
		Route::get('/invoice/printall', 'Admin\InvoiceController@customer_invoice_printall')->name('admin.invoice.customer_invoice_printall'); 
		Route::get('/invoice/print/{id}', 'Admin\InvoiceController@customer_invoice_print')->name('admin.invoice.customer_invoice_print'); 
		// Route::get('/invoice/reminder/{id}', 'Admin\InvoiceController@reminder')->name('admin.invoice.reminder'); 
		// Route::post('/invoice/reminder', 'Admin\InvoiceController@reminder'); 
		Route::post('/invoice/attachfile', 'Admin\InvoiceController@attachfile')->name('admin.invoice.attachfile'); 
		Route::get('/invoice/getattachfile', 'Admin\InvoiceController@getattachfile')->name('admin.invoice.getattachfile'); 
		Route::get('/invoice/removeattachfile', 'Admin\InvoiceController@removeattachfile')->name('admin.invoice.removeattachfile'); 
		Route::get('/invoice/attachfileemail', 'Admin\InvoiceController@attachfileemail')->name('admin.invoice.attachfileemail'); 
	  //Manage Api key 
	 // Route::get('/api-key', 'Admin\ApiController@index')->name('admin.apikey.index');
	  //Manage Api key  
				      
	//Email Templates Pages
		Route::get('/email_templates', 'Admin\EmailTemplateController@index')->name('admin.email.index');
		Route::get('/email_templates/create', 'Admin\EmailTemplateController@create')->name('admin.email.create');
		Route::post('/email_templates/store', 'Admin\EmailTemplateController@store')->name('admin.email.store');
		Route::get('/edit_email_template/{id}', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');
		Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate');	
		
	//SEO Tool
		Route::get('/edit_seo/{id}', 'Admin\AdminController@editSeo')->name('admin.edit_seo');
		Route::post('/edit_seo', 'Admin\AdminController@editSeo');
		
	Route::get('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');
	Route::post('/api-key', 'Admin\AdminController@editapi');	
	
	//clients Start
		Route::get('/clients', [ClientsController::class, 'index'])->name('admin.clients.index');
		Route::get('/clients/create', [ClientsController::class, 'create'])->name('admin.clients.create'); 
		Route::post('/clients/store', [ClientsController::class, 'store'])->name('admin.clients.store');
		Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('admin.clients.edit');
		Route::post('/clients/edit', [ClientsController::class, 'edit']);
  
		Route::post('/clients/followup/store', [ClientsController::class, 'followupstore']);
        Route::post('/clients/followup_application/store_application', [ClientsController::class, 'followupstore_application']);
  
		Route::post('/clients/followup/retagfollowup', [ClientsController::class, 'retagfollowup']);
		Route::get('/clients/changetype/{id}/{type}', [ClientsController::class, 'changetype']);
		Route::get('/document/download/pdf/{id}', [ClientsController::class, 'downloadpdf']);
		Route::get('/clients/removetag', [ClientsController::class, 'removetag']);
		Route::get('/clients/detail/{id}', [ClientsController::class, 'clientdetail'])->name('admin.clients.detail');	
		Route::get('/clients/get-recipients', [ClientsController::class, 'getrecipients'])->name('admin.clients.getrecipients');
		Route::get('/clients/get-onlyclientrecipients', [ClientsController::class, 'getonlyclientrecipients'])->name('admin.clients.getonlyclientrecipients');
		// Global search endpoint with rate limiting (60 requests per minute)
		Route::get('/clients/get-allclients', [ClientsController::class, 'getallclients'])
			->name('admin.clients.getallclients')
			->middleware('throttle:60,1');
		Route::get('/clients/change_assignee', [ClientsController::class, 'change_assignee']);
		Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('admin.clients.gettemplates');
		Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('admin.clients.sendmail');
		Route::post('/create-note', [ClientsController::class, 'createnote'])->name('admin.clients.createnote');
		Route::get('/getnotedetail', [ClientsController::class, 'getnotedetail'])->name('admin.clients.getnotedetail');
		Route::get('/deletenote', [ClientsController::class, 'deletenote'])->name('admin.clients.deletenote');
  
        Route::get('/deleteactivitylog', [ClientsController::class, 'deleteactivitylog'])->name('admin.clients.deleteactivitylog');

  
         Route::post('/not-picked-call', [ClientsController::class, 'notpickedcall'])->name('admin.clients.notpickedcall');
		//prospects Start  
		Route::get('/prospects', [ClientsController::class, 'prospects'])->name('admin.clients.prospects');
		Route::get('/viewnotedetail', [ClientsController::class, 'viewnotedetail']);
		Route::get('/viewapplicationnote', [ClientsController::class, 'viewapplicationnote']);
		Route::post('/saveprevvisa', [ClientsController::class, 'saveprevvisa']);	
		Route::post('/saveonlineprimaryform', [ClientsController::class, 'saveonlineform']);	
		Route::post('/saveonlinesecform', [ClientsController::class, 'saveonlineform']);	
		Route::post('/saveonlinechildform', [ClientsController::class, 'saveonlineform']);	
		//archived Start  
		Route::get('/archived', [ClientsController::class, 'archived'])->name('admin.clients.archived');
		Route::get('/change-client-status', [ClientsController::class, 'updateclientstatus'])->name('admin.clients.updateclientstatus');
		Route::get('/get-activities', [ClientsController::class, 'activities'])->name('admin.clients.activities');
		Route::get('/get-application-lists', [ClientsController::class, 'getapplicationlists'])->name('admin.clients.getapplicationlists');
		Route::post('/saveapplication', [ClientsController::class, 'saveapplication'])->name('admin.clients.saveapplication');
		Route::get('/get-notes', [ClientsController::class, 'getnotes'])->name('admin.clients.getnotes');
		Route::get('/convertapplication', [ClientsController::class, 'convertapplication'])->name('admin.clients.convertapplication');
		Route::get('/deleteservices', [ClientsController::class, 'deleteservices'])->name('admin.clients.deleteservices');
		Route::post('/upload-document', [ClientsController::class, 'uploaddocument'])->name('admin.clients.uploaddocument');
		Route::get('/deletedocs', [ClientsController::class, 'deletedocs'])->name('admin.clients.deletedocs');
		Route::post('/renamedoc', [ClientsController::class, 'renamedoc'])->name('admin.clients.renamedoc');
		
		Route::post('/savetoapplication', [ClientsController::class, 'savetoapplication']);
		
		//products Start   
		Route::get('/products', [ProductsController::class, 'index'])->name('admin.products.index');
		Route::get('/products/create', [ProductsController::class, 'create'])->name('admin.products.create'); 
		Route::post('/products/store', [ProductsController::class, 'store'])->name('admin.products.store');
		Route::get('/products/edit/{id}', [ProductsController::class, 'edit'])->name('admin.products.edit');
		Route::post('/products/edit', [ProductsController::class, 'edit']);
		Route::post('/products-import', [ProductsController::class, 'import'])->name('admin.products.import');

		
		Route::get('/products/detail/{id}', [ProductsController::class, 'detail'])->name('admin.products.detail');	 
		 Route::get('/products/get-recipients', [ProductsController::class, 'getrecipients'])->name('admin.products.getrecipients');
		Route::get('/products/get-allclients', [ProductsController::class, 'getallclients'])->name('admin.products.getallclients');
		
		//Partner Start
		Route::get('/partners', [PartnersController::class, 'index'])->name('admin.partners.index');
		Route::get('/partners/create', [PartnersController::class, 'create'])->name('admin.partners.create');  
		Route::post('/partners/store', [PartnersController::class, 'store'])->name('admin.partners.store');
		Route::get('/partners/edit/{id}', [PartnersController::class, 'edit'])->name('admin.partners.edit');
		Route::post('/partners/edit', [PartnersController::class, 'edit']);
		Route::get('/getpaymenttype', [PartnersController::class, 'getpaymenttype'])->name('admin.partners.getpaymenttype');
		
		Route::get('/partners/detail/{id}', [PartnersController::class, 'detail'])->name('admin.partners.detail');	 
		 Route::get('/partners/get-recipients', [PartnersController::class, 'getrecipients'])->name('admin.partners.getrecipients');
		Route::get('/partners/get-allclients', [PartnersController::class, 'getallclients'])->name('admin.partners.getallclients');
	
		//Branch Start
		Route::get('/branch', 'Admin\BranchesController@index')->name('admin.branch.index'); 
		Route::get('/branch/create', 'Admin\BranchesController@create')->name('admin.branch.create');  
		Route::post('/branch/store', 'Admin\BranchesController@store')->name('admin.branch.store');
		Route::get('/branch/edit/{id}', 'Admin\BranchesController@edit')->name('admin.branch.edit');
		Route::get('/branch/view/{id}', 'Admin\BranchesController@view')->name('admin.branch.userview');
		Route::get('/branch/view/client/{id}', 'Admin\BranchesController@viewclient')->name('admin.branch.clientview'); 
		Route::post('/branch/edit', 'Admin\BranchesController@edit');
		 
		//Quotations Start
		Route::get('/quotations', 'Admin\QuotationsController@index')->name('admin.quotations.index'); 
		
		Route::get('/quotations/client', 'Admin\QuotationsController@client')->name('admin.quotations.client');  
		Route::get('/quotations/client/create/{id}', 'Admin\QuotationsController@create')->name('admin.quotations.create');  
		Route::post('/quotations/store', 'Admin\QuotationsController@store')->name('admin.quotations.store');
		Route::get('/quotations/edit/{id}', 'Admin\QuotationsController@edit')->name('admin.quotations.edit');
		Route::post('/quotations/edit', 'Admin\QuotationsController@edit');
		 
		Route::get('/quotations/template', 'Admin\QuotationsController@template')->name('admin.quotations.template.index');   
		Route::get('/quotations/template/create', 'Admin\QuotationsController@template_create')->name('admin.quotations.template.create');  
		Route::post('/quotations/template/store', 'Admin\QuotationsController@template_store')->name('admin.quotations.template.store');  
		Route::get('/quotations/template/edit/{id}', 'Admin\QuotationsController@template_edit')->name('admin.quotations.template.edit');  
		Route::post('/quotations/template/edit', 'Admin\QuotationsController@template_edit');  
		Route::get('/quotation/detail/{id}', 'Admin\QuotationsController@quotationDetail');
		Route::get('/quotation/preview/{id}', 'Admin\QuotationsController@quotationpreview');
		//archived Start  
		Route::get('quotations/archived', 'Admin\QuotationsController@archived')->name('admin.quotations.archived');
		Route::get('quotations/changestatus', 'Admin\QuotationsController@changestatus')->name('admin.quotations.changestatus');
		Route::post('quotations/sendmail', 'Admin\QuotationsController@sendmail')->name('admin.quotations.sendmail');
		
		Route::get('getpartner', 'Admin\AdminController@getpartner')->name('admin.quotations.getpartner');
		Route::get('getpartnerbranch', 'Admin\AdminController@getpartnerbranch')->name('admin.quotations.getpartnerbranch');
		Route::get('getsubjects', 'Admin\AdminController@getsubjects');
		Route::get('getbranchproduct', 'Admin\AdminController@getbranchproduct')->name('admin.quotations.getbranchproduct');
		Route::get('getproduct', 'Admin\AdminController@getproduct')->name('admin.quotations.getproduct');
		Route::get('getbranch', 'Admin\AdminController@getbranch')->name('admin.quotations.getbranch');
		Route::get('getnewPartnerbranch', 'Admin\AdminController@getnewPartnerbranch')->name('admin.quotations.getnewPartnerbranch');
		 
		
		//Agent Start   
	/* 	Route::get('/agents', 'Admin\AgentController@index')->name('admin.agents.index');
		Route::get('/agents/create', 'Admin\AgentController@create')->name('admin.agents.create'); 
		Route::post('/agents/store', 'Admin\AgentController@store')->name('admin.agents.store');
		
		Route::post('/agents/edit', 'Admin\AgentController@edit')->name('admin.agents.edit'); */
		
		Route::get('/agents/active', 'Admin\AgentController@active')->name('admin.agents.active'); 
		Route::get('/agents/inactive', 'Admin\AgentController@inactive')->name('admin.agents.inactive');  
		Route::get('/agents/show/{id}', 'Admin\AgentController@show')->name('admin.agents.show'); 
		Route::get('/agents/create', 'Admin\AgentController@create')->name('admin.agents.create'); 
		Route::get('/agents/import', 'Admin\AgentController@import')->name('admin.agents.import'); 
		Route::post('/agents/store', 'Admin\AgentController@store')->name('admin.agents.store'); 
		Route::get('/agent/detail/{id}', 'Admin\AgentController@detail')->name('admin.agents.detail'); 
		Route::post('/agents/savepartner', 'Admin\AgentController@savepartner'); 
		 Route::get('/agents/edit/{id}', 'Admin\AgentController@edit')->name('admin.agents.edit');
		 Route::post('/agents/edit', 'Admin\AgentController@edit');
		 Route::get('/agents/import/business', 'Admin\AgentController@businessimport');
		 Route::get('/agents/import/individual', 'Admin\AgentController@individualimport');
		//Task Start 
		Route::get('/tasks', 'Admin\TasksController@index')->name('admin.tasks.index');
		Route::get('/tasks/archive/{id}', 'Admin\TasksController@taskArchive')->name('admin.tasks.archive');
		Route::get('/tasks/create', 'Admin\TasksController@create')->name('admin.tasks.create'); 
		Route::post('/tasks/store', 'Admin\TasksController@store')->name('admin.tasks.store');
			Route::post('/tasks/groupstore', 'Admin\TasksController@groupstore')->name('admin.tasks.groupstore');
			Route::post('/tasks/deletegroup', 'Admin\TasksController@deletegroup')->name('admin.tasks.deletegroup');
		Route::get('/get-tasks', 'Admin\TasksController@gettasks')->name('admin.tasks.gettasks');
		Route::get('/get-task-detail', 'Admin\TasksController@taskdetail')->name('admin.tasks.gettaskdetail');
		Route::post('/update_task_comment', 'Admin\TasksController@update_task_comment');
		Route::post('/update_task_description', 'Admin\TasksController@update_task_description');
		Route::post('/update_task_status', 'Admin\TasksController@update_task_status');
		Route::post('/update_task_priority', 'Admin\TasksController@update_task_priority');
		Route::post('/updateduedate', 'Admin\TasksController@updateduedate');
		Route::get('/task/change_assignee', 'Admin\TasksController@change_assignee');  
		//Route::get('/tasks/edit/{id}', 'Admin\TasksController@edit')->name('admin.tasks.edit');
		//Route::post('/tasks/edit', 'Admin\TasksController@edit')->name('admin.tasks.edit');
		
		//General Invoice Start 
		Route::get('/invoice/general-invoice', 'Admin\InvoiceController@general_invoice')->name('admin.invoice.general-invoice'); 
		
		Route::get('/applications/detail/{id}', 'Admin\ApplicationsController@detail')->name('admin.applications.detail'); 	 
		Route::post('/interested-service', [ClientsController::class, 'interestedService']); 	 
		Route::post('/edit-interested-service', [ClientsController::class, 'editinterestedService']); 	 
		Route::get('/get-services', [ClientsController::class, 'getServices']); 	 
		Route::get('/showproductfeeserv', [ClientsController::class, 'showproductfeeserv']);Route::post('/servicesavefee', [ClientsController::class, 'servicesavefee']);		 	 
		Route::post('/upload-mail', [ClientsController::class, 'uploadmail']); 	 
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
		Route::get('/invoice/paid', 'Admin\InvoiceController@paid')->name('admin.invoice.paid');  	 
		Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('admin.invoice.unpaid');  	 
		//Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('admin.invoice.unpaid');  
		Route::get('/invoice/', 'Admin\InvoiceController@index')->name('admin.invoice.index');	
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
		Route::get('/payment', 'Admin\AccountController@payment')->name('admin.account.payment');
		Route::get('/income-sharing/payables/unpaid', 'Admin\AccountController@payableunpaid')->name('admin.account.payableunpaid');
		Route::get('/income-sharing/payables/paid', 'Admin\AccountController@payablepaid')->name('admin.account.payablepaid');
		Route::post('/income-payment-store', 'Admin\AccountController@incomepaymentstore');
		Route::get('/revert-payment', 'Admin\AccountController@revertpayment');
		Route::get('/income-sharing/receivables/unpaid', 'Admin\AccountController@receivableunpaid')->name('admin.account.receivableunpaid');
		Route::get('/income-sharing/receivables/paid', 'Admin\AccountController@receivablepaid')->name('admin.account.receivablepaid');
		Route::get('/group-invoice/unpaid', 'Admin\InvoiceController@unpaidgroupinvoice')->name('admin.invoice.unpaidgroupinvoice');
		Route::get('/group-invoice/paid', 'Admin\InvoiceController@paidgroupinvoice')->name('admin.invoice.paidgroupinvoice');
		Route::get('/group-invoice/create', 'Admin\InvoiceController@creategroupinvoice')->name('admin.invoice.creategroupinvoice'); 
		Route::get('/invoice-schedules', 'Admin\InvoiceController@invoiceschedules')->name('admin.invoice.invoiceschedules'); 
		Route::post('/paymentschedule', 'Admin\InvoiceController@paymentschedule')->name('admin.invoice.paymentschedule'); 
		Route::post('/setup-paymentschedule', 'Admin\InvoiceController@setuppaymentschedule'); 
		Route::post('/editpaymentschedule', 'Admin\InvoiceController@editpaymentschedule')->name('admin.invoice.editpaymentschedule'); 
		Route::get('/scheduleinvoicedetail', 'Admin\InvoiceController@scheduleinvoicedetail'); 
		Route::get('/addscheduleinvoicedetail', 'Admin\InvoiceController@addscheduleinvoicedetail'); 
		Route::get('/get-all-paymentschedules', 'Admin\InvoiceController@getallpaymentschedules'); 
		Route::get('/deletepaymentschedule', 'Admin\InvoiceController@deletepaymentschedule'); 
		Route::get('/applications/preview-schedules/{id}', 'Admin\InvoiceController@apppreviewschedules'); 
		
		  
		//Product Type Start    
		Route::get('/product-type', 'Admin\ProductTypeController@index')->name('admin.feature.producttype.index');  
		Route::get('/product-type/create', 'Admin\ProductTypeController@create')->name('admin.feature.producttype.create');  
		Route::post('/product-type/store', 'Admin\ProductTypeController@store')->name('admin.feature.producttype.store');   
		Route::get('/product-type/edit/{id}', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		Route::post('/product-type/edit', 'Admin\ProductTypeController@edit');
		
		Route::get('/profiles', 'Admin\ProfileController@index')->name('admin.feature.profiles.index');  
		Route::get('/profiles/create', 'Admin\ProfileController@create')->name('admin.feature.profiles.create');  
		Route::post('/profiles/store', 'Admin\ProfileController@store')->name('admin.feature.profiles.store');  
		Route::get('/profiles/edit/{id}', 'Admin\ProfileController@edit')->name('admin.feature.profiles.edit');
		Route::post('/profiles/edit', 'Admin\ProfileController@edit');
		//Partner Type Start    
		Route::get('/partner-type', 'Admin\PartnerTypeController@index')->name('admin.feature.partnertype.index');  
		Route::get('/partner-type/create', 'Admin\PartnerTypeController@create')->name('admin.feature.partnertype.create');  
		Route::post('/partner-type/store', 'Admin\PartnerTypeController@store')->name('admin.feature.partnertype.store');   
		Route::get('/partner-type/edit/{id}', 'Admin\PartnerTypeController@edit')->name('admin.feature.partnertype.edit');
		Route::post('/partner-type/edit', 'Admin\PartnerTypeController@edit');
		   
		//Visa Type Start    
		Route::get('/visa-type', 'Admin\VisaTypeController@index')->name('admin.feature.visatype.index');  
		Route::get('/visa-type/create', 'Admin\VisaTypeController@create')->name('admin.feature.visatype.create');  
		Route::post('/visa-type/store', 'Admin\VisaTypeController@store')->name('admin.feature.visatype.store');     
		Route::get('/visa-type/edit/{id}', 'Admin\VisaTypeController@edit')->name('admin.feature.visatype.edit');
		Route::post('/visa-type/edit', 'Admin\VisaTypeController@edit');
		
		//Master Category Start    
		Route::get('/master-category', 'Admin\MasterCategoryController@index')->name('admin.feature.mastercategory.index');  
		Route::get('/master-category/create', 'Admin\MasterCategoryController@create')->name('admin.feature.mastercategory.create');  
		Route::post('/master-category/store', 'Admin\MasterCategoryController@store')->name('admin.feature.mastercategory.store');     
		Route::get('/master-category/edit/{id}', 'Admin\MasterCategoryController@edit')->name('admin.feature.mastercategory.edit');
		Route::post('/master-category/edit', 'Admin\MasterCategoryController@edit');
		
		//Lead Service Start    
		Route::get('/lead-service', 'Admin\LeadServiceController@index')->name('admin.feature.leadservice.index');  
		Route::get('/lead-service/create', 'Admin\LeadServiceController@create')->name('admin.feature.leadservice.create');  
		Route::post('/lead-service/store', 'Admin\LeadServiceController@store')->name('admin.feature.leadservice.store');     
		Route::get('/lead-service/edit/{id}', 'Admin\LeadServiceController@edit')->name('admin.feature.leadservice.edit');
		Route::post('/lead-service/edit', 'Admin\LeadServiceController@edit');
		
		//Tax Start  
		Route::get('/tax', 'Admin\TaxController@index')->name('admin.feature.tax.index');  
		Route::get('/tax/create', 'Admin\TaxController@create')->name('admin.feature.tax.create');  
		Route::post('/tax/store', 'Admin\TaxController@store')->name('admin.feature.tax.store');  
		Route::get('/tax/edit/{id}', 'Admin\TaxController@edit')->name('admin.feature.tax.edit');
		Route::post('/tax/edit', 'Admin\TaxController@edit');
		
		//Subject Area Start  	
		Route::get('/subjectarea', 'Admin\SubjectAreaController@index')->name('admin.feature.subjectarea.index');  
		Route::get('/subjectarea/create', 'Admin\SubjectAreaController@create')->name('admin.feature.subjectarea.create');  
		Route::post('/subjectarea/store', 'Admin\SubjectAreaController@store')->name('admin.feature.subjectarea.store');  
		Route::get('/subjectarea/edit/{id}', 'Admin\SubjectAreaController@edit')->name('admin.feature.subjectarea.edit');
		Route::post('/subjectarea/edit', 'Admin\SubjectAreaController@edit');
		
		//Subject Start  
		Route::get('/subject', 'Admin\SubjectController@index')->name('admin.feature.subject.index');
		Route::get('/subject/create', 'Admin\SubjectController@create')->name('admin.feature.subject.create');  
		Route::post('/subject/store', 'Admin\SubjectController@store')->name('admin.feature.subject.store');  
		Route::get('/subject/edit/{id}', 'Admin\SubjectController@edit')->name('admin.feature.subject.edit');
		Route::post('/subject/edit', 'Admin\SubjectController@edit');
		
		//Source Start
		Route::get('/source', 'Admin\SourceController@index')->name('admin.feature.source.index');  
		Route::get('/source/create', 'Admin\SourceController@create')->name('admin.feature.source.create');  
		Route::post('source/store', 'Admin\SourceController@store')->name('admin.feature.source.store');     
		Route::get('/source/edit/{id}', 'Admin\SourceController@edit')->name('admin.feature.source.edit');
		Route::post('/source/edit', 'Admin\SourceController@edit');
		
		//Tags Start
		Route::get('/tags', 'Admin\TagController@index')->name('admin.feature.tags.index');  
		Route::get('/tags/create', 'Admin\TagController@create')->name('admin.feature.tags.create');  
		Route::post('tags/store', 'Admin\TagController@store')->name('admin.feature.tags.store');     
		Route::get('/tags/edit/{id}', 'Admin\TagController@edit')->name('admin.feature.tags.edit');
		Route::post('/tags/edit', 'Admin\TagController@edit');
		
		//Checklist Start
		Route::get('/checklist', 'Admin\ChecklistController@index')->name('admin.checklist.index');  
		Route::get('/checklist/create', 'Admin\ChecklistController@create')->name('admin.checklist.create');  
		Route::post('checklist/store', 'Admin\ChecklistController@store')->name('admin.checklist.store');     
		Route::get('/checklist/edit/{id}', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');
		Route::post('/checklist/edit', 'Admin\ChecklistController@edit')->name('admin.checklist.update');
		
		//Enquiry Source Start
		Route::get('/enquirysource', 'Admin\EnquirySourceController@index')->name('admin.enquirysource.index');  
		Route::get('/enquirysource/create', 'Admin\EnquirySourceController@create')->name('admin.enquirysource.create');  
		Route::post('enquirysource/store', 'Admin\EnquirySourceController@store')->name('admin.enquirysource.store');     
		Route::get('/enquirysource/edit/{id}', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.edit');
		Route::post('/enquirysource/edit', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.update');
		
		//FeeType Start
		Route::get('/feetype', 'Admin\FeeTypeController@index')->name('admin.feetype.index');  
		Route::get('/feetype/create', 'Admin\FeeTypeController@create')->name('admin.feetype.create');  
		Route::post('feetype/store', 'Admin\FeeTypeController@store')->name('admin.feetype.store');     
		Route::get('/feetype/edit/{id}', 'Admin\FeeTypeController@edit')->name('admin.feetype.edit');
		Route::post('/feetype/edit', 'Admin\FeeTypeController@edit')->name('admin.feetype.update');
		
		
		//workflow Start
		Route::get('/workflow', 'Admin\WorkflowController@index')->name('admin.workflow.index');  
		Route::get('/workflow/create', 'Admin\WorkflowController@create')->name('admin.workflow.create');  
		Route::post('workflow/store', 'Admin\WorkflowController@store')->name('admin.workflow.store');     
		Route::get('/workflow/edit/{id}', 'Admin\WorkflowController@edit')->name('admin.workflow.edit');
		Route::get('/workflow/deactivate-workflow/{id}', 'Admin\WorkflowController@deactivateWorkflow')->name('admin.workflow.deactivate');
		Route::get('/workflow/activate-workflow/{id}', 'Admin\WorkflowController@activateWorkflow')->name('admin.workflow.activate');
		Route::post('/workflow/edit', 'Admin\WorkflowController@edit')->name('admin.workflow.update');
		
		Route::post('/partner/saveagreement', [PartnersController::class, 'saveagreement']);
		Route::post('/partner/create-contact', [PartnersController::class, 'createcontact']);
		Route::get('/get-contacts', [PartnersController::class, 'getcontacts']);
		Route::get('/deletecontact', [PartnersController::class, 'deletecontact']);
		Route::get('/getcontactdetail', [PartnersController::class, 'getcontactdetail']);
		Route::post('/partners-import', [PartnersController::class, 'import'])->name('admin.partners.import');
		
		Route::post('/partner/create-branch', [PartnersController::class, 'createbranch']);
		Route::get('/get-branches', [PartnersController::class, 'getbranch']);
		Route::get('/getbranchdetail', [PartnersController::class, 'getbranchdetail']);
		Route::get('/deletebranch', [PartnersController::class, 'deletebranch']);
		
		Route::post('/saveacademic', [ProductsController::class, 'saveacademic']);
		Route::post('/saveotherinfo', [ProductsController::class, 'saveotherinfo']);
		Route::get('/product/getotherinfo', [ProductsController::class, 'getotherinfo']);
		Route::get('/get-all-fees', [ProductsController::class, 'getallfees']);
		Route::post('/savefee', [ProductsController::class, 'savefee']);
		
		Route::get('/getfeeoptionedit', [ProductsController::class, 'editfee']);
		Route::post('/editfee', [ProductsController::class, 'editfeeform']);
		Route::get('/deletefee', [ProductsController::class, 'deletefee']);
		
		
		Route::post('/partner/addtask', [PartnersController::class, 'addtask']);
		Route::get('/partner/get-tasks', [PartnersController::class, 'gettasks']);
		Route::get('/partner/get-task-detail', [PartnersController::class, 'taskdetail']);
		Route::post('/partner/savecomment', [PartnersController::class, 'savecomment']);
		Route::get('/change-task-status', [PartnersController::class, 'changetaskstatus']);
		Route::get('/change-task-priority', [PartnersController::class, 'changetaskpriority']);
		
		Route::post('/promotion/store', 'Admin\PromotionController@store');
		Route::post('/promotion/edit', 'Admin\PromotionController@edit');
		Route::get('/get-promotions', 'Admin\PromotionController@getpromotions');
		Route::get('/getpromotioneditform', 'Admin\PromotionController@getpromotioneditform');
		Route::get('/change-promotion-status', 'Admin\PromotionController@changepromotionstatus');
		
		
		//Applications Start    
		Route::get('/applications', 'Admin\ApplicationsController@index')->name('admin.applications.index');  
		Route::get('/applications/create', 'Admin\ApplicationsController@create')->name('admin.applications.create');  
		Route::post('/discontinue_application', 'Admin\ApplicationsController@discontinue_application');  
		Route::post('/revert_application', 'Admin\ApplicationsController@revert_application');  
		Route::post('/applications-import', 'Admin\ApplicationsController@import')->name('admin.applications.import');
		//Route::post('/product-type/store', 'Admin\ProductTypeController@store')->name('admin.feature.producttype.store');   
		//Route::get('/product-type/edit/{id}', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		//Route::post('/product-type/edit', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		Route::get('/office-visits', 'Admin\OfficeVisitController@index')->name('admin.officevisits.index');  
		Route::get('/office-visits/waiting', 'Admin\OfficeVisitController@waiting')->name('admin.officevisits.waiting');  
		Route::get('/office-visits/attending', 'Admin\OfficeVisitController@attending')->name('admin.officevisits.attending');  
		Route::get('/office-visits/completed', 'Admin\OfficeVisitController@completed')->name('admin.officevisits.completed'); 
		Route::get('/office-visits/archived', 'Admin\OfficeVisitController@archived')->name('admin.officevisits.archived');   
		Route::get('/office-visits/create', 'Admin\OfficeVisitController@create')->name('admin.officevisits.create'); 
		Route::post('/checkin', 'Admin\OfficeVisitController@checkin');	
		Route::get('/get-checkin-detail', 'Admin\OfficeVisitController@getcheckin');	
		Route::post('/update_visit_purpose', 'Admin\OfficeVisitController@update_visit_purpose');	
		Route::post('/update_visit_comment', 'Admin\OfficeVisitController@update_visit_comment');	
		Route::post('/attend_session', 'Admin\OfficeVisitController@attend_session');	
		Route::post('/complete_session', 'Admin\OfficeVisitController@complete_session');	
		Route::get('/office-visits/change_assignee', 'Admin\OfficeVisitController@change_assignee');  
		//Route::post('/agents/store', 'Admin\AgentController@store')->name('admin.agents.store'); 
		//Route::get('/agent/detail/{id}', 'Admin\AgentController@detail')->name('admin.agents.detail'); 
		
		Route::get('/enquiries', 'Admin\EnquireController@index')->name('admin.enquiries.index'); 
		Route::get('/enquiries/archived-enquiries', 'Admin\EnquireController@Archived')->name('admin.enquiries.archived'); 
		Route::get('/enquiries/covertenquiry/{id}', 'Admin\EnquireController@covertenquiry'); 
		Route::get('/enquiries/archived/{id}', 'Admin\EnquireController@archivedenquiry'); 
	
		//Audit Logs Start   
		Route::get('/audit-logs', 'Admin\AuditLogController@index')->name('admin.auditlogs.index');
		
		//Reports Start   
		Route::get('/report/client', 'Admin\ReportController@client')->name('admin.reports.client');
		Route::get('/report/application', 'Admin\ReportController@application')->name('admin.reports.application');
		Route::get('/report/invoice', 'Admin\ReportController@invoice')->name('admin.reports.invoice');
		Route::get('/report/office-visit', 'Admin\ReportController@office_visit')->name('admin.reports.office-visit');
		Route::get('/report/sale-forecast/application', 'Admin\ReportController@saleforecast_application')->name('admin.reports.saleforecast-application');  
		Route::get('/report/sale-forecast/interested-service', 'Admin\ReportController@interested_service')->name('admin.reports.interested-service');
		Route::get('/report/task/personal-task-report', 'Admin\ReportController@personal_task')->name('admin.reports.personal-task-report');
		Route::get('/report/task/office-task-report', 'Admin\ReportController@office_task')->name('admin.reports.office-task-report'); 
		Route::get('/reports/visaexpires', 'Admin\ReportController@visaexpires'); 
		Route::get('/followup-dates', 'Admin\ReportController@followupdates'); 
		Route::get('/reports/agreementexpires', 'Admin\ReportController@agreementexpires');
		Route::get('/report/noofpersonofficevisit', 'Admin\ReportController@noofpersonofficevisit')->name('admin.reports.noofpersonofficevisit');
		Route::get('/report/clientrandomlyselectmonthly', 'Admin\ReportController@clientrandomlyselectmonthly')->name('admin.reports.clientrandomlyselectmonthly');
		
        Route::post('/report/save_random_client_selection', 'Admin\ReportController@saveclientrandomlyselectmonthly');


		Route::post('/save_tag', [ClientsController::class, 'save_tag']); 	 
		
		
		//Email Start
		Route::get('/emails', 'Admin\EmailController@index')->name('admin.emails.index');  
		Route::get('/emails/create', 'Admin\EmailController@create')->name('admin.emails.create');  
		Route::post('emails/store', 'Admin\EmailController@store')->name('admin.emails.store');     
		Route::get('/emails/edit/{id}', 'Admin\EmailController@edit')->name('admin.emails.edit');
		Route::post('/emails/edit', 'Admin\EmailController@edit')->name('admin.emails.update');
		
		//Crm Email Template Start
		Route::get('/crm_email_template', 'Admin\CrmEmailTemplateController@index')->name('admin.crmemailtemplate.index');  
		Route::get('/crm_email_template/create', 'Admin\CrmEmailTemplateController@create')->name('admin.crmemailtemplate.create');  
		Route::post('crm_email_template/store', 'Admin\CrmEmailTemplateController@store')->name('admin.crmemailtemplate.store');     
		Route::get('/crm_email_template/edit/{id}', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.edit');
		Route::post('/crm_email_template/edit', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.update'); 
		
		Route::get('/gen-settings', 'Admin\AdminController@gensettings')->name('admin.gensettings.index');
		Route::post('/gen-settings/update', 'Admin\AdminController@gensettingsupdate')->name('admin.gensettings.update');
		
		Route::get('/fetch-notification', 'Admin\AdminController@fetchnotification');
		Route::get('/fetch-messages', 'Admin\AdminController@fetchmessages');
	    Route::get('/upload-checklists', 'Admin\UploadChecklistController@index')->name('admin.upload_checklists.index');
		Route::post('/upload-checklists/store', 'Admin\UploadChecklistController@store')->name('admin.upload_checklistsupload');		
		Route::get('/teams', 'Admin\TeamController@index')->name('admin.teams.index');
		Route::get('/teams/edit/{id}', 'Admin\TeamController@edit')->name('admin.teams.edit');
		Route::post('/teams/edit', 'Admin\TeamController@edit');
		Route::post('/teams/store', 'Admin\TeamController@store')->name('admin.teamsupload');	
		Route::get('/all-notifications', 'Admin\AdminController@allnotification')->name('admin.notifications.index');
		Route::post('/notifications/mark-read', 'Admin\AdminController@markNotificationAsRead')->name('admin.notifications.mark-read');
		Route::post('/notifications/mark-all-read', 'Admin\AdminController@markAllNotificationsAsRead')->name('admin.notifications.mark-all-read');	
		
		// Assignee modulle
		Route::resource('/assignee', Admin\AssigneeController::class);
        Route::get('/assignee-completed', 'Admin\AssigneeController@completed'); //completed list only

        Route::post('/update-task-completed', 'Admin\AssigneeController@updatetaskcompleted'); //update task to be completed
        Route::post('/update-task-not-completed', 'Admin\AssigneeController@updatetasknotcompleted'); //update task to be not completed

        Route::get('/assigned_by_me', 'Admin\AssigneeController@assigned_by_me')->name('assignee.assigned_by_me'); //assigned by me
        Route::get('/assigned_to_me', 'Admin\AssigneeController@assigned_to_me')->name('assignee.assigned_to_me'); //assigned to me

        Route::delete('/destroy_by_me/{note_id}', 'Admin\AssigneeController@destroy_by_me')->name('assignee.destroy_by_me'); //assigned by me
        Route::delete('/destroy_to_me/{note_id}', 'Admin\AssigneeController@destroy_to_me')->name('assignee.destroy_to_me'); //assigned to me

        Route::get('/activities_completed', 'Admin\AssigneeController@activities_completed')->name('assignee.activities_completed'); //activities completed


        Route::delete('/destroy_activity/{note_id}', 'Admin\AssigneeController@destroy_activity')->name('assignee.destroy_activity'); //delete activity
        Route::delete('/destroy_complete_activity/{note_id}', 'Admin\AssigneeController@destroy_complete_activity')->name('assignee.destroy_complete_activity'); //delete completed activity

        //Save Personal Task
        Route::post('/clients/personalfollowup/store', [ClientsController::class, 'personalfollowup']);
        Route::post('/clients/updatefollowup/store', [ClientsController::class, 'updatefollowup']);
        Route::post('/clients/reassignfollowup/store', [ClientsController::class, 'reassignfollowupstore']);
        
        //update attending session to be completed
        Route::post('/clients/update-session-completed', [ClientsController::class, 'updatesessioncompleted'])->name('admin.clients.updatesessioncompleted');
        
        //Total person waiting and total activity counter
        Route::get('/fetch-InPersonWaitingCount', 'Admin\AdminController@fetchInPersonWaitingCount');
        Route::get('/fetch-TotalActivityCount', 'Admin\AdminController@fetchTotalActivityCount');
        
         
        //for datatble
        Route::get('/activities', 'Admin\AssigneeController@activities')->name('assignee.activities');
        Route::get('/activities/list','Admin\AssigneeController@getActivities')->name('activities.list');
        
        Route::post('/get_assignee_list', 'Admin\AssigneeController@get_assignee_list');
        //For email and contact number uniqueness
        Route::post('/is_email_unique', 'Admin\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'Admin\LeadController@is_contactno_unique');
        
        //merge records
        Route::post('/merge_records',[ClientsController::class, 'merge_records'])->name('client.merge_records');
        
        //update email verified at client detail page
        Route::post('/clients/update-email-verified', [ClientsController::class, 'updateemailverified']);
        
		
		 //Promo code
		Route::get('/promo-code', 'Admin\PromoCodeController@index')->name('admin.feature.promocode.index');
		Route::get('/promo-code/create', 'Admin\PromoCodeController@create')->name('admin.feature.promocode.create');
		Route::post('/promo-code/store', 'Admin\PromoCodeController@store')->name('admin.feature.promocode.store');
		Route::get('/promo-code/edit/{id}', 'Admin\PromoCodeController@edit')->name('admin.feature.promocode.edit');
		Route::post('/promo-code/edit', 'Admin\PromoCodeController@edit');
        Route::post('/promo-code/checkpromocode', 'Admin\PromoCodeController@checkpromocode');
        
        Route::post('/address_auto_populate', [ClientsController::class, 'address_auto_populate']);
  
        Route::post('/client/createservicetaken', [ClientsController::class, 'createservicetaken']);
        Route::post('/client/removeservicetaken', [ClientsController::class, 'removeservicetaken']);
        Route::post('/client/getservicetaken', [ClientsController::class, 'getservicetaken']);
  
        Route::get('/gettagdata', [ClientsController::class, 'gettagdata']);
  
  		//Account Receipts section
        Route::get('/clients/saveaccountreport/{id}', [ClientsController::class, 'saveaccountreport'])->name('admin.clients.saveaccountreport');
        Route::post('/clients/saveaccountreport', [ClientsController::class, 'saveaccountreport'])->name('admin.clients.saveaccountreport.update');
        Route::post('/clients/getTopReceiptValInDB', [ClientsController::class, 'getTopReceiptValInDB'])->name('admin.clients.getTopReceiptValInDB');
        Route::get('/clients/printpreview/{id}', [ClientsController::class, 'printpreview']); //Client receipt print preview
		Route::post('/clients/getClientReceiptInfoById', [ClientsController::class, 'getClientReceiptInfoById'])->name('admin.clients.getClientReceiptInfoById');


        Route::get('/clients/clientreceiptlist', [ClientsController::class, 'clientreceiptlist'])->name('admin.clients.clientreceiptlist');
        Route::post('/validate_receipt',[ClientsController::class, 'validate_receipt'])->name('client.validate_receipt');
  
  		//Commission Report
        Route::get('/commissionreport', [ClientsController::class, 'commissionreport'])->name('admin.commissionreport');
        Route::post('/commissionreport/list',[ClientsController::class, 'getcommissionreport'])->name('admin.commissionreportlist');
  
        Route::post('/application/updateStudentId', 'Admin\ApplicationsController@updateStudentId');
  
        Route::get('/showproductfeelatest', 'Admin\ApplicationsController@showproductfeelatest');
		Route::post('/applicationsavefeelatest', 'Admin\ApplicationsController@applicationsavefeelatest');
  
        //Document Checklist Start
		Route::get('/documentchecklist', 'Admin\DocumentChecklistController@index')->name('admin.feature.documentchecklist.index');
		Route::get('/documentchecklist/create', 'Admin\DocumentChecklistController@create')->name('admin.feature.documentchecklist.create');
		Route::post('/documentchecklist/store', 'Admin\DocumentChecklistController@store')->name('admin.feature.documentchecklist.store');
		Route::get('/documentchecklist/edit/{id}', 'Admin\DocumentChecklistController@edit')->name('admin.feature.documentchecklist.edit');
		Route::post('/documentchecklist/edit', 'Admin\DocumentChecklistController@edit');
  
  		//All Document Upload
        Route::post('/add-alldocchecklist', [ClientsController::class, 'addalldocchecklist'])->name('admin.clients.addalldocchecklist');
        Route::post('/upload-alldocument', [ClientsController::class, 'uploadalldocument'])->name('admin.clients.uploadalldocument');

        //Document Not Use Tab
        Route::post('/notuseddoc', [ClientsController::class, 'notuseddoc'])->name('admin.clients.notuseddoc');
        Route::post('/renamechecklistdoc', [ClientsController::class, 'renamechecklistdoc'])->name('admin.clients.renamechecklistdoc');
        Route::post('/verifydoc', [ClientsController::class, 'verifydoc'])->name('admin.clients.verifydoc');
        Route::get('/deletealldocs', [ClientsController::class, 'deletealldocs'])->name('admin.clients.deletealldocs');
		Route::post('/renamealldoc', [ClientsController::class, 'renamealldoc'])->name('admin.clients.renamealldoc');
  
  
       //Back To Document
        Route::post('/backtodoc', [ClientsController::class, 'backtodoc'])->name('admin.clients.backtodoc');
  
        //inactive partners
        Route::get('/partners-inactive', [PartnersController::class, 'inactivePartnerList'])->name('admin.partners.inactive');
        Route::post('/partner_change_to_inactive', 'Admin\AdminController@partnerChangeToInactive');
        Route::post('/partner_change_to_active', 'Admin\AdminController@partnerChangeToActive');
  
  
       //Partner Student Invoice
        Route::get('/partners/savepartnerstudentinvoice/{id}', [PartnersController::class, 'savepartnerstudentinvoice'])->name('admin.partners.savepartnerstudentinvoice');
        Route::post('/partners/savepartnerstudentinvoice', [PartnersController::class, 'savepartnerstudentinvoice'])->name('admin.partners.savepartnerstudentinvoice.update');
        Route::post('/partners/getTopReceiptValInDB', [PartnersController::class, 'getTopReceiptValInDB'])->name('admin.partners.getTopReceiptValInDB');
        Route::post('/partners/getEnrolledStudentList', [PartnersController::class, 'getEnrolledStudentList'])->name('admin.partners.getEnrolledStudentList');


        //Partner Student Record Invoice
        Route::get('/partners/savepartnerrecordinvoice/{id}', [PartnersController::class, 'savepartnerrecordinvoice'])->name('admin.partners.savepartnerrecordinvoice');
        Route::post('/partners/savepartnerrecordinvoice', [PartnersController::class, 'savepartnerrecordinvoice'])->name('admin.partners.savepartnerrecordinvoice.update');

        //Partner Student Record payment
        Route::get('/partners/savepartnerrecordpayment/{id}', [PartnersController::class, 'savepartnerrecordpayment'])->name('admin.partners.savepartnerrecordpayment');
        Route::post('/partners/savepartnerrecordpayment', [PartnersController::class, 'savepartnerrecordpayment'])->name('admin.partners.savepartnerrecordpayment.update');
        Route::post('/partners/getRecordedInvoiceList', [PartnersController::class, 'getRecordedInvoiceList'])->name('admin.partners.getRecordedInvoiceList');
        //update student status
        Route::post('/partners/update-student-status', [PartnersController::class, 'updateStudentStatus'])->name('admin.partners.updateStudentStatus');

        //get student info
        Route::post('/partners/getStudentInfo', [PartnersController::class, 'getStudentInfo'])->name('admin.partners.getStudentInfo');
        Route::post('/partners/getStudentCourseInfo', [PartnersController::class, 'getStudentCourseInfo'])->name('admin.partners.getStudentCourseInfo');

        Route::post('/partners/getTopInvoiceValInDB', [PartnersController::class, 'getTopInvoiceValInDB'])->name('admin.partners.getTopInvoiceValInDB');
        Route::get('/partners/printpreviewcreateinvoice/{id}', [PartnersController::class, 'printpreviewcreateinvoice']); //Create Student Invoice print preview

        Route::post('/partners/updateInvoiceSentOptionToYes', [PartnersController::class, 'updateInvoiceSentOptionToYes'])->name('admin.partners.updateInvoiceSentOptionToYes');
        Route::post('/partners/getInfoByInvoiceId', [PartnersController::class, 'getInfoByInvoiceId'])->name('admin.partners.getInfoByInvoiceId');

  Route::post('/partners/getEnrolledStudentListInEditMode', [PartnersController::class, 'getEnrolledStudentListInEditMode'])->name('admin.partners.getEnrolledStudentListInEditMode');
  
  Route::post('/partners/deleteStudentRecordByInvoiceId', [PartnersController::class, 'deleteStudentRecordByInvoiceId'])->name('admin.partners.deleteStudentRecordByInvoiceId');
        Route::post('/partners/deleteStudentRecordInvoiceByInvoiceId', [PartnersController::class, 'deleteStudentRecordInvoiceByInvoiceId'])->name('admin.partners.deleteStudentRecordInvoiceByInvoiceId');
        Route::post('/partners/deleteStudentPaymentInvoiceByInvoiceId', [PartnersController::class, 'deleteStudentPaymentInvoiceByInvoiceId'])->name('admin.partners.deleteStudentPaymentInvoiceByInvoiceId');

  
       //partner inbox and sent email
        Route::post('/upload-partner-fetch-mail', [PartnersController::class, 'uploadpartnerfetchmail']);
        Route::post('/upload-partner-sent-fetch-mail', [PartnersController::class, 'uploadpartnersentfetchmail']);
  
        //Applications overdue
		Route::get('/applications-overdue', 'Admin\ApplicationsController@overdueApplicationList')->name('admin.applications.overdue');
  
       //Applications finalize
		Route::get('/applications-finalize', 'Admin\ApplicationsController@finalizeApplicationList')->name('admin.applications.finalize');
  
        //partner assign user
        Route::post('/partners/followup_partner/store_partner', [PartnersController::class, 'followupstore_partner']);
        //Route::get('/get-partner-activities', [PartnersController::class, 'partnerActivities')->name('admin.partners.activities');
  
        //Fetch all contact list of any client at create note popup
        Route::post('/clients/fetchClientContactNo', [ClientsController::class, 'fetchClientContactNo']);
  
        //Fetch all contact list of any partner at create note popup at partner detail page
        Route::post('/partners/fetchPartnerContactNo', [PartnersController::class, 'fetchPartnerContactNo']);
  
        //update student application overall status
        Route::post('/partners/update-student-application-overall-status', [PartnersController::class, 'updateStudentApplicationOverallStatus'])->name('admin.partners.updateStudentApplicationOverallStatus');
  
  
        //Add Note To Student
        Route::post('/add-student-note', [PartnersController::class, 'addstudentnote'])->name('admin.partners.addstudentnote');
        //Fetch all partner activity logs
        Route::get('/get-partner-activities', [PartnersController::class, 'activities'])->name('admin.partners.activities');

  
  
        //Update student application commission percentage
        Route::get('/partners/updatecommissionpercentage/{partner_id}', [PartnersController::class, 'updatecommissionpercentage'])->name('admin.partners.updatecommissionpercentage');

        //Update student application commission claimed and other
        Route::get('/partners/updatecommissionclaimed/{partner_id}', [PartnersController::class, 'updatecommissionclaimed'])->name('admin.partners.updatecommissionclaimed');
  
        //Note deadline task complete
        Route::post('/update-note-deadline-completed', 'Admin\AdminController@updatenotedeadlinecompleted');
        //Note deadline extend
        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');
  
  
        //Application Refund
        Route::post('/refund_application', 'Admin\ApplicationsController@refund_application');

        //save student note
        Route::post('/partners/save-student-note', [PartnersController::class, 'saveStudentNote'])->name('admin.partners.saveStudentNote');
  
       //Get partner notes
        Route::get('/get-partner-notes', [PartnersController::class, 'getPartnerNotes'])->name('admin.partners.getPartnerNotes');
  
  
        //admin send msg
        Route::post('/sendmsg', [ClientsController::class, 'sendmsg'])->name('admin.clients.sendmsg');
  
  
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

  
       //Google review email sent
        Route::post('/is_greview_mail_sent', [ClientsController::class, 'isgreviewmailsent'])->name('admin.clients.isgreviewmailsent');
  
        Route::post('/mail/enhance', [ClientsController::class, 'enhanceMessage'])->name('admin.mail.enhance');
  
       //partner document upload
        Route::post('/upload-partner-document-upload', [PartnersController::class, 'uploadpartnerdocumentupload']);
  
         //Download Document
        Route::post('/download-document', [ClientsController::class, 'download_document']);
});     

	//Email verfiy link in send email (KEEP - Client Self-Update Feature)
    Route::post('email-verify', 'HomeController@emailVerify')->name('emailVerify');
    Route::get('email-verify-token/{token}', 'HomeController@emailVerifyToken')->name('emailVerifyToken');

    //Client edit form link in send email (KEEP - Client Self-Update Feature)
    Route::get('/verify-dob/{encoded_id}', 'HomeController@showDobForm');
    Route::post('/verify-dob', 'HomeController@verifyDob');
    //Route::get('/editclient/{id}', 'HomeController@editclient')->name('editclient');
    Route::get('/editclient/{encoded_id}', 'HomeController@editClient')->middleware('checkDobSession');
	Route::post('/editclient', 'HomeController@editclient')->name('editclient');


	//Route::get('/pr-points', 'PRPointsController@index')->name('pr-points.index');
    //Route::post('/pr-points/calculate', 'PRPointsController@calculate')->name('pr-points.calculate');

// Frontend Website - Dynamic Pages Route (Commented out)
// Route::get('/{slug}', 'HomeController@Page')->name('page.slug');
// Auth::routes(); // Removed - already defined above

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');