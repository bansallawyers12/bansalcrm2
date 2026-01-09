<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\UserRole;
use App\Models\UserType;
 
use Auth;
use Config;

class UserController extends Controller
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
		$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('first_name','ASC')->paginate(config('constants.limit'));		
		return view('Admin.users.active',compact(['lists', 'totalData']));	
	}
	
	public function create(Request $request)
	{
			//check authorization start	
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		$usertype 		= UserRole::all();
		return view('Admin.users.create',compact(['usertype']));	
	}
	
	public function store(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:255|unique:admins',
										'password' => 'required|max:255|confirmed',
										'phone' => 'required',
										'role' => 'required',
										
									  ]);
			
			
			
			$obj				= 	new Admin;
						
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->email		=	@$requestData['email'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->position		=	@$requestData['position'];
			$obj->password		=	Hash::make(@$requestData['password']);
			
			$obj->phone			=	@$requestData['phone'];
			$obj->role			=	@$requestData['role'];
			$obj->office_id		=	@$requestData['office'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->team		    =	@$requestData['team'];
			if(isset($requestData['show_dashboard_per'])){
			    $obj->show_dashboard_per		=	1;
			}else{
			     $obj->show_dashboard_per		=	0;
			}
			
			if(isset($requestData['permission']) && is_array($requestData['permission']) ){
                $obj->permission		=	implode(",",$requestData['permission']);
			}else{
			    $obj->permission		=	"";
			}
			
			$saved				=	$obj->save();  
			
			if($requestData['role'] == 7){ //role type = client(7)
		    	$objs				= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($requestData['first_name']).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/users/active')->with('success', 'User added Successfully');
			}				
		}	

		return view('Admin.users.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
		//check authorization start	
		$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
		if($check)
		{
			return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
		}	
		//check authorization end
		$usertype 		= UserRole::all();
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'phone' => 'required|max:255',
									]);
								  					  
			$obj				= 	Admin::find(@$requestData['id']);
						
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->email		=	@$requestData['email'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->position		=	@$requestData['position'];
			
			$obj->phone			=	@$requestData['phone'];
			$obj->role			=	@$requestData['role'];
			$obj->office_id		=	@$requestData['office'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->team		    =	@$requestData['team'];
			
			if( isset($requestData['permission']) && $requestData['permission'] !="" ){
			    $obj->permission		=	implode(",", $requestData['permission'] );
			}else{
			    $obj->permission		=	"";
			}
			
			if(isset($requestData['show_dashboard_per'])){
			    $obj->show_dashboard_per		=	1;
			}else{
			     $obj->show_dashboard_per		=	0;
			}
			
			if(!empty(@$requestData['password']))
			{		
				$obj->password				=	Hash::make(@$requestData['password']);
			}
			
			$obj->phone						=	@$requestData['phone'];
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/users/view/'.@$requestData['id'])->with('success', 'User Edited Successfully');
			}				
		}

		else
		{	
			if(isset($id) && !empty($id))
			{
				//$id = $this->decodeString($id);	
				if(Admin::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Admin::find($id);
					return view('Admin.users.edit', compact(['fetchedData', 'usertype']));
				}
				else
				{
					return Redirect::to('/admin/users')->with('error', 'User Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/users')->with('error', Config::get('constants.unauthorized'));
			}		
		}	
		
	}
	
	public function savezone(Request $request)
	{
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$obj							= 	Admin::find(@$requestData['user_id']);
						
			$obj->time_zone				=	@$requestData['timezone'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/users/view/'.@$requestData['user_id'])->with('success', 'User Edited Successfully');
			}				
		}
	
		
	}
	
	
	public function view(Request $request, $id)
	{
		if(isset($id) && !empty($id))
			{
				
				
				if(Admin::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Admin::find($id);
					return view('Admin.users.view', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/users/active')->with('error', 'User Not Exist');
				}	
			}
	}
	public function active(Request $request)
	{	
	    //dd($request->all());
        $req_data = $request->all();
        if( isset($req_data['search_by'])  && $req_data['search_by'] != ""){
            $search_by = $req_data['search_by'];
        } else {
            $search_by = "";
        }
        //dd($search_by);
        if($search_by) { //if search string is present
            $query 		= Admin::Where('role', '!=', '7')
            ->Where('status', '=', 1)
            ->where(function($q) use($search_by) {
                $q->where('first_name', 'LIKE', '%'.$search_by.'%')
                ->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
            })->with(['usertype']);

        } else {
            $query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']);
        }
        
		//$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('first_name','ASC')->paginate(config('constants.limit'));		
		return view('Admin.users.active',compact(['lists', 'totalData']));	
	}
	
	public function inactive(Request $request)
	{	
		$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 0)->with(['usertype']); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('first_name','ASC')->paginate(config('constants.limit'));		
		return view('Admin.users.inactive',compact(['lists', 'totalData']));	
	}
	
	public function invited(Request $request)
	{	
		$query 		= Admin::Where('role', '!=', '7')->with(['usertype']); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('first_name','ASC')->paginate(config('constants.limit'));		
		return view('Admin.users.invited',compact(['lists', 'totalData']));	
	}
}
