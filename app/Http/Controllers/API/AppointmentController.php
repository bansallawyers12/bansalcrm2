<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Admin;
use App\Models\NatureOfEnquiry;
use App\Models\BookService;
use Illuminate\Support\Facades\Validator;


class AppointmentController extends Controller
{
    
  	 // List Appointments
     // https://bansalcrm.com/api/appointments
     // GET
     /* 
     Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
     Content-Type: application/json
     */
    /////////////////////////////////
    public function index(Request $request)
    {  
        $appointments = Appointment::select(
            'id', 'user_id', 'client_id', 'noe_id', 'service_id', 'full_name',
            'email', 'phone', 'date', 'time', 'timeslot_full', 'description',
            'appointment_details', 'inperson_address', 'status', 'created_at'
        )->latest()->paginate(10); // Pagination with 10 records per page

        if ($appointments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Appointments Found'
            ], 404);
        }

        $final = [];
        foreach ($appointments as $key => $appointment) {
            $final[$key]['appointment_id'] = $appointment->id;
            $final[$key]['appointment_created_at'] = date('d/m/Y H:i:s', strtotime($appointment->created_at));

            // Appointment Creator Info
            if (!empty($appointment->user_id)) {
                $creatorInfo = Admin::select('id', 'first_name', 'last_name')->where('id', $appointment->user_id)->first();
                $final[$key]['appointment_creator_id'] = $appointment->user_id;
                $final[$key]['appointment_creator_name'] = $creatorInfo ? $creatorInfo->first_name . ' ' . $creatorInfo->last_name : 'N/A';
            } else {
                $final[$key]['appointment_creator_id'] = 'N/A';
                $final[$key]['appointment_creator_name'] = 'N/A';
            }

            // Client Info
            $clientInfo = Admin::select('id', 'client_id', 'first_name', 'last_name', 'email', 'country_code', 'phone')
                ->where('id', $appointment->client_id)
                ->first();

            $final[$key]['appointment_client_uniqueid'] = $clientInfo ? $clientInfo->client_id : 'N/A';
            $final[$key]['appointment_client_name'] = $appointment->full_name ?: $clientInfo->first_name . ' ' . $clientInfo->last_name;
            $final[$key]['appointment_client_email'] = $appointment->email ?: $clientInfo->email;
            $final[$key]['appointment_client_phone'] = $appointment->phone ?: $clientInfo->country_code . $clientInfo->phone;

            // Nature of Enquiry
            $noe_names = [
                1 => 'Permanent Residency Appointment',
                2 => 'Temporary Residency Appointment',
                3 => 'JRP/Skill Assessment',
                4 => 'Tourist Visa',
                5 => 'Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)',
                6 => 'Complex matters: AAT, Protection visa, Federal Case',
                7 => 'Visa Cancellation/ NOICC/ Visa refusals',
                8 => 'INDIA/UK/CANADA/EUROPE TO AUSTRALIA'
            ];

            $final[$key]['appointment_nature_of_enquiry'] = $noe_names[$appointment->noe_id] ?? 'N/A';

            // Service Type
            $service_names = [
                1 => 'Migration Advice',
                2 => 'Migration Consultation'
            ];

            $final[$key]['appointment_service_name'] = $service_names[$appointment->service_id] ?? 'N/A';

            // Date and Time
            $final[$key]['appointment_date'] = $appointment->date ? date('d/m/Y', strtotime($appointment->date)) : 'N/A';
            $final[$key]['appointment_time'] = $appointment->time ?? 'N/A';
            $final[$key]['appointment_timeslot'] = $appointment->timeslot_full ?? 'N/A';

            // Description
            $final[$key]['appointment_description'] = $appointment->description;

            // Appointment Details
            $final[$key]['appointment_details'] = $appointment->appointment_details == "phone" ? 'Phone' : 'In Person';

            // Address
            $address_list = [
                1 => 'ADELAIDE (Unit 5 5/55 Gawler Pl, Adelaide SA 5000)',
                2 => 'MELBOURNE (Next to Flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)'
            ];

            $final[$key]['appointment_address'] = $address_list[$appointment->inperson_address] ?? 'N/A';
        }

        return response()->json([
            'success' => true,
            'data' => $final,
            'pagination' => [
                'total' => $appointments->total(),
                'per_page' => $appointments->perPage(),
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'next_page_url' => $appointments->nextPageUrl(),
                'prev_page_url' => $appointments->previousPageUrl(),
            ]
        ]);
    }
  
  
     // Store Appointment
     // https://bansalcrm.com/api/appointments
     // POST
     /* 
     Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
     Content-Type: application/json
     */
  
     /*{
        "user_id": "1",
        "noe_id": "1",
        "service_id": "2",
        "full_name": "Vipul Kumar",
        "email": "viplucmca@yahoo.co.in",
        "phone": "+919872205642",
        "date": "2025-03-05",
        "time": "13:30:00",
        "timeslot_full": "1:30 PM-1:45 PM",
        "description": "Test Enquiry",
        "appointment_details": "phone",
        "inperson_address": "1",
        "status": "0"
    }*/
    /////////////////////////////////
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noe_id' => 'required',
            'service_id' => 'required',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'timeslot_full' => 'required',
            'description' => 'required|string',
            'appointment_details' => 'required',
            'inperson_address' => 'required',
            'status' => 'required|in:0,1'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        //dd($request->all());
        $requestData = $request->all();
        try {

            $user = \App\Admin::where(function ($query) use($requestData){
                $query->where('email',$requestData['email'])
                      ->orWhere('phone',$requestData['phone']);
            })->first();
            $fullname = $requestData['full_name'];
            if( isset($fullname) && strlen($fullname) >=4 ){
                $first_name_val = trim(substr($fullname, 0, 4));
            } else {
                $first_name_val = trim($fullname);
            } //dd($first_name_val);
            if(empty($user)){
                $objs	= 	new Admin;
                $objs->client_id =	strtoupper($first_name_val).date('his');
                $objs->role	=	7;
                $objs->last_name	=	'';
                $objs->first_name	=	$fullname;
                $objs->email	=	$requestData['email'];
                $objs->phone	=	$requestData['phone'];
                $objs->save();
                $client_id = $objs->id;
                $client_unique_id = $objs->client_id;
            } else {
                if(empty($user->client_id)){
                    Admin::where('id', $user->id)->update(['client_id' => strtoupper($first_name_val).date('his')]);
                }
                $client_id = $user->id;
                $client_unique_id = $user->client_id;
            }

            $appointment_id = Appointment::insertGetId([
                'user_id' => $request->user_id,
                'client_id' => $client_id,
                'client_unique_id' => $client_unique_id,
                'noe_id' => $request->noe_id,
                'service_id' => $request->service_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date' => $request->date,
                'time' => $request->time,
                'timeslot_full' => $request->timeslot_full,
                'description' => $request->description,
                'appointment_details' => $request->appointment_details,
                'inperson_address' => $request->inperson_address,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully!',
                'appointment_id' => $appointment_id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    //Show Appointment
    //https://bansalcrm.com/api/appointments/2493
    //GET
    /* 
     Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
     Content-Type: application/json
     */
    public function show($id)
    {
        $appointment = Appointment::find($id); //dd($appointment);
        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $appointment]);
    }

    // Update Appointment
    //https://bansalcrm.com/api/appointments/2493
    //PUT
    /* 
     Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
     Content-Type: application/json
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id); //dd($appointment);
        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }
        $appointment->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully!',
            'data' => $appointment
        ]);
    }
  

    // Delete Appointment
    //https://bansalcrm.com/api/appointments/2493
    //DELETE
    /* 
     Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
     Content-Type: application/json
     */
    public function destroy($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        $appointment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully!'
        ]);
    }
  
  
     // List of nature of Enquiry
     //https://bansalcrm.com/api/natureofenquiry
     //GET
     /* 
     Content-Type: application/json
     */
    public function natureofenquiry()
    {
        $lists = NatureOfEnquiry::select('id','title')->where('status',1)->get();//dd($lists);
        $final = array();
        foreach($lists as $key=>$list){
            $final[$key]['id'] = $list->id;
            $final[$key]['title'] = $list->title;
        }
        return response()->json([
            'success' => true,
            'data' => $final
        ]);
    }


    // List of service type
    //https://bansalcrm.com/api/servicetype
     //GET
     /* 
     Content-Type: application/json
     */
    public function servicetype()
    {
        $lists = BookService::select('id','title','image','price','duration','description')->where('status',1)->get(); //dd($lists);
        $final = array();
        foreach($lists as $key=>$list){
            $final[$key]['id'] = $list->id;
            $final[$key]['title'] = $list->title;

            if( isset($list->image) &&  $list->image != ""){
                $final[$key]['image'] = "https://bansalcrm.com/public/img/service_img/".$list->image;
            } else {
                $final[$key]['image'] = 'N/A';
            }

            $final[$key]['price'] = $list->price;
            $final[$key]['duration'] = $list->duration;
            $final[$key]['description'] = $list->description;
        }
        return response()->json([
            'success' => true,
            'data' => $final
        ]);
    }
}

