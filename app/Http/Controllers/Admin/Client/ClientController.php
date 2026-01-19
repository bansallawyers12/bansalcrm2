<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Traits\ClientHelpers;
use App\Traits\ClientQueries;
use App\Traits\ClientAuthorization;
use App\Services\SearchService;
use Illuminate\Support\Facades\DB;
use Auth;
use Config;

/**
 * Core client CRUD and listing operations
 * 
 * Methods to move from ClientsController:
 * - index
 * - archived
 * - create
 * - store
 * - edit
 * - clientdetail
 * - leaddetail
 * - updateclientstatus
 * - changetype
 * - change_assignee
 * - removetag
 * - save_tag
 * - getallclients
 * - getrecipients
 * - getonlyclientrecipients
 * - address_auto_populate
 * - updatesessioncompleted
 */
class ClientController extends Controller
{
    use ClientHelpers, ClientQueries, ClientAuthorization;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
	{
		// Check authorization using trait
		if (!$this->hasModuleAccess('20')) {
			// Return empty result set for users without module access
			$lists = $this->getEmptyClientQuery()->paginate(20);
			$totalData = 0;
			return view($this->getClientViewPath('clients.index'), compact(['lists', 'totalData']));
		}
		
		// Get base query with automatic agent filtering
		$query = $this->getBaseClientQuery();
		$totalData = $query->count();
		
		// Apply filters using trait method
		$query = $this->applyClientFilters($query, $request);
		
		// Paginate results
		$lists = $query->sortable(['id' => 'desc'])->paginate(20);
		
		// Return appropriate view based on context
		return view($this->getClientViewPath('clients.index'), compact(['lists', 'totalData']));
	}

	public function archived(Request $request)
	{
		// Get archived clients query with automatic agent filtering
		$query = $this->getArchivedClientQuery();
		$totalData = $query->count();
		
		// Paginate results
		$lists = $query->sortable(['id' => 'desc'])->paginate(20);
		
		// Return appropriate view based on context
		return view($this->getClientViewPath('archived.index'), compact(['lists', 'totalData']));
	}

	public function create(Request $request)
	{
		// REMOVED: Direct client creation is disabled
		// Clients must be created by converting leads first
		return redirect()->route('clients.index')
			->with('error', 'Direct client creation is disabled. Please create a lead first and then convert it to a client.');
	}

	public function store(Request $request)
	{
		// REMOVED: Direct client creation is disabled
		// Clients must be created by converting leads first
		return redirect()->route('clients.index')
			->with('error', 'Direct client creation is disabled. Please create a lead first and then convert it to a client.');
	}

	public function updateclientstatus(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$client = Admin::where('role', '=', '7')->where('id', $request->id)->first();

			$obj = Admin::find($request->id);
			$obj->rating = $request->rating;
			$saved = $obj->save();
			if($saved){
				if($client->rating == ''){
					$subject = 'has rated Client as '.$request->rating;
				}else{
					$subject = 'has changed Client\'s rating from '.$client->rating.' to '.$request->rating;
				}
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = Auth::user()->id;
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You\'ve successfully updated your client\'s information.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function getallclients(Request $request){
		// Validate input
		$validated = $request->validate([
			'q' => 'required|string|min:2|max:100',
		]);

		$query = $validated['q'];

		// Use SearchService for optimized search
		$searchService = new SearchService($query, 50, true);
		$results = $searchService->search();

		return response()->json($results);
	}

	public function getrecipients(Request $request){
		$squery = $request->q ?? '';
		if($squery != ''){
			try {
				$clients = Admin::where('is_archived', '=', 0)
					->where('role', '=', 7)
					->where(function($query) use ($squery) {
						$query->where('email', 'ilike', '%'.$squery.'%')
							->orWhere('first_name', 'ilike', '%'.$squery.'%')
							->orWhere('last_name', 'ilike', '%'.$squery.'%')
							->orWhere('client_id', 'ilike', '%'.$squery.'%')
							->orWhere('phone', 'ilike', '%'.$squery.'%')
							->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'ilike', '%'.$squery.'%');
					})
					->get();

				$items = array();
				foreach($clients as $clint){
					$fullName = trim(($clint->first_name ?? '') . ' ' . ($clint->last_name ?? ''));
					$items[] = array(
						'id' => $clint->id,
						'text' => $fullName, // Required by Select2
						'name' => $fullName,
						'email' => $clint->email ?? '',
						'status' => $clint->type ?? 'Client',
						'cid' => base64_encode(convert_uuencode($clint->id))
					);
				}

				return response()->json(array('items'=>$items));
			} catch (\Exception $e) {
				\Log::error('getrecipients error: ' . $e->getMessage());
				return response()->json(array('items'=>array(), 'error' => $e->getMessage()));
			}
		} else {
			return response()->json(array('items'=>array()));
		}
	}

	public function getonlyclientrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			$clients = Admin::where('is_archived', '=', 0)
				->where('role', '=', 7)
				->where(function($query) use ($squery) {
					return $query
						->where('email', 'ilike', '%'.$squery.'%')
						->orwhere('first_name', 'ilike','%'.$squery.'%')
						->orwhere('last_name', 'ilike','%'.$squery.'%')
						->orwhere('client_id', 'ilike','%'.$squery.'%')
						->orwhere('phone', 'ilike','%'.$squery.'%')
						->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'ilike', "%".$squery."%");
				})
				->get();

			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$clint->type,'id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}

			$litems = array();
			$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}

	public function save_tag(Request $request){
		$id = $request->client_id;

		if(Admin::where('id',$id)->exists()){
			$rawTags = $request->input('tagname', '');
			$obj = Admin::find($id);
			$obj->tagname = $this->normalizeTags($rawTags);
			$saved = $obj->save();
			if($saved){
				return redirect()->route('clients.detail', base64_encode(convert_uuencode(@$id)))->with('success', 'Tags addes successfully');
			}else{
				return redirect()->route('clients.detail', base64_encode(convert_uuencode(@$id)))->with('error', 'Please try again');
			}
		}else{
			return redirect()->route('clients.index')->with('error', Config::get('constants.unauthorized'));
		}
	}

	public function change_assignee(Request $request){
		$objs = Admin::find($request->id);
		if ( is_array($request->assinee) ) {
			$assineeCount = count($request->assinee);
			if( $assineeCount < 1){
				$objs->assignee = "";
			} else if( $assineeCount == 1){
				$objs->assignee = $request->assinee[0];
			} else if( $assineeCount > 1){
				$objs->assignee = implode(",",$request->assinee);
			}
		}
		$saved = $objs->save();
		if($saved){
			if ( is_array($request->assinee) && count($request->assinee) >=1) {
				$assigneeArr = $request->assinee;
				foreach($assigneeArr as $key=>$val) {
					$o = new \App\Models\Notification;
					$o->sender_id = Auth::user()->id;
					$o->receiver_id = $val;
					$o->module_id = $request->id;
					$o->url = route('clients.detail', base64_encode(convert_uuencode(@$request->id)));
					$o->notification_type = 'client';
					$o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
					$o->seen = 0;
					$o->save();
				}
			}
			$response['status'] 	= 	true;
			$response['message']	=	'Updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function removetag(Request $request){
		$objs = Admin::find($request->c);
		$itag = $request->rem_id;

		if($objs->tagname != ''){
			$rs = explode(',', $objs->tagname);
			unset($rs[$itag]);
			$objs->tagname = 	implode(',',@$rs);
			$objs->save();
		}
		return redirect()->route('clients.detail', ['id' => base64_encode(convert_uuencode(@$objs->id))])->with('success', 'Record Updated successfully');
	}

    /**
     * Auto-populate address using geocoding (currently disabled)
     */
    public function address_auto_populate(Request $request){
        $address = $request->address;
        
        // Geocoding disabled - returning empty response
        $response['status'] 	= 	0;
        $response['postal_code'] = 	"";
        $response['locality']    = 	"";
        $response['message']	=	"Geocoding feature disabled.";
        echo json_encode($response);
        
        /*
        if( isset($address) && $address != ""){
            $result = app('geocoder')->geocode($address)->get(); //dd($result[0]);
            $postalCode = $result[0]->getPostalCode();
            $locality = $result[0]->getLocality();
            if( !empty($result) ){
                $response['status'] 	= 	1;
                $response['postal_code'] = 	$postalCode;
                $response['locality'] 	= 	$locality;
                $response['message']	=	"address is success.";
            } else {
                $response['status'] 	= 	0;
                $response['postal_code'] = 	"";
                $response['locality']    = 	"";
                $response['message']	=	"address is wrong.";
            }
            echo json_encode($response);
        }
        */
    }

    /**
     * Change client type (client/lead)
     */
    public function changetype(Request $request,$id = Null, $slug = Null){
        if(isset($id) && !empty($id))
        {
            $id = $this->decodeString($id);
            if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
            {
                $obj = Admin::find($id);
                $obj->type = $slug;
                $saved = $obj->save();

                return redirect()->route('clients.detail', ['id' => base64_encode(convert_uuencode(@$id))])->with('success', 'Record Updated successfully');
            }
            else
            {
                return redirect()->route('clients.index')->with('error', 'Clients Not Exist');
            }
        }
        else
        {
            return redirect()->route('clients.index')->with('error', Config::get('constants.unauthorized'));
        }
    }
}
