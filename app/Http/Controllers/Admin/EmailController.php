<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Email; 
  
use Auth; 
use Config;

class EmailController extends Controller
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
			
			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
	//check authorization end 
	
	$query 		= Email::query();
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.emails.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.feature.producttype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.emails.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'email' => 'required|max:255|unique:emails'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Email; 
			$obj->email	=	@$requestData['email'];
			$obj->email_signature	=	@$requestData['email_signature'];
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	@$requestData['password'];
			$obj->status	=	@$requestData['status'];
			$obj->user_id	=	json_encode(@$requestData['users']);
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/emails')->with('success', 'Email Added Successfully');
			}				
		}	

		return view('Admin.feature.emails.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'email' => 'required|max:255|unique:emails,email,'.$requestData['id']
									  ]);
								  					  
			$obj			= 	Email::find(@$requestData['id']);
			$obj->email	=	@$requestData['email'];
			$obj->email_signature	=	@$requestData['email_signature'];
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	@$requestData['password'];
			$obj->status	=	@$requestData['status'];
			$obj->user_id	=	json_encode(@$requestData['users']);
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/emails')->with('success', 'Email Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Email::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Email::find($id);
					return view('Admin.feature.emails.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/emails')->with('error', 'Email Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/emails')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
}
