<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client notes management
 * 
 * Methods to move from ClientsController:
 * - createnote
 * - getnotedetail
 * - viewnotedetail
 * - viewapplicationnote
 * - getnotes
 * - deletenote
 * - pinnote
 */
class ClientNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
