<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerifiedNumber;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showForm()
    {
        $verifiedNumbers = VerifiedNumber::where('is_verified', true)
            ->orderBy('verified_at', 'desc')
            ->get();
        return view('sms.form', compact('verifiedNumbers'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:918',
        ]);

        try {
            // Format phone number for Cellcast
            $formattedPhone = $this->formatPhoneForCellcast($request->phone);
            
            if (!$formattedPhone) {
                return back()->with('error', 'Invalid phone number format. Please use Australian phone numbers.');
            }

            $response = $this->smsService->sendSms(
                $formattedPhone,
                $request->message
            );

            if (isset($response['meta']['code']) && $response['meta']['code'] === 200) {
                return back()->with('success', 'SMS sent successfully! Message ID: ' .
                    ($response['data']['messages'][0]['message_id'] ?? 'N/A'));
            }

            return back()->with('error', 'Failed to send SMS: ' . ($response['msg'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'message' => 'required'
        ]);

        $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)
            ->where('is_verified', true)
            ->first();

        if (!$verifiedNumber) {
            return back()->with('error', 'This phone number is not verified');
        }

        // Format phone number for Cellcast
        $formattedPhone = $this->formatPhoneForCellcast($request->phone_number);
        
        if (!$formattedPhone) {
            return back()->with('error', 'Invalid phone number format');
        }

        $result = $this->smsService->sendSms(
            $formattedPhone,
            $request->message
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function checkStatus($messageId)
    {
        try {
            $response = $this->smsService->getSmsStatus($messageId);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getResponses(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $response = $this->smsService->getResponses($page);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    //Check phone is verify or not
    public function isPhoneVerifyOrNot(Request $request)
    {
        $request->validate(['phone_number' => 'required']);
        $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)
            ->where('is_verified', true)
            ->first();
            
        if (!$verifiedNumber) {
            return response()->json([
                'status' => false,
                'status_bit' => 0,
                'message' => 'Phone number is not verified.'
            ]);
        }
        
        return response()->json([
            'status' => true,
            'status_bit' => 1,
            'message' => 'Phone number is already verified'
        ]);
    }

    //If phone is not verify then send verification code
    public function sendVerificationCode(Request $request)
    {
        try {
            $request->validate(['phone_number' => 'required']);

            $phoneNumber = $request->phone_number;
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Format phone number for Cellcast (Australian format: 61XXXXXXXXX)
            $formattedPhone = $this->formatPhoneForCellcast($phoneNumber);

            if (!$formattedPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format. Please use Australian phone numbers.'
                ], 400);
            }

            // Store or update verification code
            VerifiedNumber::updateOrCreate(
                ['phone_number' => $phoneNumber],
                [
                    'verification_code' => $verificationCode,
                    'is_verified' => false,
                    'verified_at' => null
                ]
            );

            // Send verification code via Cellcast
            $result = $this->smsService->sendVerificationCode($formattedPhone, $verificationCode);

            if ($result['success']) {
                Log::info('Verification code sent', [
                    'phone' => $phoneNumber,
                    'formatted' => $formattedPhone
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send verification code'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error sending verification code', [
                'error' => $e->getMessage(),
                'phone' => $request->phone_number ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending verification code. Please try again.'
            ], 500);
        }
    }

    //verify Code
    public function verifyCode(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required',
                'verification_code' => 'required|size:6'
            ]);

            $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)
                ->where('verification_code', $request->verification_code)
                ->where('is_verified', false)
                ->first();

            if (!$verifiedNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code. Please check and try again.'
                ], 400);
            }

            $verifiedNumber->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verification_code' => null
            ]);

            Log::info('Phone verified', [
                'phone' => $request->phone_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying code', [
                'error' => $e->getMessage(),
                'phone' => $request->phone_number ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification. Please try again.'
            ], 500);
        }
    }

    /**
     * Format phone number for Cellcast API
     * Converts to Australian format: 61XXXXXXXXX (11 digits total)
     */
    protected function formatPhoneForCellcast($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-digits
        $digitsOnly = preg_replace('/[^\d]/', '', $phone);

        // Validate minimum length (Australian mobile is 9-11 digits after cleanup)
        if (strlen($digitsOnly) < 9) {
            return null;
        }

        // If already starts with 61 and followed by 4 (11 digits total)
        if (preg_match('/^614\d{8}$/', $digitsOnly)) {
            return $digitsOnly; // Already 614XXXXXXXX format (11 digits)
        }

        // If starts with 04 (Australian mobile with leading 0) - 10 digits
        // Convert 04XXXXXXXX to 61XXXXXXXX (11 digits)
        if (preg_match('/^04(\d{8})$/', $digitsOnly, $matches)) {
            return '61' . $matches[1]; // Returns 61XXXXXXXX
        }

        // If starts with 4 (Australian mobile without leading 0) - 9 digits
        // Convert 4XXXXXXXX to 614XXXXXXXX (11 digits)
        if (preg_match('/^4\d{8}$/', $digitsOnly)) {
            return '61' . $digitsOnly; // Returns 614XXXXXXXX
        }

        // Not a valid Australian mobile format
        return null;
    }
}







