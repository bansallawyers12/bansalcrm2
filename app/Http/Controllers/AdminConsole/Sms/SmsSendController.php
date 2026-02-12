<?php

namespace App\Http\Controllers\AdminConsole\Sms;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Application;
use App\Services\Sms\UnifiedSmsManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * SmsSendController
 *
 * Handles manual SMS sending and bulk operations for AdminConsole
 */
class SmsSendController extends Controller
{
    protected $smsManager;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->middleware('auth:admin');
        $this->smsManager = $smsManager;
    }

    /**
     * Show manual SMS send form
     */
    public function create(Request $request)
    {
        return view('AdminConsole.features.sms.send.create');
    }

    /**
     * Send manual SMS (API endpoint - used in client detail and send form)
     * Replaces {Student_Name} and {Date} placeholders when client_id/application_id provided
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'message' => 'required|string|max:1600',
            'client_id' => 'nullable|exists:admins,id',
            'application_id' => 'nullable|integer|exists:applications,id',
            'contact_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = $request->message;

        // Replace template placeholders when client_id is present
        $hasPlaceholders = str_contains($message, '{Student_Name}') || str_contains($message, '{student_name}')
            || str_contains($message, '{Date}') || str_contains($message, '{date}');
        if ($request->client_id && $hasPlaceholders) {
            $client = Admin::find($request->client_id);
            $studentName = $client ? (trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: 'Client') : 'Client';
            $checklistDate = Carbon::now()->format('d/m/Y');
            if ($request->application_id) {
                $app = Application::find($request->application_id);
                if ($app && $app->checklist_sent_at) {
                    $checklistDate = Carbon::parse($app->checklist_sent_at)->format('d/m/Y');
                }
            }
            $replacements = ['{Student_Name}' => $studentName, '{student_name}' => $studentName, '{Date}' => $checklistDate, '{date}' => $checklistDate];
            $message = str_replace(array_keys($replacements), array_values($replacements), $message);
        }

        $result = $this->smsManager->sendSms(
            $request->phone,
            $message,
            'manual',
            [
                'client_id' => $request->client_id,
                'contact_id' => $request->contact_id,
            ]
        );

        return response()->json($result);
    }

    /**
     * Send SMS from template (API endpoint)
     */
    public function sendFromTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'template_id' => 'required|exists:sms_templates,id',
            'variables' => 'nullable|array',
            'client_id' => 'nullable|exists:admins,id',
            'contact_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->smsManager->sendFromTemplate(
            $request->phone,
            $request->template_id,
            $request->variables ?? [],
            [
                'client_id' => $request->client_id,
                'contact_id' => $request->contact_id,
            ]
        );

        return response()->json($result);
    }

    /**
     * Send bulk SMS
     */
    public function sendBulk(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Bulk SMS feature coming soon'
        ], 501);
    }
}
