<?php
namespace App\Http\Controllers\AdminConsole;

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
	
	try {
			$query 		= Email::query();
			$totalData 	= $query->count();	//for all data
			$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		} catch (\Throwable $e) {
			$totalData 	= 0;
			$lists		= new \Illuminate\Pagination\LengthAwarePaginator([], 0, (int) config('constants.limit', 20));
		}
		//dd($totalData,$lists->toArray());
		return view('AdminConsole.emails.index',compact(['lists', 'totalData'])); 	
		
		//return view('AdminConsole.producttype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('AdminConsole.emails.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'email' => 'required|max:255|unique:emails',
										'users' => 'required|array|min:1',
										'users.*' => 'required'
									  ], [
										'users.required' => 'Please select at least one user for User Sharing.',
										'users.min' => 'Please select at least one user for User Sharing.'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Email; 
			$obj->email	=	@$requestData['email'];
			$obj->email_signature	=	@$requestData['email_signature'];
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	@$requestData['password'];
			$obj->status	=	($request->has('status') && $request->input('status')) ? 1 : 0;
			$obj->user_id	=	json_encode(@$requestData['users']);
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/adminconsole/emails')->with('success', 'Email Added Successfully');
			}				
		}	

		return view('AdminConsole.emails.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'email' => 'required|max:255|unique:emails,email,'.$requestData['id'],
										'users' => 'required|array|min:1',
										'users.*' => 'required'
									  ], [
										'users.required' => 'Please select at least one user for User Sharing.',
										'users.min' => 'Please select at least one user for User Sharing.'
									  ]);
								  					  
			$obj			= 	Email::find(@$requestData['id']);
			$obj->email	=	@$requestData['email'];
			$obj->email_signature	=	@$requestData['email_signature'];
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	@$requestData['password'];
			$obj->status	=	($request->has('status') && $request->input('status')) ? 1 : 0;
			$obj->user_id	=	json_encode(@$requestData['users']);
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/adminconsole/emails')->with('success', 'Email Edited Successfully');
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
					return view('AdminConsole.emails.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/adminconsole/emails')->with('error', 'Email Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/adminconsole/emails')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
}
