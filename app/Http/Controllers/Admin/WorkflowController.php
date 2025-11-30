<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Workflow; 
use App\Models\WorkflowStage; 
  
use Auth; 
use Config;

class WorkflowController extends Controller
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
	
		$query 		= Workflow::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.workflow.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.feature.producttype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.workflow.create');	
	}
	 
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Workflow; 
			$obj->name	=	@$requestData['name'];
			
			$saved				=	$obj->save();  
			$stages = $requestData['stage_name'];
			
			foreach($stages as $stage){
				$o = new WorkflowStage;
				$o->w_id = $obj->id;
				$o->name = $stage;
				$save	=	$o->save();  
			}
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/workflow')->with('success', 'Visa Type Added Successfully');
			}				
		}	

		
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'name' => 'required|max:255'
									  ]);
								  					  
			$obj			= 	Workflow::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			
			$saved							=	$obj->save();
			$stages = $requestData['stage_name'];
			$remove = WorkflowStage::where('w_id' , $requestData['id'])->delete();
			foreach($stages as $stage){
				$o = new WorkflowStage;
				$o->w_id = $requestData['id'];
				$o->name = $stage;
				$save	=	$o->save();  
			}
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/workflow')->with('success', 'Visa Type Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Workflow::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Workflow::find($id);
					return view('Admin.feature.workflow.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/workflow')->with('error', 'Visa Type Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/workflow')->with('error', Config::get('constants.unauthorized'));
			}		 
		} 	
		
	}
}
