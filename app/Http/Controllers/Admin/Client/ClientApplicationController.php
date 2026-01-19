<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client application lifecycle
 * 
 * Methods to move from ClientsController:
 * - saveapplication
 * - getapplicationlists
 * - convertapplication
 * - savetoapplication
 * - deleteservices
 * - saleforcastservice
 */
class ClientApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
