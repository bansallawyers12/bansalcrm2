<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\StaffRole;

use Auth;
use Config;

class StaffroleController extends Controller
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
     * All Staff Roles.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	
	//check authorization end
	$query 		= StaffRole::query();
		 
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.staffrole.index',compact(['lists', 'totalData']));	

		//return view('Admin.usertype.index');	
	}
	
	public function create(Request $request) 
	{
			//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		return view('Admin.staffrole.create');	
	} 
	
	public function store(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										//'usertype' => 'required|max:255|unique:user_roles',
										
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new StaffRole;
			$obj->name	=	@$requestData['name'];
			$obj->description	=	@$requestData['description'];
			$obj->module_access	=	json_encode(@$requestData['module_access']);
			
			$saved				=	$obj->save(); 
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('staffrole.index')->with('success', 'Staff Role added Successfully');
			}				
		}	

		return view('Admin.staffrole.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{			
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			/* $this->validate($request, [
									'usertype' => 'required|max:255|unique:user_roles,usertype,'.$requestData['id']
								  ]); */									  
									  
			$obj				= 	StaffRole::find($requestData['id']);
			$obj->name	=	@$requestData['name'];
			$obj->description	=	@$requestData['description'];
			$obj->module_access	=	json_encode(@$requestData['module_access']);
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('staffrole.index')->with('success', 'Staff Role Edited Successfully');
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(StaffRole::where('id', '=', $id)->exists()) 
				{
					$fetchedData = StaffRole::find($id);
					return view('Admin.staffrole.edit', compact(['fetchedData']));
				}
				else
				{
					return redirect()->route('staffrole.index')->with('error', 'Staff Role does not exist');
				}	
			}
			else
			{
				return redirect()->route('staffrole.index')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
}
