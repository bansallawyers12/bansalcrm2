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

    /**
     * Legacy verify modal: accept client_id + phone_number, resolve ClientPhone, send OTP.
     */
    public function sendCodeLegacy(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'phone_number' => 'required|string',
        ]);
        $clientPhone = $this->findClientPhoneByNumber($request->client_id, $request->phone_number);
        if (!$clientPhone) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is not in the client\'s contact list. Please add it first, then verify.',
            ]);
        }
        return response()->json($this->verificationService->sendOTP($clientPhone->id));
    }

    /**
     * Legacy verify modal: accept client_id + phone_number + verification_code, resolve ClientPhone, verify OTP.
     */
    public function verifyCodeLegacy(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'phone_number' => 'required|string',
            'verification_code' => 'required|string|size:6',
        ]);
        $clientPhone = $this->findClientPhoneByNumber($request->client_id, $request->phone_number);
        if (!$clientPhone) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is not in the client\'s contact list.',
            ]);
        }
        return response()->json($this->verificationService->verifyOTP($clientPhone->id, $request->verification_code));
    }

    /**
     * Find ClientPhone for client by matching raw phone number (digits-only comparison).
     */
    protected function findClientPhoneByNumber(int $clientId, string $rawNumber): ?ClientPhone
    {
        $digits = preg_replace('/\D/', '', $rawNumber);
        if ($digits === '') {
            return null;
        }
        // Australian: 0412345678 -> 61412345678 for comparison
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '61' . substr($digits, 1);
        }
        $phones = ClientPhone::where('client_id', $clientId)->get();
        foreach ($phones as $cp) {
            $stored = preg_replace('/\D/', '', ($cp->client_country_code ?? '') . ($cp->client_phone ?? ''));
            if ($stored !== '' && $stored === $digits) {
                return $cp;
            }
        }
        return null;
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
