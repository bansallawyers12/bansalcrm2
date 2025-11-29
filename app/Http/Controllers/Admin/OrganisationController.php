<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Country;

use Auth;
use Config;

class OrganisationController extends Controller
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
     * All Organisation.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Organisation', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		$query 		= Admin::where('role', '=', 3);
		
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term_first')) 
		{
			$search_term_first 		= 	$request->input('search_term_first');
			if(trim($search_term_first) != '')	
			{
				$query->where('first_name', 'LIKE', '%' . $search_term_first . '%');
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
		
		if ($request->has('search_term_first') || $request->has('search_term_from') || $request->has('search_term_to')) 
		{
			$totalData 	= $query->count();
		}
		
		$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));	

		return view('Admin.organisation.index',compact(['lists', 'totalData']));	
	}
	/**
     * Add Organisation.
     *
     * @return \Illuminate\Http\Response
     */
	public function addOrganisation(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Organisation', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		/* Get all Select Data */	
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();		
		/* Get all Select Data */
		
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();

			$this->validate($request, [
										'first_name' => 'required|max:255',
										'email' => 'required|max:191|unique:admins',
										'password' => 'required|min:6|max:10',
										'phone' => 'nullable|max:12|unique:admins',
										'city' => 'nullable|max:255',
										'zip' => 'nullable|max:40'
									  ]);		
									  
			$obj							= 	new Admin;
			$obj->role						=	3; //for Organisations
			$obj->first_name				=	@$requestData['first_name'];
			$obj->email						=	@$requestData['email'];
			$obj->password					=	Hash::make(@$requestData['password']);
			$obj->decrypt_password			=	@$requestData['password'];
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
				return Redirect::to('/admin/organisations')->with('success', 'Organisation'.Config::get('constants.added'));
			}
		}
		return view('Admin.organisation.add_organisation',  compact(['countries']));		
	}
	
	public function editOrganisation(Request $request, $id = NULL)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Organisation', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		/* Get all Select Data */	
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();		
		/* Get all Select Data */
	
		if ($request->isMethod('post')) 
		{	
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'password' => 'nullable|min:6|max:10',
										'phone' => 'nullable|max:12|unique:admins,phone,'.$requestData['id'],
										'city' => 'nullable|max:255',
										'zip' => 'nullable|max:40'
									  ]);
								  					  
			$obj							= 	Admin::find($requestData['id']);
			
			$obj->first_name				=	@$requestData['first_name'];
			
			if(!empty(@$requestData['password']))
				{
					$obj->password			=	Hash::make(@$requestData['password']);
					$obj->decrypt_password	=	@$requestData['password'];
				}
				
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
				return Redirect::to('/admin/organisations')->with('success', 'Organisation'.Config::get('constants.edited'));
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
		
					return view('Admin.organisation.edit_organisation', compact(['fetchedData', 'countries']));
				}
				else
				{
					return Redirect::to('/admin/organisations')->with('error', 'Organisation'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/organisations')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
	
	public function viewOrganisation(Request $request, $id)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Organisation', $request->route()->getActionMethod(), Auth::user()->role);
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
				
				return view('Admin.organisation.view_organisation', compact(['fetchedData']));
			}
			else
			{
				return Redirect::to('/admin/organisations')->with('error', 'Organisation'.Config::get('constants.not_exist'));
			}
		}
		else
		{
			return Redirect::to('/admin/organisations')->with('error', Config::get('constants.unauthorized'));
		}
	}
}
