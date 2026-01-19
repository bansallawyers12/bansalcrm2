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

    // Move methods here
}
