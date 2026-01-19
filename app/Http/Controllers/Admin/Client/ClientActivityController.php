<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client activity log operations
 * 
 * Methods to move from ClientsController:
 * - activities
 * - deleteactivitylog
 * - pinactivitylog
 * - notpickedcall
 */
class ClientActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
