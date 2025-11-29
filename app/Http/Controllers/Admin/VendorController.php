<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Subject;
use App\Country;
use App\VendorSubject;

use Auth;
use Config;

class VendorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Vendor', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		$query 		= Admin::where('role', '=', 2); //only for test series vendors
		
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term_first')) 
		{
			$search_term_first 		= 	$request->input('search_term_first');
			if(trim($search_term_first) != '')	
			{
				$query->where('first_name', 'LIKE', '%' . $search_term_first . '%');
			}
		}
		if ($request->has('search_term_last')) 
		{
			$search_term_last 		= 	$request->input('search_term_last');
			if(trim($search_term_last) != '')	
			{
				$query->where('last_name', 'LIKE', '%' . $search_term_last . '%');
			}
		}
		if ($request->has('search_term_from')) 
		{
			$search_term_from 		= 	$request->input('search_term_from');
			if(trim($search_term_from) != '')
			{
				$query->whereDate('created_at', '>=', $search_term_from);
			}
		}
		if ($request->has('search_term_to')) 
		{	
			$search_term_to 		= 	$request->input('search_term_to');
			if(trim($search_term_to) != '')
			{
				$query->whereDate('created_at', '<=', $search_term_to);
			}	
		}

		if ($request->has('search_term_first') || $request->has('search_term_last') || $request->has('search_term_from') || $request->has('search_term_to')) 
		{
			$totalData 	= $query->count();
		}
		
		$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));	

		return view('Admin.vendor.index',compact(['lists', 'totalData']));	
	}
	/**
     * Add Vendor.
     *
     * @return \Illuminate\Http\Response
     */
	public function addVendor(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Vendor', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		/* Get all Select Data */	
			$subjects = Subject::select('id', 'subject_name')->where('status', '=', 1)->get();
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();	
		/* Get all Select Data */	
		
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();

			$this->validate($request, [
										'subject' => 'required',
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:191|unique:admins',
										'decrypt_password' => 'required|min:6|max:12',
										'phone' => 'required|max:12|unique:admins',
										'country' => 'required',
										'state' => 'required',
										'city' => 'required|max:255',
										'address' => 'required',
										'zip' => 'required|max:40'
									  ]);							
									  
			$obj							= 	new Admin;
			$obj->role						=	2;// for test series vendor
			$obj->first_name				=	@$requestData['first_name'];
			$obj->last_name					=	@$requestData['last_name'];
			$obj->email						=	@$requestData['email'];
			$obj->password					=	Hash::make(@$requestData['decrypt_password']);
			$obj->decrypt_password			=	@$requestData['decrypt_password'];
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
				//vendor subjects start	
					$vendor_id = $obj->id; //last inserted vendor id
					
					if(isset($requestData['subject']) && !empty($requestData['subject']))
					{		
						foreach($requestData['subject'] as $subject)
						{
							$objVendorSubject 					=	new VendorSubject;
							$objVendorSubject->vendor_id		= 	@$vendor_id;
							$objVendorSubject->subject_id		= 	@$subject;
							$saved								=	$objVendorSubject->save();
						}
					}		
				//vendor subjects end		
				
				return Redirect::to('/admin/vendors')->with('success', 'Vendor'.Config::get('constants.added'));
			}
		}
		return view('Admin.vendor.add_vendor', compact(['subjects', 'countries']));		
	}
	
	public function editVendor(Request $request, $id = NULL)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Vendor', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		/* Get all Select Data */	
			$subjects = Subject::select('id', 'subject_name')->where('status', '=', 1)->get();
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();		
		/* Get all Select Data */
	
		if ($request->isMethod('post')) 
		{	
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'subject' => 'required',
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'decrypt_password' => 'required|min:6|max:12',
										'phone' => 'required|max:14|unique:admins,phone,'.$requestData['id'],
										'country' => 'required',
										'state' => 'required',
										'city' => 'required|max:255',
										'address' => 'required',
										'zip' => 'required|max:40'
									  ]);
								  					  
			$obj							= 	Admin::find(@$requestData['id']);
			
			$obj->role						=	2;// for test series vendor
			$obj->first_name				=	@$requestData['first_name'];
			$obj->last_name					=	@$requestData['last_name'];
			$obj->password					=	Hash::make(@$requestData['decrypt_password']);
			$obj->decrypt_password			=	@$requestData['decrypt_password'];
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
				//Remove old vendor subjects start	
					DB::table('vendor_subjects')->where('vendor_id', @$requestData['id'])->delete();
				//Remove old vendor subjects start
				
				//vendor subjects start
					if(isset($requestData['subject']) && !empty($requestData['subject']))
					{
						foreach($requestData['subject'] as $subject)
						{
							$objVendorSubject 					=	new VendorSubject;
							$objVendorSubject->vendor_id		= 	@$requestData['id'];
							$objVendorSubject->subject_id		= 	@$subject;
							$saved								=	$objVendorSubject->save();
						}
					}	
				//vendor subjects end

				return Redirect::to('/admin/vendors')->with('success', 'Vendor'.Config::get('constants.edited'));
			}		
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
	
				if(Admin::where('id', '=', $id)->exists())
				{
					$fetchedData = Admin::find($id);
					
					$vendorSubjects = VendorSubject::select('id', 'subject_id')->where('vendor_id', '=', $id)->with(['subject'=>function($query){
							$query->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
							$query->with(['course' => function($subQuery){
								$subQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subQuery){
								$subQuery->select('id', 'test_series_type');
							}, 'group' => function($subQuery){
								$subQuery->select('id', 'group_name');
							}]);
						}])->get();

					//selected subjects start
						$selectedSubjects = array();
						foreach($vendorSubjects as $subject)
						{
							array_push($selectedSubjects, $subject->subject_id);
						}
					//selected subjects end		
					
					return view('Admin.vendor.edit_vendor', compact(['fetchedData', 'subjects', 'countries', 'selectedSubjects', 'vendorSubjects']));
				}
				else
				{
					return Redirect::to('/admin/vendors')->with('error', 'Vendor'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/vendors')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
	
	public function viewVendor(Request $request, $id)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Vendor', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(Admin::where('id', '=', $id)->exists()) 
			{
				$fetchedData 		= 	Admin::where('id', '=', $id)->with('countryData', 'stateData')->first();

				$vendorSubjects = VendorSubject::where('vendor_id', '=', $id)->with(['subject'=>function($query){
							$query->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
							$query->with(['course' => function($subQuery){
								$subQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subQuery){
								$subQuery->select('id', 'test_series_type');
							}, 'group' => function($subQuery){
								$subQuery->select('id', 'group_name');
							}]);
						}])->get();
						
				return view('Admin.vendor.view_vendor', compact(['fetchedData', 'vendorSubjects']));
			}
			else
			{
				return Redirect::to('/admin/vendors')->with('error', 'Vendor'.Config::get('constants.not_exist'));
			}
		}
		else
		{
			return Redirect::to('/admin/vendors')->with('error', Config::get('constants.unauthorized'));
		}
	}
}
