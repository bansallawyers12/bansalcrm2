<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Category; 
  
use Auth; 
use Config;

class MasterCategoryController extends Controller
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
	
		$query 		= Category::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.mastercategory.index',compact(['lists', 'totalData'])); 	
			 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.mastercategory.create');	
	}
	 
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'category_name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Category; 
			$obj->category_name	=	@$requestData['category_name'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/master-category')->with('success', 'Master Category Added Successfully');
			}				
		}	

		return view('Admin.feature.mastercategory.index');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'category_name' => 'required|max:255'
									  ]);
								  					  
			$obj			= 	Category::find(@$requestData['id']);
						
			$obj->category_name	=	@$requestData['category_name'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/master-category')->with('success', 'Master Category Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Category::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Category::find($id);
					return view('Admin.feature.mastercategory.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/master-category')->with('error', 'Master Category Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/master-category')->with('error', Config::get('constants.unauthorized'));
			}		 
		} 	
		
	}
}
