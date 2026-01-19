<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client email and SMS messaging
 * 
 * Methods to move from ClientsController:
 * - uploadmail
 * - enhanceMessage
 * - sendmsg
 * - fetchClientContactNo
 * - isgreviewmailsent
 * - updateemailverified
 * - emailVerify
 * - emailVerifyToken (public, no auth)
 * - thankyou (public, no auth)
 */
class ClientMessagingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['emailVerifyToken', 'thankyou']);
    }

    // Move methods here
}
