<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
