<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use App\Models\Blog;
use App\Models\BlogCategory;

use Auth;
use Config;

class BlogController extends Controller {
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
		
		$query 		= Blog::where('id', '!=', '')->with(['categorydetail']);
		
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
		
		return view('Admin.blog.index',compact(['lists', 'totalData']));	
	}
	
	public function create(Request $request)
	{ //dd(phpinfo());
		$categories = BlogCategory::where('parent_id', null)->orderby('name', 'asc')->get();		
		return view('Admin.blog.create', compact('categories'));	
	}
	
	function getYoutubeEmbedUrl($url){
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        return 'https://www.youtube.com/embed/' . $youtube_id ;
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
					'title' => 'required|max:255',
					'slug' => 'required|max:255',
					'description' => 'required'
			]);
			$requestData 		= 	$request->all();
			
			if($request->hasfile('image')) 
			{
				$blog_image = $this->uploadFile($request->file('image'), Config::get('constants.blog')); 
			}
			else
			{ 
				$blog_image = NULL;
			}
			
			$obj				= 	new Blog;
			$obj->title			=	@$requestData['title'];
			$obj->description			=	@$requestData['description'];
			$obj->short_description			=	@$requestData['short_description'];
			$obj->status		=	@$requestData['status'];
			$obj->parent_category		=	@$requestData['parent_category'];
			$obj->image			=	@$blog_image;
			$obj->slug			=	@$requestData['slug'];
			
			$obj->meta_title			=	@$requestData['meta_title'];
			$obj->meta_description		=	@$requestData['meta_description'];
			$obj->meta_keyword			=	@$requestData['meta_keyword'];

            if($request->has('youtube_url'))
			{
				$youtube_url = @$requestData['youtube_url']; //'https://www.youtube.com/watch?v=oVT78QcRQtU';
                if($youtube_url != ""){
                    $embed_url = $this->getYoutubeEmbedUrl($youtube_url);
                } else {
                    $embed_url = NULL;
                }
            } else {
				$embed_url = NULL;
			} //dd($embed_url);
            $obj->youtube_url			=  $embed_url;
            
            if($request->hasfile('pdf_doc')) 
			{
				$pdf_doc = $this->uploadFile($request->file('pdf_doc'), Config::get('constants.blog')); 
			}
			else
			{ 
				$pdf_doc = NULL;
			}
            $obj->pdf_doc			=	@$pdf_doc;
            	
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/blog')->with('success', 'Blog added Successfully');
			}
		}			
	}
	
	public function edit(Request $request, $id = NULL)
	{	
	    //echo phpinfo();
	    //dd();
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
				'title' => 'required|max:255',
				'slug' => 'required|max:255|unique:blogs,slug,'.@$requestData['id'],
				'description' => 'required'
			]);
									  
			if($request->hasfile('image'))  {	
				/* Unlink File Function Start */ 
				if($requestData['image'] != '')
				{
					$this->unlinkFile($requestData['old_image'], Config::get('constants.blog'));
				}
				/* Unlink File Function End */
				
				$blog_image = $this->uploadFile($request->file('image'), Config::get('constants.blog'));
			}
			else
			{
				$blog_image = @$requestData['old_image'];
			}
			
			$obj				= 	Blog::find(@$requestData['id']);
			$obj->title			=	@$requestData['title'];
			$obj->description		=	@$requestData['description'];
			$obj->short_description		=	@$requestData['short_description'];
			$obj->parent_category		=	@$requestData['parent_category'];
			$obj->image			=	@$blog_image;
			$obj->status		=	@$requestData['status'];
			$obj->slug			=	@$requestData['slug'];
			
			$obj->meta_title			=	@$requestData['meta_title'];
			$obj->meta_description		=	@$requestData['meta_description'];
			$obj->meta_keyword			=	@$requestData['meta_keyword'];

            if($request->has('youtube_url'))
			{
				$youtube_url = @$requestData['youtube_url']; //'https://www.youtube.com/watch?v=oVT78QcRQtU';
                if($youtube_url != ""){
                    $embed_url = $this->getYoutubeEmbedUrl($youtube_url);
                } else {
                    $embed_url = NULL;
                }
            } else {
				$embed_url = NULL;
			} //dd($embed_url);
            $obj->youtube_url	=  $embed_url;
            
            
            if($request->hasfile('pdf_doc'))  {	
				/* Unlink File Function Start */ 
				if($requestData['pdf_doc'] != '')
				{
					$this->unlinkFile($requestData['old_pdf'], Config::get('constants.blog'));
				}
				/* Unlink File Function End */
				
				$pdf_doc = $this->uploadFile($request->file('pdf_doc'), Config::get('constants.blog'));
			}
			else
			{
				$pdf_doc = @$requestData['old_pdf'];
			}
            $obj->pdf_doc			=	@$pdf_doc;
            
			$saved				=	$obj->save();
			 
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/blog')->with('success', 'Blog'.Config::get('constants.edited'));
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(Blog::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Blog::find($id);
					$categories = BlogCategory::where('parent_id', null)->orderby('name', 'asc')->get();	
					return view('Admin.blog.edit', compact(['fetchedData', 'categories']));
				}
				else
				{
					return Redirect::to('/admin/blog')->with('error', 'Blog'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/blog')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
	
}
