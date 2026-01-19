<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client appointment scheduling
 * 
 * Methods to move from ClientsController:
 * - addAppointment
 * - editappointment
 * - updateappointmentstatus
 * - getAppointments
 * - getAppointmentdetail
 * - deleteappointment
 */
class ClientAppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function addAppointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
    }

	public function editappointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
	}
  
	public function updateappointmentstatus(Request $request, $status = Null, $id = Null){
		return redirect()->back()->with('error', 'Appointment functionality has been removed');
	}
	
	public function getAppointments(Request $request){
		return response('<div class="row"><div class="col-md-12"><p class="text-muted">Appointment functionality has been removed.</p></div></div>', 200);
	}

	public function getAppointmentdetail(Request $request){
		return response('Appointment functionality has been removed', 404);
	}

	public function deleteappointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
	}
}
