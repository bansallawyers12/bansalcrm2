<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Traits\ClientHelpers;
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
    use ClientHelpers;

    public function __construct()
    {
        $this->middleware('auth:admin');
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
