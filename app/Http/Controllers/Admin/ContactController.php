<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Contact;
 
use Auth;  
use Config;

class ContactController extends Controller
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
			/*  $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
		
		  if(Auth::user()->role == 1){
			$query 		= Contact::query(); 
		 }else{	
			$query 		= Contact::where('user_id', '=', Auth::user()->id);
		 }	
		if ($request->has('name'))
		{
			$name = $request->input('name');
			if (trim($name) != '')
			{
				$query->where('name', 'like', '%' . $name . '%');
			}
		}
		if ($request->has('email'))
		{
			$email = $request->input('email');
			if (trim($email) != '')
			{
				$query->where('contact_email', 'like', '%' . $email . '%');
			}
		}
		if ($request->has('phone'))
		{
			$phone = $request->input('phone');
			if (trim($phone) != '')
			{
				$query->where('contact_phone', 'like', '%' . $phone . '%');
			}
		}			
		
		//$query 		= Contact::where('id','!=','' );
		
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit')); 
		
		return view('Admin.managecontact.index',compact(['lists', 'totalData'])); 
		
		//return view('Admin.managecontact.index'); 	
		
	}
	
	public function create(Request $request) 
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
		
		$managecontact 		=  Managecontact::all();	 */	
		return view('Admin.managecontact.create');
	}
	
	 public function add(Request $request){
		 $this->validate($request, [
			'name' => 'required|max:255',
			'contact_email' => 'required|unique:contacts',
			'contact_phone' => 'required'
		  ]);
		  $requestData = $request->all();

			$obj = new Contact;
			$obj->user_id = Auth::user()->id;
			$obj->name = @$requestData['name'];
			$obj->contact_email = @$requestData['contact_email'];
			$obj->contact_phone = @$requestData['contact_phone'];
			$obj->department = @$requestData['department'];

			$saved = $obj->save();

			if(!$saved)
			{
				return json_encode(array('success' => false, 'message' => Config::get('constants.server_error')));
			}
			else
			{
				return json_encode(array('success' => true, 'contactdetail' => $obj));
			}
	 }
	 public function store(Request $request)
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
				'name' => 'required|max:255',
				'contact_email' => 'required|unique:contacts',
				'contact_phone' => 'required'
			]);

			$requestData = $request->all();

			$obj = new Contact;
			$obj->user_id = Auth::user()->id;
			$obj->name = @$requestData['name'];
			$obj->contact_email = @$requestData['contact_email'];
			$obj->contact_phone = @$requestData['contact_phone'];
			$obj->department = @$requestData['department'];

			$saved = $obj->save();
			
			if(!$saved) 
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{ 
				return redirect()->route('managecontact.index')->with('success', 'Contacts added Successfully');
			} 				
		}	 
	} 
	
	 public function storeaddress(Request $request){
		 if ($request->isMethod('post')) 
		 {
			$requestData = $request->all();
			$obj = Contact::find($requestData['customer_id']);
			if (!$obj) {
				return json_encode(array('success' => false, 'message' => 'Contact not found'));
			}
			// Address fields (country, address, city, zipcode, phone) are not stored in contacts table
			return json_encode(array('success' => true, 'contactdetail' => $obj));
		 }
	 }
	 public function edit(Request $request, $id = NULL)
	{			
		//check authorization end
	//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData = $request->all();

			$this->validate($request, [
				'name' => 'required|max:255',
				'contact_email' => 'required|unique:contacts,contact_email,' . $requestData['id'],
				'contact_phone' => 'required'
			]);

			$obj = Contact::find($requestData['id']);
			$obj->name = @$requestData['name'];
			$obj->contact_email = @$requestData['contact_email'];
			$obj->contact_phone = @$requestData['contact_phone'];
			$obj->department = @$requestData['department'];

			$saved = $obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('managecontact.index')->with('success', 'Contact Edited Successfully');
			}				
		}
		else
		{	 
			if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	 
				if(Contact::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Contact::find($id);
					return view('Admin.managecontact.edit', compact(['fetchedData']));
				}
				else
				{
					return redirect()->route('managecontact.index')->with('error', 'Contact Not Exist');
				}	
			}
			else
			{
				return redirect()->route('managecontact.index')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	} 
	
	
}
