<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Imports\ImportUser;
use App\Admin;
use App\Agent;
use App\RepresentingPartner;
 
use Auth;
use Config;

class AgentController extends Controller
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
	public function active(Request $request)
	{
		$query 		= Agent::where('is_acrchived', '=', 0);  
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.agents.active',compact(['lists', 'totalData']));  
	}
	public function inactive(Request $request)
	{
		$query 		= Agent::where('is_acrchived', '=', 1); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.agents.inactive',compact(['lists', 'totalData']));  
	}
	
	public function create(Request $request){
		return view('Admin.agents.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [										
										'email' => 'required|email',
										'related_office' => 'required'
									  ]);
			
			$requestData 		= 	$request->all();
			 
			$obj				= 	new Agent; 
			$obj->agent_type	=	implode(',',@$requestData['agent_type']);
			$obj->struture	=	@$requestData['struture'];
			if(@$requestData['struture'] == 'Individual'){
			$obj->full_name	=	@$requestData['full_name'];
			}else{
				$obj->full_name	=	@$requestData['c_name'];
				$obj->business_name	=	@$requestData['business_name'];
				$obj->tax_number	=	@$requestData['tax_number']; 
		    	$obj->contract_expiry_date	=	@$requestData['contract_expiry_date'];
			}
		
			$obj->email	=	@$requestData['email'];
			$obj->country_code	=	@$requestData['country_code'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->related_office	=	@$requestData['related_office'];
			$obj->income_sharing	=	@$requestData['income_sharing'];
			$obj->claim_revenue	=	@$requestData['claim_revenue'];
			
			/* Profile Image Upload Function Start */						  
				if($request->hasfile('profile_img')) 
				{	
					$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
				}
				else
				{
					$profile_img = NULL;
				}		 
			/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/agents/active')->with('success', 'Agents Added Successfully');
			}				
		}	

		return view('Admin.agents.create');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'email' => 'required|email',
										'related_office' => 'required'
									  ]);
								  					  
			$obj				= 	Agent::find(@$requestData['id']);
						
			$obj->agent_type	=	implode(',',@$requestData['agent_type']);
			$obj->struture	=	@$requestData['struture'];
			if(@$requestData['struture'] == 'Individual'){
			$obj->full_name	=	@$requestData['full_name'];
			}else{
				$obj->full_name	=	@$requestData['c_name'];
				$obj->business_name	=	@$requestData['business_name'];
			$obj->tax_number	=	@$requestData['tax_number']; 
			$obj->contract_expiry_date	=	@$requestData['contract_expiry_date'];
			}
			
			$obj->email	=	@$requestData['email'];
			$obj->country_code	=	@$requestData['country_code'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->related_office	=	@$requestData['related_office'];
			$obj->income_sharing	=	@$requestData['income_sharing'];
			$obj->claim_revenue	=	@$requestData['claim_revenue'];
			
			/* Profile Image Upload Function Start */						  
			if($request->hasfile('profile_img')) 
			{	
				/* Unlink File Function Start */ 
					if($requestData['profile_img'] != '')
						{
							$this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
						}
				/* Unlink File Function End */
				
				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}		
		/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/agents/active')->with('success', 'Agents Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Agent::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Agent::find($id);
					return view('Admin.agents.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/agents/active')->with('error', 'Agents Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/agents/active')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	/* public function show(Request $request, $id = NULL){
		if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	
				if(User::where('id', '=', $id)->exists()) 
				{
					$fetchedData = User::where('id',$id)->first();
					
					return view('Admin.agents.show', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/agents/index')->with('error', 'Agent Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/agents/index')->with('error', Config::get('constants.unauthorized'));
			}
	} */ 
 
	public function detail(Request $request, $id = NULL){
		if(isset($id) && !empty($id)) { 
			$id = $this->decodeString($id);	
			if(Agent::where('id', '=', $id)->exists()) 
			{
				$fetchedData = Agent::find($id);
				return view('Admin.agents.detail', compact(['fetchedData']));
			}
			else 
			{
				return Redirect::to('/admin/agents/active')->with('error', 'Agents Not Exist');
			}	
		}
		else
		{
			return Redirect::to('/admin/agents/active')->with('error', Config::get('constants.unauthorized'));
		}
	}
	
	public function savepartner(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			 
			$obj				= 	new RepresentingPartner; 
			$obj->partner_id	=	@$requestData['represent_partner'];			
			$obj->agent_id	=	@$requestData['client_id'];			
						
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/agent/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Partner Added Successfully');
			}				
		}	 
	}
	
	
	public function businessimport(Request $request){
		if ($request->isMethod('post')) 
		{
			
			 Excel::import(new ImportUser, 
                      $request->file('uploadfile')->store('files'), $request);
			return redirect()->back()->with('success', 'Agents Imported successfully');
		}else{
			return view('Admin.agents.importbusiness');
		}
	}
	
	public function individualimport(Request $request){
		return view('Admin.agents.importindividual');
	}
}
