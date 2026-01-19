<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client followup management
 * 
 * Methods to move from ClientsController:
 * - followupstore
 * - followupstore_application
 * - reassignfollowupstore
 * - updatefollowup
 * - retagfollowup
 * - personalfollowup
 * - updatefollowupschedule
 */
class ClientFollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
