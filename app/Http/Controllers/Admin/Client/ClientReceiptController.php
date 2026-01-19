<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client receipts, invoices, and commission reports
 * 
 * Methods to move from ClientsController:
 * - saveaccountreport
 * - clientreceiptlist
 * - getTopReceiptValInDB
 * - printpreview
 * - getClientReceiptInfoById
 * - validate_receipt
 * - commissionreport
 * - getcommissionreport
 */
class ClientReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // Move methods here
}
