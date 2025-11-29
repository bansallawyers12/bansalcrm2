<?php

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
Route::get('/exception', 'ExceptionController@index')->name('exception');
Route::post('/exception', 'ExceptionController@index')->name('exception');
// Route::get('/import', 'TestController@import');
/*********************Front Panel Start ***********************/
//Coming Soon
// Route::get('/coming_soon', 'HomeController@coming_soon')->name('coming_soon');	

//Home Page
//Route::get('/', 'HomeController@index')->name('home');
//Route::get('/index', 'HomeController@index')->name('home');
// Route::get('page/{slug}', 'HomeController@Page')->name('page.slug'); 

// Route::get('/enquiry', 'HomeController@enquiry')->name('enquiry');
// Route::post('/enquiry/store', 'HomeController@store')->name('enquiry.store');

// Route::get('sicaptcha', 'HomeController@sicaptcha')->name('sicaptcha'); 
// Route::get('invoice/secure/{slug}', 'InvoiceController@invoice')->name('invoice');   
// Route::get('/invoice/download/{id}', 'InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download'); 
// Route::get('/invoice/print/{id}', 'InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print');  
// Route::get('/profile', 'HomeController@myprofile')->name('profile');   
//Login and Register
Auth::routes();
// Route::get('auth/{provider}', 'Auth\AuthController@redirectToProvider');
// Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

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

//Payment Process		
//Route::get('/payment', 'PaymentController@index')->name('payment.index');
//Route::post('/checkout', 'PaymentController@checkout')->name('payment.checkout');
//Route::get('/payment_status', 'PaymentController@status')->name('payment.status');
	
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

// Frontend Route
//Home Page
Route::get('/', 'HomeController@index')->name('home');
Route::get('/index', 'HomeController@index')->name('home');
//Route::get('/about-us', 'HomeController@about')->name('about');
Route::get('/testimonials', 'HomeController@testimonial')->name('testimonial');
Route::get('/ourservices', 'HomeController@ourservices')->name('ourservices');
Route::get('/ourservices/{slug}', 'HomeController@servicesdetail')->name('servicesdetail');  
Route::get('/blogs', 'HomeController@blogs')->name('blogs');  
Route::get('/search_result', 'HomeController@search_result')->name('search_result');

//Route::get('/blogs/{slug}', 'HomeController@blogdetail')->name('blogdetail');  
Route::get('/contact-us', 'HomeController@contactus');

Route::get('stripe/{appointmentId}', 'HomeController@stripe');
Route::post('stripe', 'HomeController@stripePost')->name('stripe.post1');

Route::get('/book-an-appointment', 'HomeController@bookappointment')->name('bookappointment');
Route::get('/book-an-appointment1', 'HomeController@bookappointment1')->name('bookappointment1');
Route::post('/book-an-appointment/store', 'AppointmentBookController@store');
Route::post('/book-an-appointment/storepaid', 'AppointmentBookController@storepaid')->name('stripe.post');
Route::post('/getdatetime', 'HomeController@getdatetime');
Route::post('/getdatetimebackend', 'HomeController@getdatetimebackend');

Route::post('/contact', 'HomeController@contact');
Route::post('/getdisableddatetime', 'HomeController@getdisableddatetime');
Route::get('/refresh-captcha', 'HomeController@refresh_captcha'); 
//Route::get('/mission-vision', 'HomeController@missionvision')->name('mission_vision'); 
Route::get('page/{slug}', 'HomeController@Page')->name('page.slug'); 
Route::get('sicaptcha', 'HomeController@sicaptcha')->name('sicaptcha'); 
Route::get('invoice/secure/{slug}', 'InvoiceController@invoice')->name('invoice');   
Route::get('/invoice/download/{id}', 'InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download'); 
Route::get('/invoice/print/{id}', 'InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print');     
Route::get('/profile', 'HomeController@myprofile')->name('profile');  
/*---------------Agent Route-------------------*/
include_once 'agent.php';
/*********************Admin Panel Start ***********************/
Route::prefix('admin')->group(function() {
	
    //Login and Logout 
		Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
		Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
		Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login');
		Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
	
	//General
        Route::get('/dashboard', 'Admin\AdminController@dashboard')->name('admin.dashboard');
		Route::get('/get_customer_detail', 'Admin\AdminController@CustomerDetail')->name('admin.get_customer_detail');
		Route::get('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile');
		Route::post('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile');
		Route::get('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
		Route::post('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
		Route::get('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions');
		Route::post('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions'); 
		Route::post('/update_action', 'Admin\AdminController@updateAction');
		Route::post('/approved_action', 'Admin\AdminController@approveAction');
		Route::post('/process_action', 'Admin\AdminController@processAction');
		Route::post('/archive_action', 'Admin\AdminController@archiveAction');
		Route::post('/declined_action', 'Admin\AdminController@declinedAction');
		Route::post('/delete_action', 'Admin\AdminController@deleteAction');
         Route::post('/delete_slot_action', 'Admin\AdminController@deleteSlotAction');
		Route::post('/move_action', 'Admin\AdminController@moveAction');
		
		/*Route::get('/appointments-education', 'Admin\AdminController@appointmentsEducation');
		Route::get('/appointments-jrp', 'Admin\AdminController@appointmentsJrp');
		Route::get('/appointments-tourist', 'Admin\AdminController@appointmentsTourist');
		Route::get('/appointments-others', 'Admin\AdminController@appointmentsOthers');*/
		
		Route::get('/appointments-education', 'Admin\AdminController@appointmentsEducation')->name('appointments-education');
		Route::get('/appointments-jrp', 'Admin\AdminController@appointmentsJrp')->name('appointments-jrp');
		Route::get('/appointments-tourist', 'Admin\AdminController@appointmentsTourist')->name('appointments-tourist');
		Route::get('/appointments-others', 'Admin\AdminController@appointmentsOthers')->name('appointments-others');
		
		Route::post('/add_ckeditior_image', 'Admin\AdminController@addCkeditiorImage')->name('add_ckeditior_image');
		Route::post('/get_chapters', 'Admin\AdminController@getChapters')->name('admin.get_chapters');
		Route::get('/website_setting', 'Admin\AdminController@websiteSetting')->name('admin.website_setting');
		Route::post('/website_setting', 'Admin\AdminController@websiteSetting')->name('admin.website_setting');
		Route::post('/get_states', 'Admin\AdminController@getStates');
		Route::get('/settings/taxes/returnsetting', 'Admin\AdminController@returnsetting')->name('admin.returnsetting');
		Route::get('/settings/taxes/taxrates', 'Admin\AdminController@taxrates')->name('admin.taxrates');
		Route::get('/settings/taxes/taxrates/create', 'Admin\AdminController@taxratescreate')->name('admin.taxrates.create');
		Route::post('/settings/taxes/taxrates/store', 'Admin\AdminController@savetaxrate')->name('admin.taxrates.store');
		Route::get('/settings/taxes/taxrates/edit/{id}', 'Admin\AdminController@edittaxrates')->name('admin.edittaxrates');
		Route::post('/settings/taxes/taxrates/edit', 'Admin\AdminController@edittaxrates')->name('admin.edittaxrates');
		Route::post('/settings/taxes/savereturnsetting', 'Admin\AdminController@returnsetting')->name('admin.savereturnsetting');
		Route::get('/getsubcategories', 'Admin\AdminController@getsubcategories');
		Route::get('/getproductbranch', 'Admin\AdminController@getproductbranch');
		Route::get('/getservicemodal', 'Admin\ServicesController@servicemodal');
		Route::get('/getassigneeajax', 'Admin\AdminController@getassigneeajax');
		Route::get('/getpartnerajax', 'Admin\AdminController@getpartnerajax');
		Route::get('/settings/currencies', 'Admin\CurrencyController@index')->name('admin.currency.index');
		Route::get('/settings/currencies/edit/{id}', 'Admin\CurrencyController@edit')->name('admin.currency.edit');
		Route::post('/settings/currencies/edit', 'Admin\CurrencyController@edit')->name('admin.currency.edit');
		Route::get('/settings/currencies/create', 'Admin\CurrencyController@create')->name('admin.currency.create');
		Route::post('/settings/currencies/store', 'Admin\CurrencyController@store')->name('admin.currency.store');
		Route::get('/checkclientexist', 'Admin\AdminController@checkclientexist');
	/*CRM route start*/
		Route::post('/uploadfile/store', 'Admin\MediaController@store')->name('admin.media.store');
		Route::get('/uploadfile/index', 'Admin\MediaController@index')->name('admin.media.index');
		Route::get('/uploadfile/delete', 'Admin\MediaController@deleteAction')->name('admin.media.delete');
		
		Route::get('/users', 'Admin\UserController@index')->name('admin.users.index');
		Route::get('/users/create', 'Admin\UserController@create')->name('admin.users.create'); 
		Route::post('/users/store', 'Admin\UserController@store')->name('admin.users.store');
		Route::get('/users/edit/{id}', 'Admin\UserController@edit')->name('admin.users.edit');
		Route::get('/users/view/{id}', 'Admin\UserController@view')->name('admin.users.view');
		Route::post('/users/edit', 'Admin\UserController@edit')->name('admin.users.edit');
		Route::post('/users/savezone', 'Admin\UserController@savezone');
		
		Route::get('/users/active', 'Admin\UserController@active')->name('admin.users.active');
		Route::get('/users/inactive', 'Admin\UserController@inactive')->name('admin.users.inactive'); 
		Route::get('/users/invited', 'Admin\UserController@invited')->name('admin.users.invited');  
		
		Route::get('/staff', 'Admin\StaffController@index')->name('admin.staff.index');
		Route::get('/staff/create', 'Admin\StaffController@create')->name('admin.staff.create'); 
		Route::post('/staff/store', 'Admin\StaffController@store')->name('admin.staff.store');
		Route::get('/staff/edit/{id}', 'Admin\StaffController@edit')->name('admin.staff.edit');
		Route::post('/staff/edit', 'Admin\StaffController@edit')->name('admin.staff.edit');
		
		Route::get('/customer', 'Admin\CustomerController@index')->name('admin.customer.index');
		Route::get('/customer/create', 'Admin\CustomerController@create')->name('admin.customer.create'); 
		Route::post('/customer/store', 'Admin\CustomerController@store')->name('admin.customer.store');
		Route::get('/customer/edit/{id}', 'Admin\CustomerController@edit')->name('admin.customer.edit');
		Route::post('/customer/edit', 'Admin\CustomerController@edit')->name('admin.customer.edit');
		Route::post('/customer/upload', 'Admin\CustomerController@uploadcsv')->name('admin.customer.upload');
		
		Route::get('/users/clientlist', 'Admin\UserController@clientlist')->name('admin.users.clientlist'); 		
		Route::get('/users/createclient', 'Admin\UserController@createclient')->name('admin.users.createclient'); 
		Route::post('/users/storeclient', 'Admin\UserController@storeclient')->name('admin.users.storeclient'); 
		Route::get('/users/editclient/{id}', 'Admin\UserController@editclient')->name('admin.users.editclient');
		Route::post('/users/editclient', 'Admin\UserController@editclient')->name('admin.users.editclient'); 
		
		Route::post('/followup/store', 'Admin\FollowupController@store')->name('admin.followup.store'); 
		Route::get('/followup/list', 'Admin\FollowupController@index')->name('admin.followup.index'); 
		Route::post('/followup/compose', 'Admin\FollowupController@compose')->name('admin.followup.compose'); 
		 
		Route::get('/usertype', 'Admin\UsertypeController@index')->name('admin.usertype.index');
		Route::get('/usertype/create', 'Admin\UsertypeController@create')->name('admin.usertype.create');  		
		Route::post('/usertype/store', 'Admin\UsertypeController@store')->name('admin.usertype.store');
		Route::get('/usertype/edit/{id}', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');
		Route::post('/usertype/edit', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');
		
		Route::get('/userrole', 'Admin\UserroleController@index')->name('admin.userrole.index');
		Route::get('/userrole/create', 'Admin\UserroleController@create')->name('admin.userrole.create');  
		Route::post('/userrole/store', 'Admin\UserroleController@store')->name('admin.userrole.store');
		Route::get('/userrole/edit/{id}', 'Admin\UserroleController@edit')->name('admin.userrole.edit');
		Route::post('/userrole/edit', 'Admin\UserroleController@edit')->name('admin.userrole.edit');
		
	//Services Start
		Route::get('/services', 'Admin\ServicesController@index')->name('admin.services.index');
		Route::get('/services/create', 'Admin\ServicesController@create')->name('admin.services.create'); 
		Route::post('/services/store', 'Admin\ServicesController@store')->name('admin.services.store');
		Route::get('/services/edit/{id}', 'Admin\ServicesController@edit')->name('admin.services.edit');
		Route::post('/services/edit', 'Admin\ServicesController@edit')->name('admin.services.edit');
			     
	  //Manage Contacts Start   
		Route::get('/contact', 'Admin\ContactController@index')->name('admin.managecontact.index'); 
		Route::get('/contact/create', 'Admin\ContactController@create')->name('admin.managecontact.create');
		Route::post('/managecontact/store', 'Admin\ContactController@store')->name('admin.managecontact.store');
		Route::post('/contact/add', 'Admin\ContactController@add')->name('admin.managecontact.add');
		Route::get('/contact/edit/{id}', 'Admin\ContactController@edit')->name('admin.managecontact.edit');
		Route::post('/contact/edit', 'Admin\ContactController@edit')->name('admin.managecontact.edit');
		Route::post('/contact/storeaddress', 'Admin\ContactController@storeaddress')->name('admin.managecontact.edit');
		 
		//Leads Start    
		Route::get('/leads', 'Admin\LeadController@index')->name('admin.leads.index');  
		Route::get('/leads/history/{id}', 'Admin\LeadController@history')->name('admin.leads.history'); 
		Route::get('/leads/create', 'Admin\LeadController@create')->name('admin.leads.create');   
		Route::post('/leads/assign', 'Admin\LeadController@assign')->name('admin.leads.assign');    
		Route::get('/leads/edit/{id}', 'Admin\LeadController@edit')->name('admin.leads.edit');
		Route::post('/leads/edit', 'Admin\LeadController@edit')->name('admin.leads.edit');
	    Route::get('/leads/notes/delete/{id}', 'Admin\LeadController@leaddeleteNotes');
	    Route::get('/get-notedetail', 'Admin\LeadController@getnotedetail');
	    Route::post('/followup/update', 'Admin\FollowupController@followupupdate');
	   
		Route::post('/leads/store', 'Admin\LeadController@store')->name('admin.leads.store');
		Route::get('/leads/convert', 'Admin\LeadController@convertoClient'); 	
		Route::get('/leads/pin/{id}', 'Admin\LeadController@leadPin'); 	
		//Invoices Start    
		
		Route::get('/invoice/lists/{id}', 'Admin\InvoiceController@lists')->name('admin.invoice.lists');  
		Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit')->name('admin.invoice.edit');  
		Route::post('/invoice/edit', 'Admin\InvoiceController@edit')->name('admin.invoice.edit');  
		Route::get('/invoice/create', 'Admin\InvoiceController@create')->name('admin.invoice.create');   
		Route::post('/invoice/store', 'Admin\InvoiceController@store')->name('admin.invoice.store'); 
		Route::get('/invoice/detail', 'Admin\InvoiceController@detail')->name('admin.invoice.detail'); 
		Route::get('/invoice/email/{id}', 'Admin\InvoiceController@email')->name('admin.invoice.email'); 
		Route::post('/invoice/email', 'Admin\InvoiceController@email')->name('admin.invoice.email'); 
		Route::get('/invoice/editpayment', 'Admin\InvoiceController@editpayment')->name('admin.invoice.editpayment'); 
		Route::get('/invoice/invoicebyid', 'Admin\InvoiceController@invoicebyid')->name('admin.invoice.invoicebyid'); 
		Route::get('/invoice/history', 'Admin\InvoiceController@history')->name('admin.invoice.history'); 
		Route::post('/invoice/paymentsave', 'Admin\InvoiceController@paymentsave')->name('admin.invoice.paymentsave'); 
		Route::post('/invoice/editpaymentsave', 'Admin\InvoiceController@editpaymentsave')->name('admin.invoice.editpaymentsave'); 
		Route::post('/invoice/addcomment', 'Admin\InvoiceController@addcomment')->name('admin.invoice.addcomment'); 
		Route::post('/invoice/sharelink', 'Admin\InvoiceController@sharelink')->name('admin.invoice.sharelink'); 
		Route::post('/invoice/disablelink', 'Admin\InvoiceController@disablelink')->name('admin.invoice.disablelink'); 
		Route::get('/invoice/download/{id}', 'Admin\InvoiceController@customer_invoice_download')->name('admin.invoice.customer_invoice_download'); 
		Route::get('/invoice/exportall', 'Admin\InvoiceController@exportall')->name('admin.invoice.exportall'); 
		Route::get('/invoice/printall', 'Admin\InvoiceController@customer_invoice_printall')->name('admin.invoice.customer_invoice_printall'); 
		Route::get('/invoice/print/{id}', 'Admin\InvoiceController@customer_invoice_print')->name('admin.invoice.customer_invoice_print'); 
		Route::get('/invoice/reminder/{id}', 'Admin\InvoiceController@reminder')->name('admin.invoice.reminder'); 
		Route::post('/invoice/reminder', 'Admin\InvoiceController@reminder')->name('admin.invoice.reminder'); 
		Route::post('/invoice/attachfile', 'Admin\InvoiceController@attachfile')->name('admin.invoice.attachfile'); 
		Route::get('/invoice/getattachfile', 'Admin\InvoiceController@getattachfile')->name('admin.invoice.getattachfile'); 
		Route::get('/invoice/removeattachfile', 'Admin\InvoiceController@removeattachfile')->name('admin.invoice.removeattachfile'); 
		Route::get('/invoice/attachfileemail', 'Admin\InvoiceController@attachfileemail')->name('admin.invoice.attachfileemail'); 
	  //Manage Api key 
	 // Route::get('/api-key', 'Admin\ApiController@index')->name('admin.apikey.index');
	  //Manage Api key  
				      
	//CMS Pages
		Route::get('/cms_pages', 'Admin\CmsPageController@index')->name('admin.cms_pages.index');
		Route::get('/cms_pages/create', 'Admin\CmsPageController@create')->name('admin.cms_pages.create');
		Route::post('/cms_pages/store', 'Admin\CmsPageController@store')->name('admin.cms_pages.store');
		Route::get('/cms_pages/edit/{id}', 'Admin\CmsPageController@editCmsPage')->name('admin.edit_cms_page');
		Route::post('/cms_pages/edit', 'Admin\CmsPageController@editCmsPage')->name('admin.edit_cms_page');
		
	//Email Templates Pages
		Route::get('/email_templates', 'Admin\EmailTemplateController@index')->name('admin.email.index');
		Route::get('/email_templates/create', 'Admin\EmailTemplateController@create')->name('admin.email.create');
		Route::post('/email_templates/store', 'Admin\EmailTemplateController@store')->name('admin.email.store');
		Route::get('/edit_email_template/{id}', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');
		Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');	
		
	//SEO Tool
		Route::get('/edit_seo/{id}', 'Admin\AdminController@editSeo')->name('admin.edit_seo');
		Route::post('/edit_seo', 'Admin\AdminController@editSeo')->name('admin.edit_seo');
		
		Route::get('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');
		Route::post('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');	
		
		Route::get('/offer/index', 'Admin\OfferController@index')->name('admin.offer.index'); 
		Route::get('/offer/create', 'Admin\OfferController@create')->name('admin.offer.create'); 
		Route::post('/offer/store', 'Admin\OfferController@store')->name('admin.offer.store'); 
		Route::get('/offer/edit/{id}', 'Admin\OfferController@edit')->name('admin.offer.edit'); 
		Route::post('/offer/edit', 'Admin\OfferController@edit')->name('admin.offer.edit'); 
		
		Route::get('/photo-gallery/getlist', 'Admin\MediaController@getlist')->name('admin.photo.getlist');
		Route::post('/photo-gallery/uploadlist', 'Admin\MediaController@uploadlist')->name('admin.photo.uploadlist');
		Route::post('/photo-gallery/update_action', 'Admin\MediaController@update_action')->name('admin.photo.update_action');
		
		//clients Start  
		Route::get('/clients', 'Admin\ClientsController@index')->name('admin.clients.index');
		Route::get('/clients/create', 'Admin\ClientsController@create')->name('admin.clients.create'); 
		Route::post('/clients/store', 'Admin\ClientsController@store')->name('admin.clients.store');
		Route::get('/clients/edit/{id}', 'Admin\ClientsController@edit')->name('admin.clients.edit');
		Route::post('/clients/edit', 'Admin\ClientsController@edit')->name('admin.clients.edit');
		Route::post('/clients/followup/store', 'Admin\ClientsController@followupstore');
		Route::post('/clients/followup/retagfollowup', 'Admin\ClientsController@retagfollowup');
		Route::get('/clients/changetype/{id}/{type}', 'Admin\ClientsController@changetype');
		Route::get('/document/download/pdf/{id}', 'Admin\ClientsController@downloadpdf');
		Route::get('/clients/removetag', 'Admin\ClientsController@removetag');
		Route::get('/clients/detail/{id}', 'Admin\ClientsController@detail')->name('admin.clients.detail');	
		Route::get('/clients/get-recipients', 'Admin\ClientsController@getrecipients')->name('admin.clients.getrecipients');
		Route::get('/clients/get-onlyclientrecipients', 'Admin\ClientsController@getonlyclientrecipients')->name('admin.clients.getonlyclientrecipients');
		Route::get('/clients/get-allclients', 'Admin\ClientsController@getallclients')->name('admin.clients.getallclients');
		Route::get('/clients/change_assignee', 'Admin\ClientsController@change_assignee');
		Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('admin.clients.gettemplates');
		Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('admin.clients.sendmail');
		Route::post('/create-note', 'Admin\ClientsController@createnote')->name('admin.clients.createnote');
		Route::get('/getnotedetail', 'Admin\ClientsController@getnotedetail')->name('admin.clients.getnotedetail');
		Route::get('/deletenote', 'Admin\ClientsController@deletenote')->name('admin.clients.deletenote');
  
        Route::get('/deleteactivitylog', 'Admin\ClientsController@deleteactivitylog')->name('admin.clients.deleteactivitylog');

  
         Route::post('/not-picked-call', 'Admin\ClientsController@notpickedcall')->name('admin.clients.notpickedcall');
		//prospects Start  
		Route::get('/prospects', 'Admin\ClientsController@prospects')->name('admin.clients.prospects');
		Route::get('/viewnotedetail', 'Admin\ClientsController@viewnotedetail');
		Route::get('/viewapplicationnote', 'Admin\ClientsController@viewapplicationnote');
		Route::post('/saveprevvisa', 'Admin\ClientsController@saveprevvisa');	
		Route::post('/saveonlineprimaryform', 'Admin\ClientsController@saveonlineform');	
		Route::post('/saveonlinesecform', 'Admin\ClientsController@saveonlineform');	
		Route::post('/saveonlinechildform', 'Admin\ClientsController@saveonlineform');	
		//archived Start  
		Route::get('/archived', 'Admin\ClientsController@archived')->name('admin.clients.archived');
		Route::get('/change-client-status', 'Admin\ClientsController@updateclientstatus')->name('admin.clients.updateclientstatus');
		Route::get('/get-activities', 'Admin\ClientsController@activities')->name('admin.clients.activities');
		Route::get('/get-application-lists', 'Admin\ClientsController@getapplicationlists')->name('admin.clients.getapplicationlists');
		Route::post('/saveapplication', 'Admin\ClientsController@saveapplication')->name('admin.clients.saveapplication');
		Route::get('/get-notes', 'Admin\ClientsController@getnotes')->name('admin.clients.getnotes');
		Route::get('/convertapplication', 'Admin\ClientsController@convertapplication')->name('admin.clients.convertapplication');
		Route::get('/deleteservices', 'Admin\ClientsController@deleteservices')->name('admin.clients.deleteservices');
		Route::post('/upload-document', 'Admin\ClientsController@uploaddocument')->name('admin.clients.uploaddocument');
		Route::get('/deletedocs', 'Admin\ClientsController@deletedocs')->name('admin.clients.deletedocs');
		Route::post('/renamedoc', 'Admin\ClientsController@renamedoc')->name('admin.clients.renamedoc');
		
		Route::post('/savetoapplication', 'Admin\ClientsController@savetoapplication');
		
		//products Start   
		Route::get('/products', 'Admin\ProductsController@index')->name('admin.products.index');
		Route::get('/products/create', 'Admin\ProductsController@create')->name('admin.products.create'); 
		Route::post('/products/store', 'Admin\ProductsController@store')->name('admin.products.store');
		Route::get('/products/edit/{id}', 'Admin\ProductsController@edit')->name('admin.products.edit');
		Route::post('/products/edit', 'Admin\ProductsController@edit')->name('admin.products.edit');
		Route::post('/products-import', 'Admin\ProductsController@import')->name('admin.products.import');

		
		Route::get('/products/detail/{id}', 'Admin\ProductsController@detail')->name('admin.products.detail');	 
		 Route::get('/products/get-recipients', 'Admin\ProductsController@getrecipients')->name('admin.products.getrecipients');
		Route::get('/products/get-allclients', 'Admin\ProductsController@getallclients')->name('admin.products.getallclients');
		
		//Partner Start
		Route::get('/partners', 'Admin\PartnersController@index')->name('admin.partners.index');
		Route::get('/partners/create', 'Admin\PartnersController@create')->name('admin.partners.create');  
		Route::post('/partners/store', 'Admin\PartnersController@store')->name('admin.partners.store');
		Route::get('/partners/edit/{id}', 'Admin\PartnersController@edit')->name('admin.partners.edit');
		Route::post('/partners/edit', 'Admin\PartnersController@edit')->name('admin.partners.edit');
		Route::get('/getpaymenttype', 'Admin\PartnersController@getpaymenttype')->name('admin.partners.getpaymenttype');
		
		Route::get('/partners/detail/{id}', 'Admin\PartnersController@detail')->name('admin.partners.detail');	 
		 Route::get('/partners/get-recipients', 'Admin\PartnersController@getrecipients')->name('admin.partners.getrecipients');
		Route::get('/partners/get-allclients', 'Admin\PartnersController@getallclients')->name('admin.partners.getallclients');
	
		//Branch Start
		Route::get('/branch', 'Admin\BranchesController@index')->name('admin.branch.index'); 
		Route::get('/branch/create', 'Admin\BranchesController@create')->name('admin.branch.create');  
		Route::post('/branch/store', 'Admin\BranchesController@store')->name('admin.branch.store');
		Route::get('/branch/edit/{id}', 'Admin\BranchesController@edit')->name('admin.branch.edit');
		Route::get('/branch/view/{id}', 'Admin\BranchesController@view')->name('admin.branch.userview');
		Route::get('/branch/view/client/{id}', 'Admin\BranchesController@viewclient')->name('admin.branch.clientview'); 
		Route::post('/branch/edit', 'Admin\BranchesController@edit')->name('admin.branch.edit');
		 
		//Quotations Start
		Route::get('/quotations', 'Admin\QuotationsController@index')->name('admin.quotations.index'); 
		
		Route::get('/quotations/client', 'Admin\QuotationsController@client')->name('admin.quotations.client');  
		Route::get('/quotations/client/create/{id}', 'Admin\QuotationsController@create')->name('admin.quotations.create');  
		Route::post('/quotations/store', 'Admin\QuotationsController@store')->name('admin.quotations.store');
		Route::get('/quotations/edit/{id}', 'Admin\QuotationsController@edit')->name('admin.quotations.edit');
		Route::post('/quotations/edit', 'Admin\QuotationsController@edit')->name('admin.quotations.edit');
		 
		Route::get('/quotations/template', 'Admin\QuotationsController@template')->name('admin.quotations.template.index');   
		Route::get('/quotations/template/create', 'Admin\QuotationsController@template_create')->name('admin.quotations.template.create');  
		Route::post('/quotations/template/store', 'Admin\QuotationsController@template_store')->name('admin.quotations.template.store');  
		Route::get('/quotations/template/edit/{id}', 'Admin\QuotationsController@template_edit')->name('admin.quotations.template.edit');  
		Route::post('/quotations/template/edit', 'Admin\QuotationsController@template_edit')->name('admin.quotations.template.edit');  
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
		Route::post('/interested-service', 'Admin\ClientsController@interestedService'); 	 
		Route::post('/edit-interested-service', 'Admin\ClientsController@editinterestedService'); 	 
		Route::get('/get-services', 'Admin\ClientsController@getServices'); 	 
		Route::get('/showproductfeeserv', 'Admin\ClientsController@showproductfeeserv');Route::post('/servicesavefee', 'Admin\ClientsController@servicesavefee');		 	 
		Route::get('/deleteappointment', 'Admin\ClientsController@deleteappointment'); 
		Route::post('/add-appointment', 'Admin\ClientsController@addAppointment'); 	 
		
		Route::post('/add-appointment-book', 'Admin\ClientsController@addAppointmentBook');
		
		Route::post('/editappointment', 'Admin\ClientsController@editappointment'); 	 
		Route::post('/upload-mail', 'Admin\ClientsController@uploadmail'); 	 
		Route::post('/updatefollowupschedule', 'Admin\ClientsController@updatefollowupschedule'); 	 
		Route::get('/updateappointmentstatus/{status}/{id}', 'Admin\ClientsController@updateappointmentstatus'); 	 
		Route::get('/get-appointments', 'Admin\ClientsController@getAppointments'); 
  
        Route::get('/pinnote', 'Admin\ClientsController@pinnote'); 	 
  	    Route::get('/pinactivitylog', 'Admin\ClientsController@pinactivitylog');
  
		Route::get('/getintrestedservice', 'Admin\ClientsController@getintrestedservice'); 	 
		Route::post('/application/saleforcastservice', 'Admin\ClientsController@saleforcastservice');
		Route::get('/getintrestedserviceedit', 'Admin\ClientsController@getintrestedserviceedit'); 	 
		Route::get('/getAppointmentdetail', 'Admin\ClientsController@getAppointmentdetail');
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
		Route::post('/product-type/edit', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		
		Route::get('/profiles', 'Admin\ProfileController@index')->name('admin.feature.profiles.index');  
		Route::get('/profiles/create', 'Admin\ProfileController@create')->name('admin.feature.profiles.create');  
		Route::post('/profiles/store', 'Admin\ProfileController@store')->name('admin.feature.profiles.store');  
		Route::get('/profiles/edit/{id}', 'Admin\ProfileController@edit')->name('admin.feature.profiles.edit');
		Route::post('/profiles/edit', 'Admin\ProfileController@edit')->name('admin.feature.profiles.edit');
		//Partner Type Start    
		Route::get('/partner-type', 'Admin\PartnerTypeController@index')->name('admin.feature.partnertype.index');  
		Route::get('/partner-type/create', 'Admin\PartnerTypeController@create')->name('admin.feature.partnertype.create');  
		Route::post('/partner-type/store', 'Admin\PartnerTypeController@store')->name('admin.feature.partnertype.store');   
		Route::get('/partner-type/edit/{id}', 'Admin\PartnerTypeController@edit')->name('admin.feature.partnertype.edit');
		Route::post('/partner-type/edit', 'Admin\PartnerTypeController@edit')->name('admin.feature.partnertype.edit');
		   
		//Visa Type Start    
		Route::get('/visa-type', 'Admin\VisaTypeController@index')->name('admin.feature.visatype.index');  
		Route::get('/visa-type/create', 'Admin\VisaTypeController@create')->name('admin.feature.visatype.create');  
		Route::post('/visa-type/store', 'Admin\VisaTypeController@store')->name('admin.feature.visatype.store');     
		Route::get('/visa-type/edit/{id}', 'Admin\VisaTypeController@edit')->name('admin.feature.visatype.edit');
		Route::post('/visa-type/edit', 'Admin\VisaTypeController@edit')->name('admin.feature.visatype.edit');
		
		//Master Category Start    
		Route::get('/master-category', 'Admin\MasterCategoryController@index')->name('admin.feature.mastercategory.index');  
		Route::get('/master-category/create', 'Admin\MasterCategoryController@create')->name('admin.feature.mastercategory.create');  
		Route::post('/master-category/store', 'Admin\MasterCategoryController@store')->name('admin.feature.mastercategory.store');     
		Route::get('/master-category/edit/{id}', 'Admin\MasterCategoryController@edit')->name('admin.feature.mastercategory.edit');
		Route::post('/master-category/edit', 'Admin\MasterCategoryController@edit')->name('admin.feature.mastercategory.edit');
		
		//Lead Service Start    
		Route::get('/lead-service', 'Admin\LeadServiceController@index')->name('admin.feature.leadservice.index');  
		Route::get('/lead-service/create', 'Admin\LeadServiceController@create')->name('admin.feature.leadservice.create');  
		Route::post('/lead-service/store', 'Admin\LeadServiceController@store')->name('admin.feature.leadservice.store');     
		Route::get('/lead-service/edit/{id}', 'Admin\LeadServiceController@edit')->name('admin.feature.leadservice.edit');
		Route::post('/lead-service/edit', 'Admin\LeadServiceController@edit')->name('admin.feature.leadservice.edit');
		
		//Tax Start  
		Route::get('/tax', 'Admin\TaxController@index')->name('admin.feature.tax.index');  
		Route::get('/tax/create', 'Admin\TaxController@create')->name('admin.feature.tax.create');  
		Route::post('/tax/store', 'Admin\TaxController@store')->name('admin.feature.tax.store');  
		Route::get('/tax/edit/{id}', 'Admin\TaxController@edit')->name('admin.feature.tax.edit');
		Route::post('/tax/edit', 'Admin\TaxController@edit')->name('admin.feature.tax.edit');
		
		//Subject Area Start  	
		Route::get('/subjectarea', 'Admin\SubjectAreaController@index')->name('admin.feature.subjectarea.index');  
		Route::get('/subjectarea/create', 'Admin\SubjectAreaController@create')->name('admin.feature.subjectarea.create');  
		Route::post('/subjectarea/store', 'Admin\SubjectAreaController@store')->name('admin.feature.subjectarea.store');  
		Route::get('/subjectarea/edit/{id}', 'Admin\SubjectAreaController@edit')->name('admin.feature.subjectarea.edit');
		Route::post('/subjectarea/edit', 'Admin\SubjectAreaController@edit')->name('admin.feature.subjectarea.edit');
		
		//Subject Start  
		Route::get('/subject', 'Admin\SubjectController@index')->name('admin.feature.subject.index');
		Route::get('/subject/create', 'Admin\SubjectController@create')->name('admin.feature.subject.create');  
		Route::post('/subject/store', 'Admin\SubjectController@store')->name('admin.feature.subject.store');  
		Route::get('/subject/edit/{id}', 'Admin\SubjectController@edit')->name('admin.feature.subject.edit');
		Route::post('/subject/edit', 'Admin\SubjectController@edit')->name('admin.feature.subject.edit');
		
		//Source Start
		Route::get('/source', 'Admin\SourceController@index')->name('admin.feature.source.index');  
		Route::get('/source/create', 'Admin\SourceController@create')->name('admin.feature.source.create');  
		Route::post('source/store', 'Admin\SourceController@store')->name('admin.feature.source.store');     
		Route::get('/source/edit/{id}', 'Admin\SourceController@edit')->name('admin.feature.source.edit');
		Route::post('/source/edit', 'Admin\SourceController@edit')->name('admin.feature.source.edit');
		
		//Tags Start
		Route::get('/tags', 'Admin\TagController@index')->name('admin.feature.tags.index');  
		Route::get('/tags/create', 'Admin\TagController@create')->name('admin.feature.tags.create');  
		Route::post('tags/store', 'Admin\TagController@store')->name('admin.feature.tags.store');     
		Route::get('/tags/edit/{id}', 'Admin\TagController@edit')->name('admin.feature.tags.edit');
		Route::post('/tags/edit', 'Admin\TagController@edit')->name('admin.feature.tags.edit');
		
		//Checklist Start
		Route::get('/checklist', 'Admin\ChecklistController@index')->name('admin.checklist.index');  
		Route::get('/checklist/create', 'Admin\ChecklistController@create')->name('admin.checklist.create');  
		Route::post('checklist/store', 'Admin\ChecklistController@store')->name('admin.checklist.store');     
		Route::get('/checklist/edit/{id}', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');
		Route::post('/checklist/edit', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');
		
		//Enquiry Source Start
		Route::get('/enquirysource', 'Admin\EnquirySourceController@index')->name('admin.enquirysource.index');  
		Route::get('/enquirysource/create', 'Admin\EnquirySourceController@create')->name('admin.enquirysource.create');  
		Route::post('enquirysource/store', 'Admin\EnquirySourceController@store')->name('admin.enquirysource.store');     
		Route::get('/enquirysource/edit/{id}', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.edit');
		Route::post('/enquirysource/edit', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.edit');
		
		//FeeType Start
		Route::get('/feetype', 'Admin\FeeTypeController@index')->name('admin.feetype.index');  
		Route::get('/feetype/create', 'Admin\FeeTypeController@create')->name('admin.feetype.create');  
		Route::post('feetype/store', 'Admin\FeeTypeController@store')->name('admin.feetype.store');     
		Route::get('/feetype/edit/{id}', 'Admin\FeeTypeController@edit')->name('admin.enquirysource.edit');
		Route::post('/feetype/edit', 'Admin\FeeTypeController@edit')->name('admin.feetype.edit');
		
		
		//workflow Start
		Route::get('/workflow', 'Admin\WorkflowController@index')->name('admin.workflow.index');  
		Route::get('/workflow/create', 'Admin\WorkflowController@create')->name('admin.workflow.create');  
		Route::post('workflow/store', 'Admin\WorkflowController@store')->name('admin.workflow.store');     
		Route::get('/workflow/edit/{id}', 'Admin\WorkflowController@edit')->name('admin.workflow.edit');
		Route::get('/workflow/deactivate-workflow/{id}', 'Admin\WorkflowController@deactivateWorkflow')->name('admin.workflow.deactivate');
		Route::get('/workflow/activate-workflow/{id}', 'Admin\WorkflowController@activateWorkflow')->name('admin.workflow.activate');
		Route::post('/workflow/edit', 'Admin\WorkflowController@edit')->name('admin.workflow.edit');
		
		Route::post('/partner/saveagreement', 'Admin\PartnersController@saveagreement');
		Route::post('/partner/create-contact', 'Admin\PartnersController@createcontact');
		Route::get('/get-contacts', 'Admin\PartnersController@getcontacts');
		Route::get('/deletecontact', 'Admin\PartnersController@deletecontact');
		Route::get('/getcontactdetail', 'Admin\PartnersController@getcontactdetail');
		Route::post('/partners-import', 'Admin\PartnersController@import')->name('admin.partners.import');
		
		Route::post('/partner/create-branch', 'Admin\PartnersController@createbranch');
		Route::get('/get-branches', 'Admin\PartnersController@getbranch');
		Route::get('/getbranchdetail', 'Admin\PartnersController@getbranchdetail');
		Route::get('/deletebranch', 'Admin\PartnersController@deletebranch');
		
		Route::post('/saveacademic', 'Admin\ProductsController@saveacademic');
		Route::post('/saveotherinfo', 'Admin\ProductsController@saveotherinfo');
		Route::get('/product/getotherinfo', 'Admin\ProductsController@getotherinfo');
		Route::get('/get-all-fees', 'Admin\ProductsController@getallfees');
		Route::post('/savefee', 'Admin\ProductsController@savefee');
		
		Route::get('/getfeeoptionedit', 'Admin\ProductsController@editfee');
		Route::post('/editfee', 'Admin\ProductsController@editfeeform');
		Route::get('/deletefee', 'Admin\ProductsController@deletefee');
		
		Route::post('/add-partner-appointment', 'Admin\PartnersController@addappointment');
		Route::get('/partner/get-appointments', 'Admin\PartnersController@getappointment');
		Route::get('/partner/getAppointmentdetail', 'Admin\PartnersController@getAppointmentdetail');
		
		Route::post('/partner/addtask', 'Admin\PartnersController@addTask');
		Route::get('/partner/get-tasks', 'Admin\PartnersController@gettasks');
		Route::get('/partner/get-task-detail', 'Admin\PartnersController@taskdetail');
		Route::post('/partner/savecomment', 'Admin\PartnersController@savecomment');
		Route::get('/change-task-status', 'Admin\PartnersController@changetaskstatus');
		Route::get('/change-task-priority', 'Admin\PartnersController@changetaskpriority');
		
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
		Route::get('/migration', 'Admin\ApplicationsController@migrationindex')->name('admin.migration.index');
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


		Route::post('/save_tag', 'Admin\ClientsController@save_tag'); 	 
		
		
		//Email Start
		Route::get('/emails', 'Admin\EmailController@index')->name('admin.emails.index');  
		Route::get('/emails/create', 'Admin\EmailController@create')->name('admin.emails.create');  
		Route::post('emails/store', 'Admin\EmailController@store')->name('admin.emails.store');     
		Route::get('/emails/edit/{id}', 'Admin\EmailController@edit')->name('admin.emails.edit');
		Route::post('/emails/edit', 'Admin\EmailController@edit')->name('admin.emails.edit');
		
		//Crm Email Template Start
		Route::get('/crm_email_template', 'Admin\CrmEmailTemplateController@index')->name('admin.crmemailtemplate.index');  
		Route::get('/crm_email_template/create', 'Admin\CrmEmailTemplateController@create')->name('admin.crmemailtemplate.create');  
		Route::post('crm_email_template/store', 'Admin\CrmEmailTemplateController@store')->name('admin.crmemailtemplate.store');     
		Route::get('/crm_email_template/edit/{id}', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.edit');
		Route::post('/crm_email_template/edit', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.edit'); 
		
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
		Route::get('/all-notifications', 'Admin\AdminController@allnotification');	
		
		// Appointment modulle
		Route::resource('appointments', Admin\AppointmentsController::class);
		Route::get('/get-assigne-detail', 'Admin\AppointmentsController@assignedetail');
		Route::post('/update_appointment_status', 'Admin\AppointmentsController@update_appointment_status');
		Route::post('/update_appointment_priority', 'Admin\AppointmentsController@update_appointment_priority');
		Route::get('/change_assignee', 'Admin\AppointmentsController@change_assignee');  
		Route::post('/update_apppointment_comment', 'Admin\AppointmentsController@update_apppointment_comment');
		Route::post('/update_apppointment_description', 'Admin\AppointmentsController@update_apppointment_description');
		


		//Blog 
		Route::get('/blog', 'Admin\BlogController@index')->name('admin.blog.index');
		Route::get('/blog/create', 'Admin\BlogController@create')->name('admin.blog.create');
		Route::post('/blog/store', 'Admin\BlogController@store')->name('admin.blog.store');
		Route::get('/blog/edit/{id}', 'Admin\BlogController@edit')->name('admin.blog.edit');
		Route::post('/blog/edit', 'Admin\BlogController@edit')->name('admin.blog.edit');

	//Blog Category
		Route::get('/blogcategories', 'Admin\BlogCategoryController@index')->name('admin.blogcategory.index');
		Route::get('/blogcategories/create', 'Admin\BlogCategoryController@create')->name('admin.blogcategory.create');
		Route::post('/blogcategories/store', 'Admin\BlogCategoryController@store')->name('admin.blogcategory.store');
		Route::get('/blogcategories/edit/{id}', 'Admin\BlogCategoryController@edit')->name('admin.blogcategory.edit');
		Route::post('/blogcategories/edit', 'Admin\BlogCategoryController@edit')->name('admin.blogcategory.edit');

		//CMS Pages
		Route::get('/cms_pages', 'Admin\CmsPageController@index')->name('admin.cms_pages.index');
		Route::get('/cms_pages/create', 'Admin\CmsPageController@create')->name('admin.cms_pages.create');
		Route::post('/cms_pages/store', 'Admin\CmsPageController@store')->name('admin.cms_pages.store');
		Route::get('/cms_pages/edit/{id}', 'Admin\CmsPageController@editCmsPage')->name('admin.edit_cms_page');
		Route::post('/cms_pages/edit', 'Admin\CmsPageController@editCmsPage')->name('admin.edit_cms_page');
		
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
        Route::post('/clients/personalfollowup/store', 'Admin\ClientsController@personalfollowup');
        Route::post('/clients/updatefollowup/store', 'Admin\ClientsController@updatefollowup');
        Route::post('/clients/reassignfollowup/store', 'Admin\ClientsController@reassignfollowupstore');
        
        //update attending session to be completed
        Route::post('/clients/update-session-completed', 'Admin\ClientsController@updatesessioncompleted')->name('admin.clients.updatesessioncompleted');
        
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
        Route::post('/merge_records','Admin\ClientsController@merge_records')->name('client.merge_records');
        
        //update email verified at client detail page
        Route::post('/clients/update-email-verified', 'Admin\ClientsController@updateemailverified');
        
         //Appointment Dates Not Available
		Route::get('/appointment-dates-disable', 'Admin\AppointmentDisableDateController@index')->name('admin.feature.appointmentdisabledate.index');
		Route::get('/appointment-dates-disable/create', 'Admin\AppointmentDisableDateController@create')->name('admin.feature.appointmentdisabledate.create');
		Route::post('/appointment-dates-disable/store', 'Admin\AppointmentDisableDateController@store')->name('admin.feature.appointmentdisabledate.store');
		Route::get('/appointment-dates-disable/edit/{id}', 'Admin\AppointmentDisableDateController@edit')->name('admin.feature.appointmentdisabledate.edit');
		Route::post('/appointment-dates-disable/edit', 'Admin\AppointmentDisableDateController@edit')->name('admin.feature.appointmentdisabledate.edit');
		
		 //Promo code
		Route::get('/promo-code', 'Admin\PromoCodeController@index')->name('admin.feature.promocode.index');
		Route::get('/promo-code/create', 'Admin\PromoCodeController@create')->name('admin.feature.promocode.create');
		Route::post('/promo-code/store', 'Admin\PromoCodeController@store')->name('admin.feature.promocode.store');
		Route::get('/promo-code/edit/{id}', 'Admin\PromoCodeController@edit')->name('admin.feature.promocode.edit');
		Route::post('/promo-code/edit', 'Admin\PromoCodeController@edit')->name('admin.feature.promocode.edit');
        Route::post('/promo-code/checkpromocode', 'Admin\PromoCodeController@checkpromocode');
        
        Route::post('/address_auto_populate', 'Admin\ClientsController@address_auto_populate');
  
        Route::post('/client/createservicetaken', 'Admin\ClientsController@createservicetaken');
        Route::post('/client/removeservicetaken', 'Admin\ClientsController@removeservicetaken');
        Route::post('/client/getservicetaken', 'Admin\ClientsController@getservicetaken');
  
        Route::get('/gettagdata', 'Admin\ClientsController@gettagdata');
  
  		//Account Receipts section
        Route::get('/clients/saveaccountreport/{id}', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport');
        Route::post('/clients/saveaccountreport', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport');
        Route::post('/clients/getTopReceiptValInDB', 'Admin\ClientsController@getTopReceiptValInDB')->name('admin.clients.getTopReceiptValInDB');
        Route::get('/clients/printpreview/{id}', 'Admin\ClientsController@printpreview'); //Client receipt print preview
		Route::post('/clients/getClientReceiptInfoById', 'Admin\ClientsController@getClientReceiptInfoById')->name('admin.clients.getClientReceiptInfoById');


        Route::get('/clients/clientreceiptlist', 'Admin\ClientsController@clientreceiptlist')->name('admin.clients.clientreceiptlist');
        Route::post('/validate_receipt','Admin\ClientsController@validate_receipt')->name('client.validate_receipt');
  
  		//Commission Report
        Route::get('/commissionreport', 'Admin\ClientsController@commissionreport')->name('admin.commissionreport');
        Route::post('/commissionreport/list','Admin\ClientsController@getcommissionreport')->name('admin.commissionreportlist');
  
        Route::post('/application/updateStudentId', 'Admin\ApplicationsController@updateStudentId');
  
        Route::get('/showproductfeelatest', 'Admin\ApplicationsController@showproductfeelatest');
		Route::post('/applicationsavefeelatest', 'Admin\ApplicationsController@applicationsavefeelatest');
  
        //Document Checklist Start
		Route::get('/documentchecklist', 'Admin\DocumentChecklistController@index')->name('admin.feature.documentchecklist.index');
		Route::get('/documentchecklist/create', 'Admin\DocumentChecklistController@create')->name('admin.feature.documentchecklist.create');
		Route::post('/documentchecklist/store', 'Admin\DocumentChecklistController@store')->name('admin.feature.documentchecklist.store');
		Route::get('/documentchecklist/edit/{id}', 'Admin\DocumentChecklistController@edit')->name('admin.feature.documentchecklist.edit');
		Route::post('/documentchecklist/edit', 'Admin\DocumentChecklistController@edit')->name('admin.feature.documentchecklist.edit');
  
  		//All Document Upload
        Route::post('/add-alldocchecklist', 'Admin\ClientsController@addalldocchecklist')->name('admin.clients.addalldocchecklist');
        Route::post('/upload-alldocument', 'Admin\ClientsController@uploadalldocument')->name('admin.clients.uploadalldocument');

        //Document Not Use Tab
        Route::post('/notuseddoc', 'Admin\ClientsController@notuseddoc')->name('admin.clients.notuseddoc');
        Route::post('/renamechecklistdoc', 'Admin\ClientsController@renamechecklistdoc')->name('admin.clients.renamechecklistdoc');
        Route::post('/verifydoc', 'Admin\ClientsController@verifydoc')->name('admin.clients.verifydoc');
        Route::get('/deletealldocs', 'Admin\ClientsController@deletealldocs')->name('admin.clients.deletealldocs');
		Route::post('/renamealldoc', 'Admin\ClientsController@renamealldoc')->name('admin.clients.renamealldoc');
  
  
       //Back To Document
        Route::post('/backtodoc', 'Admin\ClientsController@backtodoc')->name('admin.clients.backtodoc');
  
        //inactive partners
        Route::get('/partners-inactive', 'Admin\PartnersController@inactivePartnerList')->name('admin.partners.inactive');
        Route::post('/partner_change_to_inactive', 'Admin\AdminController@partnerChangeToInactive');
        Route::post('/partner_change_to_active', 'Admin\AdminController@partnerChangeToActive');
  
  
         //Partner Student Invoice
        Route::get('/partners/savepartnerstudentinvoice/{id}', 'Admin\PartnersController@savepartnerstudentinvoice')->name('admin.partners.savepartnerstudentinvoice');
        Route::post('/partners/savepartnerstudentinvoice', 'Admin\PartnersController@savepartnerstudentinvoice')->name('admin.partners.savepartnerstudentinvoice');
        Route::post('/partners/getTopReceiptValInDB', 'Admin\PartnersController@getTopReceiptValInDB')->name('admin.partners.getTopReceiptValInDB');
        Route::post('/partners/getEnrolledStudentList', 'Admin\PartnersController@getEnrolledStudentList')->name('admin.partners.getEnrolledStudentList');


        //Partner Student Record Invoice
        Route::get('/partners/savepartnerrecordinvoice/{id}', 'Admin\PartnersController@savepartnerrecordinvoice')->name('admin.partners.savepartnerrecordinvoice');
        Route::post('/partners/savepartnerrecordinvoice', 'Admin\PartnersController@savepartnerrecordinvoice')->name('admin.partners.savepartnerrecordinvoice');

        //Partner Student Record payment
        Route::get('/partners/savepartnerrecordpayment/{id}', 'Admin\PartnersController@savepartnerrecordpayment')->name('admin.partners.savepartnerrecordpayment');
        Route::post('/partners/savepartnerrecordpayment', 'Admin\PartnersController@savepartnerrecordpayment')->name('admin.partners.savepartnerrecordpayment');
        Route::post('/partners/getRecordedInvoiceList', 'Admin\PartnersController@getRecordedInvoiceList')->name('admin.partners.getRecordedInvoiceList');
        //update student status
        Route::post('/partners/update-student-status', 'Admin\PartnersController@updateStudentStatus')->name('admin.partners.updateStudentStatus');

        //get student info
        Route::post('/partners/getStudentInfo', 'Admin\PartnersController@getStudentInfo')->name('admin.partners.getStudentInfo');
        Route::post('/partners/getStudentCourseInfo', 'Admin\PartnersController@getStudentCourseInfo')->name('admin.partners.getStudentCourseInfo');

        Route::post('/partners/getTopInvoiceValInDB', 'Admin\PartnersController@getTopInvoiceValInDB')->name('admin.partners.getTopInvoiceValInDB');
        Route::get('/partners/printpreviewcreateinvoice/{id}', 'Admin\PartnersController@printpreviewcreateinvoice'); //Create Student Invoice print preview



});     

Route::get('/{slug}', 'HomeController@Page')->name('page.slug');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');