<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use App\BlogCategory;

use Auth;
use Config;

class BlogCategoryController extends Controller {
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
     * All Cms Page.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			 /* $check = $this->checkAuthorizationAction('cmspages', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	 
		//check authorization end
		
		$query 		= BlogCategory::where('id', '!=', '')->with(['parent']);
		
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term')) 
		{
			$search_term 		= 	$request->input('search_term');
			if(trim($search_term) != '')
			{		
				$query->where('title', 'LIKE', '%' . $search_term . '%');
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

		if ($request->has('search_term') || $request->has('search_term_from') || $request->has('search_term_to')) 
		{
			$totalData 	= $query->count();//after search
		}
		
		$lists		= $query->orderby('id','DESC')->get();
		
		return view('Admin.blogcategory.index',compact(['lists', 'totalData']));	
	}
	
	public function create(Request $request)
	{ 
		$categories = BlogCategory::where('parent_id', null)->orderby('name', 'asc')->get();	
		return view('Admin.blogcategory.create', compact('categories'));	
	}
	
	public function store(Request $request)
	{
		 /* $check = $this->checkAuthorizationAction('cmspages', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
					'name' => 'required|max:255',
					'slug' => 'required|max:255 | unique:blog_categories',
					 'parent_id' => 'nullable|numeric'
			]);
			$requestData 		= 	$request->all();
			
			$obj				= 	new BlogCategory;
			$obj->name			=	@$requestData['name'];
			$obj->status		=	@$requestData['status'];
			$obj->parent_id		=	@$requestData['parent_id'];
			$obj->slug			=	@$requestData['slug'];
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/blogcategories')->with('success', 'Blog Category added Successfully');
			}
		}			
	}
	
	public function edit(Request $request, $id = NULL)
	{	
		//check authorization start	
			/*  $check = $this->checkAuthorizationAction('cmspages', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
	
		if ($request->isMethod('post'))  {
			$requestData 		= 	$request->all();
						
			$this->validate($request, [
										'name' => 'required|max:255',
										'slug' => 'required|max:255|unique:blog_categories,slug,'.@$requestData['id'],
										
									  ]);
			$obj				= 	BlogCategory::find(@$requestData['id']);
			$obj->name			=	@$requestData['name'];
			$obj->status		=	@$requestData['status'];
			$obj->parent_id		=	@$requestData['parent_id'];
			$obj->slug			=	@$requestData['slug']; 
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/blogcategories')->with('success', 'Blog Category'.Config::get('constants.edited'));
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(BlogCategory::where('id', '=', $id)->exists()) 
				{
					$fetchedData = BlogCategory::find($id);
					$categories = BlogCategory::where('parent_id', null)->orderby('name', 'asc')->get();	
				return view('Admin.blogcategory.edit', compact(['fetchedData', 'categories']));
				}
				else
				{
					return Redirect::to('/admin/blogcategories')->with('error', 'Blog Category'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/blogcategories')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
	
}
