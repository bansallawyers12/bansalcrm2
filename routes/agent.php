<?php Route::prefix('agent')->group(function() {
        Route::get('/', 'Auth\AgentLoginController@showLoginForm')->name('agent.login');
        Route::get('/login', 'Auth\AgentLoginController@showLoginForm')->name('agent.login');
        
        Route::post('/login', 'Auth\AgentLoginController@login')->name('agent.login');
        
        Route::post('/logout', 'Auth\AgentLoginController@logout')->name('agent.logout');
        
        Route::get('/dashboard', 'Agent\DashboardController@dashboard')->name('agent.dashboard');
        
        
        Route::get('/clients', 'Agent\ClientsController@index')->name('agent.clients.index');
        Route::get('/clients/create', 'Agent\ClientsController@create')->name('agent.clients.create');
        Route::get('/clients/create', 'Agent\ClientsController@create')->name('agent.clients.create'); 
        Route::post('/clients/store', 'Agent\ClientsController@store')->name('agent.clients.store');
        Route::get('/clients/edit/{id}', 'Agent\ClientsController@edit')->name('agent.clients.edit');
        Route::post('/clients/edit', 'Agent\ClientsController@edit')->name('agent.clients.edit');
        
        Route::get('/clients/detail/{id}', 'Agent\ClientsController@detail')->name('agent.clients.detail');	
        Route::get('/clients/get-recipients', 'Agent\ClientsController@getrecipients')->name('agent.clients.getrecipients');
        Route::get('/clients/get-allclients', 'Agent\ClientsController@getallclients')->name('agent.clients.getallclients');
        Route::get('/get-templates', 'Agent\AdminController@gettemplates')->name('agent.clients.gettemplates');
        Route::post('/sendmail', 'Agent\AdminController@sendmail')->name('agent.clients.sendmail');
        Route::post('/create-note', 'Agent\ClientsController@createnote')->name('agent.clients.createnote');
        Route::get('/getnotedetail', 'Agent\ClientsController@getnotedetail')->name('agent.clients.getnotedetail');
        Route::get('/deletenote', 'Agent\ClientsController@deletenote')->name('agent.clients.deletenote');
        //prospects Start  
        Route::get('/prospects', 'Agent\ClientsController@prospects')->name('agent.clients.prospects');
        Route::get('/viewnotedetail', 'Agent\ClientsController@viewnotedetail');
        Route::get('/viewapplicationnote', 'Agent\ClientsController@viewapplicationnote');
        
        //archived Start  
        Route::get('/archived', 'Agent\ClientsController@archived')->name('agent.clients.archived');
        Route::get('/change-client-status', 'Agent\ClientsController@updateclientstatus')->name('agent.clients.updateclientstatus');
        Route::get('/get-activities', 'Agent\ClientsController@activities')->name('agent.clients.activities');
        Route::get('/get-application-lists', 'Agent\ClientsController@getapplicationlists')->name('agent.clients.getapplicationlists');
        Route::post('/saveapplication', 'Agent\ClientsController@saveapplication')->name('agent.clients.saveapplication');
        Route::get('/get-notes', 'Agent\ClientsController@getnotes')->name('agent.clients.getnotes');
        Route::get('/convertapplication', 'Agent\ClientsController@convertapplication')->name('agent.clients.convertapplication');
        Route::get('/deleteservices', 'Agent\ClientsController@deleteservices')->name('agent.clients.deleteservices');
        Route::post('/upload-document', 'Agent\ClientsController@uploaddocument')->name('agent.clients.uploaddocument');
        Route::get('/deletedocs', 'Agent\ClientsController@deletedocs')->name('agent.clients.deletedocs');
        Route::post('/renamedoc', 'Agent\ClientsController@renamedoc')->name('agent.clients.renamedoc');
        
        Route::post('/savetoapplication', 'Agent\ClientsController@savetoapplication');
        
        Route::post('/interested-service', 'Agent\ClientsController@interestedService'); 	 
        Route::post('/edit-interested-service', 'Agent\ClientsController@editinterestedService'); 
        
        Route::get('/showproductfeeserv', 'Agent\ClientsController@showproductfeeserv');
        Route::post('/servicesavefee', 'Agent\ClientsController@servicesavefee');
        
        Route::get('/pinnote', 'Agent\ClientsController@pinnote'); 
        Route::get('/getpartner', 'Agent\DashboardController@getpartner');
        Route::get('getproduct', 'Agent\DashboardController@getproduct')->name('agent.quotations.getproduct');
        Route::get('getbranch', 'Agent\DashboardController@getbranch')->name('agent.quotations.getbranch');
        Route::get('/get-services', 'Agent\ClientsController@getServices'); 
        Route::get('/getintrestedservice', 'Agent\ClientsController@getintrestedservice');
        Route::get('/getintrestedserviceedit', 'Agent\ClientsController@getintrestedserviceedit'); 
        
        
        Route::get('/getapplicationdetail', 'Agent\ApplicationsController@getapplicationdetail');
        Route::get('/application/export/pdf/{id}', 'Agent\ApplicationsController@exportapplicationpdf'); 
        Route::get('/getapplicationnotes', 'Agent\ApplicationsController@getapplicationnotes');
});

?>