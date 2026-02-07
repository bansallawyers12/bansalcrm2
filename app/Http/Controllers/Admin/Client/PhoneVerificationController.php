<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientPhone;
use App\Services\Sms\PhoneVerificationService;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    protected $verificationService;

    public function __construct(PhoneVerificationService $verificationService)
    {
        $this->middleware('auth:admin');
        $this->verificationService = $verificationService;
    }

    public function sendOTP(Request $request)
    {
        $request->validate(['client_phone_id' => 'required|exists:client_phones,id']);
        return response()->json($this->verificationService->sendOTP($request->client_phone_id));
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'client_phone_id' => 'required|exists:client_phones,id',
            'otp_code' => 'required|string|size:6',
        ]);
        return response()->json($this->verificationService->verifyOTP($request->client_phone_id, $request->otp_code));
    }

    public function resendOTP(Request $request)
    {
        $request->validate(['client_phone_id' => 'required|exists:client_phones,id']);
        if (!$this->verificationService->canResendOTP($request->client_phone_id)) {
            return response()->json(['success' => false, 'message' => 'Please wait 30 seconds before resending.']);
        }
        return response()->json($this->verificationService->sendOTP($request->client_phone_id));
    }

    public function getStatus($clientPhoneId)
    {
        $clientPhone = ClientPhone::find($clientPhoneId);
        if (!$clientPhone) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json([
            'success' => true,
            'is_verified' => (bool) $clientPhone->is_verified,
            'verified_at' => $clientPhone->verified_at?->toIso8601String(),
            'needs_verification' => $clientPhone->needsVerification(),
        ]);
    }

    public function sendOTPForLead(Request $request)
    {
        $request->validate(['lead_id' => 'required|exists:leads,id']);
        return response()->json($this->verificationService->sendOTPForLead($request->lead_id));
    }

    public function verifyOTPForLead(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'otp_code' => 'required|string|size:6',
        ]);
        return response()->json($this->verificationService->verifyOTPForLead($request->lead_id, $request->otp_code));
    }

    public function resendOTPForLead(Request $request)
    {
        $request->validate(['lead_id' => 'required|exists:leads,id']);
        if (!$this->verificationService->canResendOTPForLead($request->lead_id)) {
            return response()->json(['success' => false, 'message' => 'Please wait 30 seconds before resending.']);
        }
        return response()->json($this->verificationService->sendOTPForLead($request->lead_id));
    }

    public function getStatusForLead($leadId)
    {
        $lead = \App\Models\Lead::find($leadId);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json([
            'success' => true,
            'is_verified' => (bool) $lead->is_verified,
            'verified_at' => $lead->verified_at?->toIso8601String(),
            'needs_verification' => $lead->needsVerification(),
        ]);
    }
}
