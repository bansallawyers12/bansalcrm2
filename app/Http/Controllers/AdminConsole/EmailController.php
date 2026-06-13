<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use App\Models\Admin;
use App\Models\FromEmail;
use App\Services\SesSenderService;
use App\Support\FromEmailAddress;

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
			$query 		= FromEmail::query();
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
			try {
				$email = $this->resolveFromEmailInput($request);
			} catch (ValidationException $e) {
				return redirect()->back()->withErrors($e->errors())->withInput();
			}

			$this->validate($request, [
										'password' => 'required|string|min:1',
										'users' => 'required|array|min:1',
										'users.*' => 'required'
									  ], [
										'password.required' => 'Password is required.',
										'users.required' => 'Please select at least one user for User Sharing.',
										'users.min' => 'Please select at least one user for User Sharing.'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new FromEmail; 
			$obj->email	=	$email;
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	$requestData['password'];
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

			try {
				$email = $this->resolveFromEmailInput($request, (int) ($requestData['id'] ?? 0));
			} catch (ValidationException $e) {
				return redirect()->back()->withErrors($e->errors())->withInput();
			}
			
			$this->validate($request, [										
										'users' => 'required|array|min:1',
										'users.*' => 'required'
									  ], [
										'users.required' => 'Please select at least one user for User Sharing.',
										'users.min' => 'Please select at least one user for User Sharing.'
									  ]);
								  					  
			$obj			= 	FromEmail::find(@$requestData['id']);
			$obj->email	=	$email;
			$obj->display_name	=	@$requestData['display_name'];
			// Only update password when a new value is provided (avoids overwriting with empty on edit)
			if (!empty(trim($requestData['password'] ?? ''))) {
				$obj->password = $requestData['password'];
			}
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
				if(FromEmail::where('id', '=', $id)->exists()) 
				{
					$fetchedData = FromEmail::find($id);
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

	/**
	 * Build full from_emails address from email_name + email_domain radio.
	 *
	 * @throws ValidationException
	 */
	private function resolveFromEmailInput(Request $request, ?int $ignoreId = null): string
	{
		$this->validate($request, [
			'email_name' => ['required', 'string', 'max:64', 'not_regex:/@/', 'regex:/^[a-zA-Z0-9._%+-]+$/'],
			'email_domain' => ['required', Rule::in(FromEmailAddress::domains())],
		], [
			'email_name.required' => 'Email name is required.',
			'email_name.not_regex' => 'Email name cannot contain @. Enter only the part before @.',
			'email_name.regex' => 'Email name may only contain letters, numbers, and . _ % + - (no @).',
			'email_domain.required' => 'Please select an email domain.',
			'email_domain.in' => 'Please select a valid email domain.',
		]);

		$email = FromEmailAddress::compose(
			FromEmailAddress::normalizeLocal((string) $request->input('email_name')),
			(string) $request->input('email_domain')
		);

		if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw ValidationException::withMessages([
				'email_name' => 'Could not build a valid email address from the name and domain.',
			]);
		}

		if (! app(SesSenderService::class)->isAllowedSenderDomain($email)) {
			throw ValidationException::withMessages([
				'email_domain' => 'Use @bansaleducation.com.au, @educationelite.com.au, or @bansalimmigration.com.au.',
			]);
		}

		$this->assertFromEmailUnique($email, $ignoreId);

		return $email;
	}

	/**
	 * Case-insensitive duplicate check (create + edit).
	 *
	 * @throws ValidationException
	 */
	private function assertFromEmailUnique(string $email, ?int $ignoreId = null): void
	{
		$normalized = strtolower(trim($email));
		$query = FromEmail::whereRaw('LOWER(TRIM(email)) = ?', [$normalized]);
		if ($ignoreId) {
			$query->where('id', '!=', $ignoreId);
		}

		if ($query->exists()) {
			throw ValidationException::withMessages([
				'email_name' => 'This email address already exists.',
				'email' => 'This email address already exists.',
			]);
		}
	}
}
