<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\SubjectArea; 
  
use Auth; 
use Config;

class SubjectAreaController extends Controller
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
	
		$query 		= SubjectArea::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.subjectarea.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.feature.producttype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.subjectarea.create');	
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
			
			$obj				= 	new SubjectArea; 
			$obj->name	=	@$requestData['name'];

			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/subjectarea')->with('success', 'Tax Added Successfully');
			}				
		}	

		return view('Admin.feature.subjectarea.create');	
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
								  					  
			$obj			= 	SubjectArea::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/subjectarea')->with('success', 'Record updated Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(SubjectArea::where('id', '=', $id)->exists()) 
				{
					$fetchedData = SubjectArea::find($id);
					return view('Admin.feature.subjectarea.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/subjectarea')->with('error', 'Record Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/subjectarea')->with('error', Config::get('constants.unauthorized'));
			}		 
		} 	
		
	}
}
