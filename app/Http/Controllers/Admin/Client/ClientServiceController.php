<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client interested services and service taken
 * 
 * Methods to move from ClientsController:
 * - interestedService
 * - editinterestedService
 * - getServices
 * - getintrestedservice
 * - getintrestedserviceedit
 * - createservicetaken
 * - removeservicetaken
 * - getservicetaken
 * - gettagdata
 */
class ClientServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
