<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\Admin;
use App\Models\Contact;
// NOTE: TaxRate model/table has been removed
// use App\Models\TaxRate;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\InvoicePayment;
use Auth;
use Config;
use App\Models\ActivitiesLog;
use App\Models\Note;


use App\Services\EmailService;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
  
    protected $emailService;
    protected $dashboardService;
  
    public function __construct(EmailService $emailService, DashboardService $dashboardService)
    {
        $this->middleware('auth:admin');
        $this->emailService = $emailService;
        $this->dashboardService = $dashboardService;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try {
            // Get dashboard data using service (always show today's actions)
            $todayTasks = $this->dashboardService->getTodayTasks('today');
            $checkInQueue = $this->dashboardService->getCheckInQueue();
            $clientsWithRecentActivities = $this->dashboardService->getClientsWithRecentActivities(10);
            $loginStats = $this->dashboardService->getLoginStatistics();
            $recentActivities = $this->dashboardService->getRecentActivities(10);
            
            return view('Admin.dashboard', compact([
                'todayTasks',
                'checkInQueue',
                'clientsWithRecentActivities',
                'loginStats',
                'recentActivities'
            ]));
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            \Log::error('Dashboard error trace: ' . $e->getTraceAsString());
            
            // Return view with empty data on error
            return view('Admin.dashboard', [
                'todayTasks' => collect([]),
                'checkInQueue' => ['total' => 0, 'items' => collect([])],
                'clientsWithRecentActivities' => collect([]),
                'loginStats' => $this->dashboardService->getLoginStatistics(),
                'recentActivities' => collect([])
            ])->with('error', 'An error occurred while loading the dashboard. Some data may not be available.');
        }
    }

    public function fetchnotification(Request $request){
         //$notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->orderby('created_at','DESC')->paginate(5);
         $notificalistscount = \App\Models\Notification::where('receiver_id', Auth::user()->id)->count(); //->where('receiver_status', 0)
         /*$output = '';
	    foreach($notificalists as $listnoti){
	        $output .= '<a href="'.$listnoti->url.'?t='.$listnoti->id.'" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc">'.$listnoti->message.' <span class="time">'.date('d/m/Y h:i A',strtotime($listnoti->created_at)).'</span></span>
					</a>';
	    }*/

	    $data = array(
          // 'notification' => $output,
           'unseen_notification'  => $notificalistscount
        );
        echo json_encode($data);
    }
    
    public function fetchmessages(Request $request){
        $notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('seen', 0)->first();
        if($notificalists){
            $obj = \App\Models\Notification::find($notificalists->id);
            $obj->seen = 1;
            $obj->save();
            return $notificalists->message;
        }else{
            return 0;
        }
    }
    
    public function fetchInPersonWaitingCount(Request $request){
        //if(\Auth::user()->role == 1){
            $InPersonwaitingCount = \App\Models\CheckinLog::where('status',0)->count();
        /*}else{
            $InPersonwaitingCount = \App\Models\CheckinLog::where('user_id',Auth::user()->id)->where('status',0)->count();
        }*/
        $data = array('InPersonwaitingCount'  => $InPersonwaitingCount);
        echo json_encode($data);
   }

    public function fetchTotalActivityCount(Request $request){
        if(\Auth::user()->role == 1){
            $assigneesCount = \App\Models\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->count();
        }else{
            $assigneesCount = \App\Models\Note::where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->count();
        }
        $data = array('assigneesCount'  => $assigneesCount);
        echo json_encode($data);
    }

   
	/**
     * My Profile.
     *
     * @return \Illuminate\Http\Response
     */
	public function returnsetting(Request $request){
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();
			$obj							= 	Admin::find(Auth::user()->id);
			// GST columns removed from admins table
			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('returnsetting')->with('success', 'Your Profile has been edited successfully.');
			}
		}else{
			//return view('Admin.my_profile', compact(['fetchedData', 'countries']));
			return view('Admin.settings.returnsetting');
		}
	}
	// NOTE: Tax rate methods have been removed (taxrates, taxratescreate, edittaxrates, savetaxrate)
	// These methods were related to the tax_rates table which has been dropped
	public function myProfile(Request $request)
	{
		/* Get all Select Data */
			$countries = array();
		/* Get all Select Data */

		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$this->validate($request, [
										'first_name' => 'required',
										'last_name' => 'nullable',
										'email' => 'required|email|unique:admins,email,'.Auth::user()->id,
										'country' => 'required',
										'phone' => 'required',
										'state' => 'required',
										'city' => 'required',
										'address' => 'required',
										'zip' => 'required'
									  ]);

			$obj							= 	Admin::find(Auth::user()->id);

		$obj->first_name				=	@$requestData['first_name'];
			$obj->last_name					=	@$requestData['last_name'];
			$obj->email						=	@$requestData['email'];
			$obj->phone						=	@$requestData['phone'];
			$obj->country					=	@$requestData['country'];
			$obj->state						=	@$requestData['state'];
			$obj->city						=	@$requestData['city'];
			$obj->address					=	@$requestData['address'];
			$obj->zip						=	@$requestData['zip'];

			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('my_profile')->with('success', 'Your Profile has been edited successfully.');
			}
		}
		else
		{
			$id = Auth::user()->id;
			$fetchedData = Admin::find($id);

			return view('Admin.my_profile', compact(['fetchedData', 'countries']));
		}
	}
	/**
     * Change password and Logout automatiaclly.
     *
     * @return \Illuminate\Http\Response
     */
	public function change_password(Request $request)
	{
		//check authorization start
			/* $check = $this->checkAuthorizationAction('Admin', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			} */
		//check authorization end

		if ($request->isMethod('post'))
		{
			$this->validate($request, [
										'old_password' => 'required|min:6',
										'password' => 'required|confirmed|min:6',
										'password_confirmation' => 'required|min:6'
									  ]);


			$requestData 	= 	$request->all();
			$admin_id = Auth::user()->id;

			$fetchedData = Admin::where('id', '=', $admin_id)->first();
			if(!empty($fetchedData))
				{
					if($admin_id == trim($requestData['admin_id']))
						{
							 if (!(Hash::check($request->get('old_password'), Auth::user()->password)))
								{
									return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
								}
							else
								{
									$admin = Admin::find($requestData['admin_id']);
									$admin->password = Hash::make($requestData['password']);
									if($admin->save())
										{
											Auth::guard('admin')->logout();
											$request->session()->flush();

											return redirect('/admin')->with('success', 'Your Password has been changed successfully.');
										}
									else
										{
											return redirect()->back()->with('error', Config::get('constants.server_error'));
										}
								}
						}
					else
						{
							return redirect()->back()->with('error', 'You can change the password only your account.');
						}
				}
			else
				{
					return redirect()->back()->with('error', 'User is not exist, so you can not change the password.');
				}
		}
		return view('Admin.change_password');
	}

	public function CustomerDetail(Request $request){

		$contactexist = Contact::where('id', $request->customer_id)->where('user_id', Auth::user()->id)->exists();
		if($contactexist){
			$contact = Contact::where('id', $request->customer_id)->with(['currencydata'])->first();
			return json_encode(array('success' => true, 'contactdetail' => $contact));
		}else{
			return json_encode(array('success' => false, 'message' => 'ID not exist'));
		}
	}

	public function editapi(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('api_key', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		if ($request->isMethod('post'))
		{
			$obj	= 	Admin::find(Auth::user()->id);
			$obj->client_id	=	md5(Auth::user()->id.time());
			$saved				=	$obj->save();
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('edit_api')->with('success', 'Api Key'.Config::get('constants.edited'));
			}
		}else{
			return view('Admin.apikey');
		}
	}

	public function updateAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);
			$requestData['current_status'] = trim($requestData['current_status']);
			$requestData['table'] = trim($requestData['table']);
			$requestData['col'] = trim($requestData['colname']);

			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['current_status']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{
							if($requestData['current_status'] == 0)
							{
								$updated_status = 1;
								$message = 'Record has been enabled successfully.';
							}
							else
							{
								$updated_status = 0;
								$message = 'Record has been disabled successfully.';
							}
							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update([$requestData['col'] => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		 die;

	}


	public function moveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);
			$requestData['col'] = trim($requestData['col']);

				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{
							// When un-archiving clients, also clear archive metadata for consistency
							if($requestData['table'] == 'admins' && $requestData['col'] == 'is_archived') {
								$response = DB::table($requestData['table'])->where('id', $requestData['id'])->update([
									'is_archived' => 0,
									'archived_on' => null,
									'archived_by' => null
								]);
							} else {
								// For other tables/columns, keep existing behavior
								$response = DB::table($requestData['table'])->where('id', $requestData['id'])->update([$requestData['col'] => 0]);
							}
							
							if($response)
							{
								$status = 1;
								$message = 'Record successfully moved';
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}

		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function declinedAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);


			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 2;
								$message = 'Record has been disabled successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function approveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);


			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 1;
								$message = 'Record has been approved successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error').'sss';
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function processAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);


			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 4;
								$message = 'Record has been processed successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error').'sss';
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function archiveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);

			$astatus = '';
			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

					if($recordExist)
					{
						$updated_status = 1;
						$message = 'Record has been archived successfully.';

						// Handle admins table (clients/leads) separately - use correct column names and metadata
						if($requestData['table'] == 'admins'){
							// Archive clients/leads with proper metadata (same as deleteAction)
							$updateData = [
								'is_archived' => 1,
								'archived_on' => date('Y-m-d'),
								'archived_by' => Auth::user()->id
							];
							$response = DB::table($requestData['table'])->where('id', $requestData['id'])->update($updateData);
							
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							// For other tables (quotations, etc.) - use existing logic with 'is_archive' column
							$response = DB::table($requestData['table'])->where('id', $requestData['id'])->update(['is_archive' => $updated_status]);
							$getarchive = DB::table($requestData['table'])->where('id', $requestData['id'])->first();
							if($getarchive->status == 0){
								$astatus = '<span title="draft" class="ui label uppercase">Draft</span><span> (Archived)</span>';
							}else if($getarchive->status == 1){
								$astatus = '<span title="draft" class="ui label uppercase yellow">Sent</span><span> (Archived)</span>';
							}else if($getarchive->status == 2){
								$astatus = '<span title="draft" class="ui label uppercase text-danger">Declined</span><span> (Archived)</span>';
							}
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
					}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'astatus'=>$astatus));
		die;
	}

	public function permanentDeleteAction(Request $request)
	{
		$status = 0;
		$message = '';
		
		if ($request->isMethod('post'))
		{
			$requestData = $request->all();
			$requestData['id'] = trim($requestData['id']);
			$requestData['table'] = trim($requestData['table']);
			
			$role = Auth::user()->role;
			
			// Only admin (role 1) can permanently delete
			if($role == 1)
			{
				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));
					
					if($tableExist)
					{
						// Additional safety check for admins table (clients)
						if($requestData['table'] == 'admins')
						{
							$client = \App\Models\Admin::where('id', $requestData['id'])->first();
							
							if($client)
							{
								// Verify client is archived
								if($client->is_archived != 1)
								{
									$message = 'Only archived clients can be permanently deleted.';
								}
								// Verify archived for at least 6 months
								elseif($client->archived_on)
								{
									$archivedDate = \Carbon\Carbon::parse($client->archived_on);
									$sixMonthsAgo = \Carbon\Carbon::now()->subMonths(6);
									
									if($archivedDate->lte($sixMonthsAgo))
									{
										// Safe to delete - archived for 6+ months
										// Set is_deleted timestamp instead of actual deletion for audit trail
										$response = DB::table($requestData['table'])
											->where('id', $requestData['id'])
											->update(['is_deleted' => date('Y-m-d H:i:s')]);
										
										if($response)
										{
											$status = 1;
											$message = 'Client has been permanently deleted successfully.';
										}
										else
										{
											$message = Config::get('constants.server_error');
										}
									}
									else
									{
										$daysArchived = \Carbon\Carbon::now()->diffInDays($archivedDate);
										$daysRemaining = 180 - $daysArchived;
										$message = 'Client must be archived for at least 6 months before permanent deletion. ' . $daysRemaining . ' days remaining.';
									}
								}
								else
								{
									$message = 'Client must be archived before permanent deletion.';
								}
							}
							else
							{
								$message = 'Client not found.';
							}
						}
						else
						{
							$message = 'Permanent deletion is only allowed for clients.';
						}
					}
					else
					{
						$message = 'Table does not exist.';
					}
				}
				else
				{
					$message = 'ID or Table parameter is missing.';
				}
			}
			else
			{
				$message = 'You are not authorized to perform this action. Only administrators can permanently delete records.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function deleteAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);
			$requestData['table'] = trim($requestData['table']);

			$role = Auth::user()->role;

				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{
							if($requestData['table'] == 'admins'){
								/* if($requestData['current_status'] == 0)
								{
									$updated_status = 1;
									$message = 'Record has been enabled successfully.';
								}
								else
								{
									$updated_status = 0;
									$message = 'Record has been disabled successfully.';
								}	 */
							$o = \App\Models\Admin::where('id', $requestData['id'])->first();
							if($o->is_archived == 1){
								$is_archived = 0;
								$updateData = ['is_archived' => $is_archived, 'archived_on' => null, 'archived_by' => null];
							}else{
								$is_archived = 1;
								$updateData = ['is_archived' => $is_archived, 'archived_on' => date('Y-m-d'), 'archived_by' => Auth::user()->id];
							}
							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update($updateData);
							if($response)
							{
								$status = 1;
								$message = 'Record has been enabled successfully.';
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
							}else
							if($requestData['table'] == 'currencies'){
								$isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
								if($isexist){
									$response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();

									if($response)
									{
										$status = 1;
										$message = 'Record has been deleted successfully.';
									}
									else
									{
										$message = Config::get('constants.server_error');
									}
								}else{
									$message = 'ID does not exist, please check it once again.';
								}
							}else
							// NOTE: invoice_schedules table deletion handler removed - Invoice Schedule feature has been removed
							if($requestData['table'] == 'agents'){
								$response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->update(['is_acrchived' => 1]);

								if($response)
									{
										$status = 1;
										$message = 'Record has been Archived successfully.';
									}
									else
									{
										$message = Config::get('constants.server_error');
									}
							}else if($requestData['table'] == 'products'){
								$applicationisexist	= DB::table('applications')->where('product_id', $requestData['id'])->exists();

								if($applicationisexist){
									$message = "Can't Delete its have relation with other records";
								}else{
									$isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
									if($isexist){
									$response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
									// NOTE: template_infos table has been removed

									if($response)
									{
										$status = 1;
										$message = 'Record has been deleted successfully.';
									}
									else
									{
										$message = Config::get('constants.server_error');
									}
									}else{
										$message = 'ID does not exist, please check it once again.';
									}
								}


							}else if($requestData['table'] == 'partners'){
								$applicationisexist	= DB::table('applications')->where('partner_id', $requestData['id'])->exists();
								$productsexist	= DB::table('products')->where('partner', $requestData['id'])->exists();

								if($applicationisexist){
									$message = "Can't Delete its have relation with other records";
								}else if($productsexist){
									$message = "Can't Delete its have relation with other records";
								}else{
									$isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
									if($isexist){
									$response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
									// NOTE: template_infos table has been removed

									if($response)
									{
										$status = 1;
										$message = 'Record has been deleted successfully.';
									}
									else
									{
										$message = Config::get('constants.server_error');
									}
									}else{
										$message = 'ID does not exist, please check it once again.';
									}
								}


							}else{
                              
                                //save and send to activity log
                                if( $requestData['table'] == 'applications' ){
                                    $application_data = \App\Models\Application::select('id','client_id','partner_id','product_id')->where('id', $requestData['id'])->first();
                                    if($application_data){
                                        $productdetail = \App\Models\Product::select('name')->where('id', $application_data->product_id)->first();
                                        $partnerdetail = \App\Models\Partner::select('partner_name')->where('id', $application_data->partner_id)->first();
                                        $subject = 'removed application';

                                        $description = 'removed '.$productdetail->name;
                                        $description_other = '<small>'.$partnerdetail->partner_name.'</small>';

                                        $objs = new ActivitiesLog;
                                        $objs->client_id = $application_data->client_id;
                                        $objs->created_by = Auth::user()->id;
                                        $objs->description = '<p>'.$description.' '.$description_other.'</p>';
                                        $objs->subject = $subject;
                                        $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                                        $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                                        $objs->save();
                                    }
                                }

                              
								$response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
								if($response)
								{
									$status = 1;
									$message = 'Record has been deleted successfully.';
								}
								else
								{
									$message = Config::get('constants.server_error');
								}
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Table does not exist, please check it once again.';
				}

		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}
  
  
    public function deleteSlotAction(Request $request)
	{
        $status = 	0;
		$method = 	$request->method();
		if ($request->isMethod('post')) {
			$requestData 	= 	$request->all();
            $requestData['id'] = trim($requestData['id']);
			$requestData['table'] = trim($requestData['table']);
            //echo  $requestData['id'].'==='.$requestData['table'];dd('###');
            $role = Auth::user()->role;
            if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
			{
				// Appointment/book_service functionality removed - table deleted
                if($requestData['table'] == 'book_service_disable_slots'){
                    $message = 'This functionality has been removed. The book_service_disable_slots table no longer exists.';
                    $status = 0;
                } else {
                    $tableExist = Schema::hasTable(trim($requestData['table']));
                    if($tableExist) {
                        // Handle other tables if needed
                    } else {
                        $message = 'Table does not exist, please check it once again.';
                    }
                }
            } else {
                $message = 'Id OR Table does not exist, please check it once again.';
            }
        } else {
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function getStates(Request $request)
	{
		$status 			= 	0;
		$data				=	array();
		$method 			= 	$request->method();

		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			if(isset($requestData['id']) && !empty($requestData['id']))
			{
				$recordExist = Country::where('id', $requestData['id'])->exists();

				if($recordExist)
				{
					$data 	= 	State::where('country_id', '=', $requestData['id'])->get();

					if($data)
					{
						$status = 1;
						$message = 'Record has been fetched successfully.';
					}
					else
					{
						$message = Config::get('constants.server_error');
					}
				}
				else
				{
					$message = 'ID does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'ID does not exist, please check it once again.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'data'=>$data));
		die;
	}

	// Removed: getChapters() - McqSubject and McqChapter models/tables don't exist (dead code)

	public function sessions(Request $request)
	{
		return view('Admin.sessions');
	}

	public function getpartner(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\Partner::where('service_workflow', $catid)->where('status', 0)->orderby('partner_name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Partner</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->partner_name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}

	public function getpartnerbranch(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\Partner::where('service_workflow', $catid)->where('status', 0)->orderby('partner_name','ASC')->get();
		ob_start();
		?>
		<option value="">Select Partner & Branch</option>
		<?php
		foreach($lists as $list){
			$listsbranchs = \App\Models\PartnerBranch::where('partner_id', $list->id)->get();
			foreach($listsbranchs as $listsbranch){
			?>
			<option value="<?php echo $listsbranch->id; ?>_<?php echo $list->id; ?>"><?php echo $list->partner_name.' ('.$listsbranch->name.')'; ?></option>
			<?php
			}
		}
		echo ob_get_clean();
	}

	public function getbranchproduct(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\Product::whereRaw('? = ANY(string_to_array(branches, \',\'))', [$catid])->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select Product</option>
		<?php
		foreach($lists as $list){

			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php

		}
		echo ob_get_clean();
	}

	public function getproduct(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\Product::where('partner', $catid)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Product</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}

	public function gettemplates(Request $request){
		$id = $request->id;
		
		// Validate and sanitize the ID parameter - PostgreSQL requires valid integers
		if (empty($id) || $id === '' || $id === null) {
			echo json_encode(array('subject'=>'','description'=>''));
			return;
		}
		
		// Ensure ID is a valid integer
		if (!is_numeric($id)) {
			echo json_encode(array('subject'=>'','description'=>''));
			return;
		}
		
		$CrmEmailTemplate = \App\Models\CrmEmailTemplate::where('id', (int)$id)->first();
		if($CrmEmailTemplate){
			echo json_encode(array('subject'=>$CrmEmailTemplate->subject, 'description'=>$CrmEmailTemplate->description));
		}else{
			echo json_encode(array('subject'=>'','description'=>''));
		}
	}

	public function sendmail(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		
		// Validate required fields
		if (!isset($requestData['email_from']) || empty($requestData['email_from'])) {
			if($request->ajax() || $request->wantsJson()) {
				return response()->json(['status' => false, 'message' => 'Please select a From email address']);
			}
			return redirect()->back()->with('error', 'Please select a From email address')->withInput();
		}
		
		if (!isset($requestData['email_to']) || empty($requestData['email_to'])) {
			if($request->ajax() || $request->wantsJson()) {
				return response()->json(['status' => false, 'message' => 'Please select at least one recipient']);
			}
			return redirect()->back()->with('error', 'Please select at least one recipient')->withInput();
		}
		
		if (!isset($requestData['subject']) || empty($requestData['subject'])) {
			if($request->ajax() || $request->wantsJson()) {
				return response()->json(['status' => false, 'message' => 'Please enter email subject']);
			}
			return redirect()->back()->with('error', 'Please enter email subject')->withInput();
		}
		
		if (!isset($requestData['message']) || empty($requestData['message'])) {
			if($request->ajax() || $request->wantsJson()) {
				return response()->json(['status' => false, 'message' => 'Please enter email message']);
			}
			return redirect()->back()->with('error', 'Please enter email message')->withInput();
		}
		
		$user_id = @Auth::user()->id;
		$reciept_id = null; // Initialize as NULL for PostgreSQL integer column compatibility
		$array = array();

        if(isset($requestData['receipt'])){
            $fetchedData = InvoicePayment::where('id', '=', $requestData['receipt'])->first();
            $reciept_id = $fetchedData->id;
            $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
            ])->loadView('emails.reciept', compact('fetchedData'));
            $output = $pdf->output();
            $invoicefilename = 'receipt_'.$reciept_id.'.pdf';
            
            // Get client_id from invoice relationship for S3 path structure
            $invoice = $fetchedData->invoice;
            $client_id = $invoice ? $invoice->client_id : 'general';
            $client_info = \App\Models\Admin::select('client_id')->where('id', $client_id)->first();
            $client_unique_id = $client_info ? $client_info->client_id : 'general';
            
            // Upload to S3
            $filePath = $client_unique_id.'/invoices/receipts/'.$invoicefilename;
            Storage::disk('s3')->put($filePath, $output);
            
            // Download to temp location for email attachment
            $tempPath = sys_get_temp_dir() . '/' . $invoicefilename;
            file_put_contents($tempPath, $output);
            
            $array['file'] = $tempPath;
            $array['file_name'] = $invoicefilename;
            $array['s3_path'] = $filePath; // Store S3 path for potential cleanup if needed
        }

        if(isset($requestData['invreceipt'])){
            $invoicedetail = \App\Models\Invoice::where('id', '=', $requestData['invreceipt'])->first();
            if($invoicedetail->type == 3){
                $workflowdaa = \App\Models\Workflow::where('id', $invoicedetail->application_id)->first();
                $applicationdata = array();
                $partnerdata = array();
                $productdata = array();
                $branchdata = array();
            }else{
                $applicationdata = \App\Models\Application::where('id', $invoicedetail->application_id)->first();
                $partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
                $productdata = \App\Models\Product::where('id', @$applicationdata->product_id)->first();
                $branchdata = \App\Models\PartnerBranch::where('id', @$applicationdata->branch)->first();
                $workflowdaa = \App\Models\Workflow::where('id', @$applicationdata->workflow)->first();
            }

			$clientdata = \App\Models\Admin::where('role', 7)->where('id', $invoicedetail->client_id)->first();
			$admindata = \App\Models\Admin::where('role', 1)->where('id', $invoicedetail->user_id)->first();

            $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
            ])->loadView('emails.invoice',compact(['applicationdata','partnerdata','workflowdaa','clientdata','productdata','branchdata','invoicedetail','admindata']));
            $reciept_id = $invoicedetail->id;

            $output = $pdf->output();
            $invoicefilename = 'invoice_'.$reciept_id.'.pdf';
            
            // Get client unique ID for S3 path structure
            $client_info = \App\Models\Admin::select('client_id')->where('id', $invoicedetail->client_id)->first();
            $client_unique_id = $client_info ? $client_info->client_id : 'general';
            
            // Upload to S3
            $filePath = $client_unique_id.'/invoices/'.$invoicefilename;
            Storage::disk('s3')->put($filePath, $output);
            
            // Download to temp location for email attachment
            $tempPath = sys_get_temp_dir() . '/' . $invoicefilename;
            file_put_contents($tempPath, $output);
            
            $array['file'] = $tempPath;
            $array['file_name'] = $invoicefilename;
            $array['s3_path'] = $filePath; // Store S3 path for potential cleanup if needed
        }

		$obj = new \App\Models\MailReport;
		$obj->user_id 		=  $user_id;
		$obj->from_mail 	=  isset($requestData['email_from']) ? $requestData['email_from'] : '';
		$obj->to_mail 		=  isset($requestData['email_to']) ? implode(',',$requestData['email_to']) : '';
		if(isset($requestData['email_cc'])){
		$obj->cc 			=  implode(',',@$requestData['email_cc']);
		}
		// Handle template_id - PostgreSQL integer column cannot accept empty strings, must be NULL or valid integer
		$obj->template_id 	=  (isset($requestData['template']) && $requestData['template'] !== '' && $requestData['template'] !== null) 
								? (int)$requestData['template'] 
								: null;
		$obj->reciept_id 	=  $reciept_id;
		$obj->subject		=  isset($requestData['subject']) ? $requestData['subject'] : '';
		if(isset($requestData['type'])){
		$obj->type 			=  @$requestData['type'];
		}
		$obj->message		 =  isset($requestData['message']) ? $requestData['message'] : '';
		// Set mail_type - Required NOT NULL field for PostgreSQL (1 = manually composed/sent email)
		$obj->mail_type		=  1;
      
		$attachments = array();
      
		if(isset($requestData['checklistfile'])){
            if(!empty($requestData['checklistfile'])){
                $checklistfiles = $requestData['checklistfile'];
                $attachments = array();
                foreach($checklistfiles as $checklistfile){
                    $filechecklist =  \App\Models\UploadChecklist::where('id', $checklistfile)->first();
                    if($filechecklist){
                        $attachments[] = array('file_name' => $filechecklist->name,'file_url' => $filechecklist->file);
                    }
                }
                //$obj->attachments = json_encode($attachments);
            }
        }
      
        $attachments2 = array();
        if(isset($requestData['checklistfile_document'])){
            if(!empty($requestData['checklistfile_document'])){
                $checklistfiles_documents = $requestData['checklistfile_document'];
                $attachments2 = array();
                foreach($checklistfiles_documents as $checklistfile1){
                    $filechecklist_doc = \App\Models\Document::with('category')->where('id', $checklistfile1)->first();
                    if($filechecklist_doc){
                        $useLocalPath = in_array($filechecklist_doc->doc_type, ['education', 'migration'])
                            || ($filechecklist_doc->doc_type === 'documents' && $filechecklist_doc->category && in_array($filechecklist_doc->category->name, ['Education', 'Migration']));
                        if ($useLocalPath) {
                            $attachments2[] = array('file_name' => $filechecklist_doc->file_name, 'file_url' => public_path() . '/' . 'img/documents/' . $filechecklist_doc->myfile);
                        } else {
                            $attachments2[] = array('file_name' => $filechecklist_doc->file_name, 'file_url' => $filechecklist_doc->myfile);
                        }
                    }
                }
                //$obj->attachments = json_encode($attachments);
            }
        }

		 $attachments = array_merge($attachments, $attachments2);
        if(!empty($attachments) && count($attachments) >0){
            $obj->attachments = json_encode($attachments);
        }

		$saved	=	$obj->save();

		// When checklist email is sent: update application checklist_sent_at and log to client activity
		if(isset($requestData['checklistfile']) && !empty($requestData['checklistfile'])){
            $clientIdForLog = null;
            $logSubject = 'Checklist sent to client';
            $sentDate = now()->format('d/m/Y');
            $logDescription = 'Checklist sent on ' . $sentDate;

            if(!empty($requestData['application_id'])){
                $app = \App\Models\Application::find((int)$requestData['application_id']);
                if($app){
                    $wasAlreadySent = $app->checklist_sent_at !== null;
                    $app->checklist_sent_at = now()->toDateString();
                    $app->save();
                    $clientIdForLog = $app->client_id;
                    $logSubject = $wasAlreadySent ? 'Checklist resent to client' : 'Checklist sent to client';
                    $logDescription = $logSubject . ' on ' . $sentDate;
                }
            }
            if($clientIdForLog === null && !empty($requestData['email_to'][0])){
                $clientIdForLog = is_numeric($requestData['email_to'][0]) ? (int)$requestData['email_to'][0] : null;
            }
            if($clientIdForLog){
                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $clientIdForLog;
                $objs->created_by = Auth::user()->id;
                $objs->subject = $logSubject;
                $objs->description = $logDescription;
                $objs->task_status = 0;
                $objs->pin = 0;
                $objs->save();
            }
        }

        // When email is sent with application_id: record email reminder and log for filters
        if ($saved && !empty($requestData['application_id'])) {
            $app = \App\Models\Application::find((int)$requestData['application_id']);
            if ($app && $app->client_id) {
                \App\Models\ApplicationReminder::create([
                    'application_id' => $app->id,
                    'type' => 'email',
                    'reminded_at' => now(),
                    'user_id' => Auth::user()->id,
                ]);
                $emailSentDate = now()->format('d/m/Y');
                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $app->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->subject = 'Email reminder sent';
                $objs->description = 'Email reminder sent on ' . $emailSentDate;
                $objs->task_status = 0;
                $objs->pin = 0;
                $objs->save();
            }
        }
      
      
        if(isset($requestData['checklistfile_document'])){
            if(!empty($requestData['checklistfile_document'])){
                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $obj->to_mail;
                $objs->created_by = Auth::user()->id;
                $objs->subject = "Document Checklist sent to client";
                $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
                $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
                $objs->save();
            }
        }

		$subject = $requestData['subject'];
		$message = $requestData['message'];
		foreach($requestData['email_to'] as $l){
			if(@$requestData['type'] == 'partner'){
				$client = \App\Models\Partner::Where('id', $l)->first();
			$subject = str_replace('{Client First Name}',$client->partner_name, $subject);
			$message = str_replace('{Client First Name}',$client->partner_name, $message);
			}else if(@$requestData['type'] == 'agent'){
				$client = \App\Models\Agent::Where('id', $l)->first();
			$subject = str_replace('{Client First Name}',$client->full_name, $subject);
			$message = str_replace('{Client First Name}',$client->full_name, $message);
			}else{
				$client = \App\Models\Admin::Where('id', $l)->first();
			$subject = str_replace('{Client First Name}',$client->first_name, $subject);
			$message = str_replace('{Client First Name}',$client->first_name, $message);
			}

			$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
			$message = str_replace('{Company Name}', \App\Helpers\Helper::defaultCrmCompanyName(), $message);
			$ccarray = array();
			if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
				foreach($requestData['email_cc'] as $cc){
					$clientcc = \App\Models\Admin::Where('id', $cc)->first();
					$ccarray[] = $clientcc;
				}
			}

			if(isset($requestData['checklistfile'])){
    		    if(!empty($requestData['checklistfile'])){
    		       $checklistfiles = $requestData['checklistfile'];
    		        foreach($checklistfiles as $checklistfile){
    		           $filechecklist =  \App\Models\UploadChecklist::where('id', $checklistfile)->first();
    		           if($filechecklist){
    		            $array['files'][] =  public_path() . '/' .'checklists/'.$filechecklist->file;
    		           }
    		        }
    		    }
		    }
          
            if(isset($requestData['checklistfile_document'])){
                if(!empty($requestData['checklistfile_document'])){
                    $checklistfiles_documents = $requestData['checklistfile_document'];
                    foreach($checklistfiles_documents as $checklistfile1){
                        $filechecklist_doc = \App\Models\Document::with('category')->where('id', $checklistfile1)->first();
                        if($filechecklist_doc){
                            $useLocalPath = in_array($filechecklist_doc->doc_type, ['education', 'migration'])
                                || ($filechecklist_doc->doc_type === 'documents' && $filechecklist_doc->category && in_array($filechecklist_doc->category->name, ['Education', 'Migration']));
                            if ($useLocalPath) {
                                $array['files'][] = public_path() . '/' . 'img/documents/' . $filechecklist_doc->myfile;
                            } elseif ($filechecklist_doc->doc_type == 'documents') {
                                $fileUrl = $filechecklist_doc->myfile; // AWS S3 link
                                if (filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                                    $tempPath = sys_get_temp_dir() . '/' . basename($fileUrl);
                                    file_put_contents($tempPath, file_get_contents($fileUrl));
                                    $array['files'][] = $tempPath;
                                } else {
                                    $array['files'][] = $fileUrl;
                                }
                            }
                        }
                    }
                }
            }
          
            //echo "<pre>array=";print_r($array);die;
          
		    /*if($request->hasfile('attach'))
            {
                 $array['filesatta'][] =  $request->attach;
            }*/
          
            // Process Uploaded Files
            if ($request->hasFile('attach')) {
                foreach ($request->file('attach') as $file1) {
                    $array['filesatta'][] =  $file1;
                }
            }
           
            //$this->send_compose_template($client->email, $subject, $requestData['email_from'], $message, '', $array,@$ccarray);
          
            try {
                $attachments = [];

                if(isset($array['files'])){
                    $attachments = array_merge($attachments, $array['files']);
                }

                if(isset($array['filesatta'])){
                    foreach($array['filesatta'] as $file) {
                        $filename = time().'_'.$file->getClientOriginalName(); // Unique filename
                        $filePath = storage_path('app/uploads/'.$filename); // Save in storage/uploads folder

                        // Move the file to storage folder
                        $file->move(storage_path('app/uploads'), $filename);

                        // Add saved file path to attachments
                        $attachments[] = $filePath;
                    }
                }

                $ccarray = [];
                if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
                    foreach($requestData['email_cc'] as $cc){
                        $clientcc = \App\Models\Admin::Where('id', $cc)->first();
                        if($clientcc) {
                            $ccarray[] = $clientcc->email;
                        }
                    }
                }

                $this->emailService->sendEmail(
                    'emails.template',
                    ['content' => $message],
                    $client->email,
                    $subject,
                    $requestData['email_from'],
                    $attachments,
                    $ccarray
                );
                
                // Clean up temp files after email is sent
                if(isset($array['file']) && file_exists($array['file'])){
                    @unlink($array['file']);
                }

                // Return JSON response for AJAX requests
                if($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => true, 'message' => 'Email sent successfully!']);
                }
                return redirect()->back()->with('success', 'Email sent successfully!');
            } catch (\Exception $e) {
                // Return JSON response for AJAX requests
                if($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
                }
                return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage())->withInput();
            }
		}
        if(!empty($array['file'])){
            unset($array['file']);
        }
        if(!$saved) {
            // Return JSON response for AJAX requests
            if($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => Config::get('constants.server_error')]);
            }
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        } else {
            // Return JSON response for AJAX requests
            if($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Email Sent Successfully']);
            }
            return redirect()->back()->with('success', 'Email Sent Successfully');
        }
	}


	public function getbranch(Request $request){
		$catid = $request->cat_id;
		$pro = \App\Models\Product::where('id', $catid)->first();
		if($pro){
		$user_array = explode(',',$pro->branches);
		$lists = \App\Models\PartnerBranch::WhereIn('id',$user_array)->Where('partner_id',$pro->partner)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Branch</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		}else{
			?>
			<option value="">Select a Branch</option>
			<?php
		}
		echo ob_get_clean();
	}

	public function getnewPartnerbranch(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\PartnerBranch::Where('partner_id',$catid)->orderby('name','ASC')->get();



		ob_start();
		?>
		<option value="">Select a Branch</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name.'('.$list->city.')'; ?></option>
			<?php
		}

		echo ob_get_clean();
	}


	// Removed: getsubjects() - subjects table has been dropped

	public function getproductbranch(Request $request){
		$catid = $request->cat_id;
		$sss = \App\Models\Product::where('id', $catid)->first();
		if($sss){
		$lists = \App\Models\PartnerBranch::where('id', $sss->branches)->get();
		ob_start();
		?>
		<option value="">Please select branch</option>
		<?php
		foreach($lists as $list){

			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php

		}
		}else{
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}

	// Removed: getsubcategories() - Dead code that queries non-existent fields (cat_id, sub_id) in SubCategory model


		public function getpartnerajax(Request $request){
	    $fetchedData = \App\Models\Partner::where('partner_name','LIKE', '%'.$request->likevalue.'%')->get();
		$agents = array();
		foreach($fetchedData as $list){
			$agents[] = array(
				'id' => $list->id,
				'agent_id' => $list->partner_name,
				'agent_company_name' => $list->partner_name,
			);
		}

		echo json_encode($agents);
	}

	public function getassigneeajax(Request $request){
	    $squery = $request->likevalue;
	     $fetchedData = \App\Models\Admin::where('role', '!=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'ILIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'ILIKE','%'.$squery.'%')->orwhere('last_name', 'ILIKE','%'.$squery.'%')->orwhere('client_id', 'ILIKE','%'.$squery.'%')->orwhere('phone', 'ILIKE','%'.$squery.'%')->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'ILIKE', "%".$squery."%");

            })
            ->get();


	$agents = array();
	foreach($fetchedData as $list){
		$agents[] = array(
			'id' => $list->id,
			'agent_id' => $list->first_name.' '.$list->last_name,
			'assignee' => $list->first_name.' '.$list->last_name,
		);
	}

	echo json_encode($agents);
}

	public function allnotification(Request $request){
		$query = \App\Models\Notification::where('receiver_id', Auth::user()->id);
		
		// Filter by read/unread status
		if($request->has('filter') && $request->filter != 'all'){
			if($request->filter == 'unread'){
				$query->where('receiver_status', 0);
			} elseif($request->filter == 'read'){
				$query->where('receiver_status', 1);
			}
		}
		
		// Search functionality
		if($request->has('search') && !empty($request->search)){
			$search = $request->search;
			$query->where('message', 'LIKE', '%'.$search.'%');
		}
		
		$lists = $query->orderby('created_at','DESC')->paginate(20)->appends($request->query());
		
		// Get counts for filter tabs
		$totalCount = \App\Models\Notification::where('receiver_id', Auth::user()->id)->count();
		$unreadCount = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count();
		$readCount = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 1)->count();
		
		return view('Admin.notifications', compact(['lists', 'totalCount', 'unreadCount', 'readCount']));
	}
	
	public function markNotificationAsRead(Request $request){
		if($request->has('id') && !empty($request->id)){
			$notification = \App\Models\Notification::where('id', $request->id)
				->where('receiver_id', Auth::user()->id)
				->first();
			
			if($notification){
				$notification->receiver_status = 1;
				$notification->save();
				return response()->json(['success' => true, 'message' => 'Notification marked as read']);
			}
		}
		return response()->json(['success' => false, 'message' => 'Notification not found']);
	}
	
	public function markAllNotificationsAsRead(Request $request){
		$updated = \App\Models\Notification::where('receiver_id', Auth::user()->id)
			->where('receiver_status', 0)
			->update(['receiver_status' => 1]);
		
		return response()->json(['success' => true, 'message' => $updated.' notifications marked as read']);
	}
  
    public function partnerChangeToInactive(Request $request)
	{
		$status 			= 	1;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();
            $requestData['id'] = trim($requestData['id']);
            $requestData['table'] = trim($requestData['table']);

			$astatus = '';
			$role = Auth::user()->role;
			if($role == 1 || $role == 7 || $role == 12 || $role == 11) { //11=>account staff team
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
                {
					$tableExist = Schema::hasTable(trim($requestData['table']));
                    if($tableExist) {
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                        if($recordExist) {
                            $updated_status = 1;
                            $message = 'Record has been inactive successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							$getarchive = 	DB::table($requestData['table'])->where('id', $requestData['id'])->first();
							if($getarchive->status == 0){
								$astatus = '<span title="draft" class="ui label uppercase">Active</span>';
							} else if($getarchive->status == 1){
								$astatus = '<span title="draft" class="ui label uppercase yellow">Inactive</span>';
							}
							if($response) {
								$status = 1;
							} else {
								$message = Config::get('constants.server_error');
							}
						} else {
							$message = 'ID does not exist, please check it once again.';
						}
					} else {
						$message = 'Table does not exist, please check it once again.';
					}
				} else {
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			} else {
				$message = 'You are not authorized person to perform this action.';
			}
		} else {
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'astatus'=>$astatus));
		die;
	}


    public function partnerChangeToActive(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();
            $requestData['id'] = trim($requestData['id']);
            $requestData['table'] = trim($requestData['table']);

			$astatus = '';
			$role = Auth::user()->role;
			if($role == 1 || $role == 7 || $role == 12 || $role == 11) { //11=>account staff team
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
                {
					$tableExist = Schema::hasTable(trim($requestData['table']));
                    if($tableExist) {
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                        if($recordExist) {
                            $updated_status = 0;
                            $message = 'Record has been active successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							$getarchive = 	DB::table($requestData['table'])->where('id', $requestData['id'])->first();
							if($getarchive->status == 0){
								$astatus = '<span title="draft" class="ui label uppercase">Active</span>';
							} else if($getarchive->status == 1){
								$astatus = '<span title="draft" class="ui label uppercase yellow">Inactive</span>';
							}
							if($response) {
								$status = 0;
							} else {
								$message = Config::get('constants.server_error');
							}
						} else {
							$message = 'ID does not exist, please check it once again.';
						}
					} else {
						$message = 'Table does not exist, please check it once again.';
					}
				} else {
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			} else {
				$message = 'You are not authorized person to perform this action.';
			}
		} else {
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'astatus'=>$astatus));
		die;
	}
  
    //Note deadline task complete
     public function updatenotedeadlinecompleted(Request $request,Note $note)
     {
         $data = $request->all(); //dd($data['id']);
         $note = Note::where('id',$data['id'])->update(['status'=>'1']);
         //$note = 1;
         if($note){
             $note_data = Note::where('id',$data['id'])->first(); //dd($note_data);
             if($note_data){
                 $admin_data = Admin::where('id',$note_data['assigned_to'])->first(); //dd($admin_data);
                 if($admin_data){
                     $assignee_name = $admin_data['first_name']." ".$admin_data['last_name'];
                 } else {
                     $assignee_name = 'N/A';
                 }
                 $objs = new ActivitiesLog;
                 $objs->client_id = $note_data['client_id'];
                 $objs->created_by = Auth::user()->id;

                 //$objs->subject = 'Partner closed action in group '.$note_data['task_group'].' with deadline '.date('d/m/Y',strtotime($note_data['note_deadline'])).' to '.@$assignee_name;
                 //$objs->description = '<p>'.@$note_data['description'].'</p>';

                 $objs->subject = 'Closed Note Deadline';
                 $objs->description = '<span class="text-semi-bold">'.@$note_data['title'].'</span><p>'.@$note_data['description'].'</p>';


                 if(Auth::user()->id != @$note_data['assigned_to']){
                     $objs->use_for = @$note_data['assigned_to'];
                 } else {
                     $objs->use_for = null; // Use null instead of empty string for PostgreSQL
                 }

                 $objs->followup_date = @$note_data['updated_at'];
                 $objs->task_group = 'partner';
                 $objs->task_status = 1; //maked completed
                 $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                 $objs->save();
             }
             $response['status'] 	= 	true;
             $response['message']	=	'Note Deadline updated successfully';
         } else {
             $response['status'] 	= 	false;
             $response['message']	=	'Please try again';
         }
         echo json_encode($response);
     }

     //Note deadline extend
     public function extenddeadlinedate(Request $request)
     {
         $requestData = $request->all(); //dd($requestData);
         if( \App\Models\Note::where('id',$requestData['note_id'])->count() >0 ){
             $note_data = \App\Models\Note::where('id',$requestData['note_id'])->get();
             //dd($note_data);
             if( !empty($note_data) && count($note_data) >0 ){

                 if(isset($requestData['note_deadline']) && $requestData['note_deadline'] != ''){
                     $note_deadlineArr = explode("/",$requestData['note_deadline']);
                     $note_deadlineArrFormated = $note_deadlineArr[2]."-".$note_deadlineArr[1]."-".$note_deadlineArr[0];
                 } else {
                     $note_deadlineArrFormated = NULL;
                 }

                 foreach ($note_data as $note_val) {  //dd($note_val->unique_group_id);
                     $updated = \App\Models\Note::where('id', $note_val->id)
                     ->update([
                         'description' => $requestData['description'],
                         'note_deadline' => $note_deadlineArrFormated,
                         'user_id' => Auth::user()->id,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
                     if( $updated ){
                         $note_info = \App\Models\Note::where('id',$note_val->id)->first(); //dd($note_info);
                         // Create a notification for the current assignee
                         $o = new \App\Models\Notification;
                         $o->sender_id = Auth::user()->id;
                         $o->receiver_id = $note_info['assigned_to'];
                         $o->module_id = $note_info['client_id'];
                         $o->url = route('partners.detail', @$note_info['client_id']);
                         $o->notification_type = 'client';
                         $o->message = 'Action Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . date('d/M/Y h:i A', strtotime(@$note_info['followup_date']));
                         $o->seen = 0; // Set seen to 0 (unseen) for new notifications
                         $o->save();

                         $objs = new ActivitiesLog;
                         $objs->client_id = $note_info['client_id'];
                         $objs->created_by = Auth::user()->id;
                         $objs->subject = 'Extended Note Deadline';
                       
                          //Get assigner name
                        $assignee_info = \App\Models\Admin::select('id','first_name','last_name')->where('id', $note_info['assigned_to'])->first();
                        if($assignee_info){
                            $assignee_name = $assignee_info->first_name;
                        } else {
                            $assignee_name = 'N/A';
                        }

                        $note_info_title = 'Partner assigned action with deadline '.$requestData['note_deadline'].' to '.$assignee_name;
                       
                        $objs->description = '<span class="text-semi-bold">'.@$note_info_title.'</span><p>'.@$note_info['description'].'</p>';
                       
                         if (Auth::user()->id != $note_info['user_id']) {
                             $objs->use_for = $note_info['user_id'];
                         } else {
                             $objs->use_for = null; // Use null instead of empty string for PostgreSQL
                         }
                         $objs->followup_date = $note_info['followup_date'];
                         $objs->task_group = 'partner';
                         $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                         $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                         $objs->save();
                     }
                 }
             }
         }
        echo json_encode(array('success' => true, 'message' => 'successfully updated', 'clientID' => $note_info['client_id'] ));
        exit;
    }

    /**
     * Complete an action (Note) with a completion message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeAction(Request $request)
    {
        try {
            $request->validate([
                'action_id' => 'required|integer|exists:notes,id',
                'completion_message' => 'required|string|min:1'
            ]);

            $note = Note::find($request->action_id);
            
            if (!$note) {
                return response()->json([
                    'status' => false,
                    'message' => 'Action not found'
                ], 404);
            }

            // Check if already completed
            if ($note->status == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'This action is already completed'
                ], 400);
            }

            // Get client_id from request or note
            $clientId = $request->input('client_id', $note->client_id);
            
            if (!$clientId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client ID is required'
                ], 400);
            }

            // Update note status to completed
            $note->status = 1;
            $note->save();

            // Create activity log entry
            $activity = new ActivitiesLog();
            $activity->client_id = $clientId;
            $activity->created_by = Auth::user()->id;
            $activity->subject = 'Completed action';
            $activity->description = '<span class="text-semi-bold">Action Completed</span><p>' . htmlspecialchars($request->completion_message) . '</p>';
            $activity->task_status = 0; // Activity, not task
            $activity->pin = 0;
            $activity->save();
            
            // If this action is related to an application, also log to ApplicationActivitiesLog
            if (!empty($note->application_id)) {
                // Get the application to determine the stage
                $application = \App\Models\Application::find($note->application_id);
                if ($application) {
                    // Get the ORIGINAL note description (entered when assigning the task)
                    $originalNoteDescription = strip_tags($note->description);
                    
                    $obj1 = new \App\Models\ApplicationActivitiesLog;
                    $obj1->stage = $application->stage;
                    $obj1->type = 'task';
                    $obj1->comment = 'completed a task';
                    $obj1->title = 'Action completed by '.Auth::user()->first_name.' '.Auth::user()->last_name;
                    // Show BOTH the original note description AND the completion message
                    $obj1->description = '<span class="text-semi-bold">Action Completed</span><p>' . htmlspecialchars($originalNoteDescription) . '</p><hr><p><strong>Completion Note:</strong> ' . htmlspecialchars($request->completion_message) . '</p>';
                    $obj1->app_id = $note->application_id;
                    $obj1->user_id = Auth::user()->id;
                    $obj1->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Action completed successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) {
                    return is_array($errors) ? implode(', ', $errors) : $errors;
                }, $e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error completing action: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while completing the action. Please try again.'
            ], 500);
        }
    }
}
