<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\VerifiedNumber;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function showForm()
    {
        return view('sms.form');
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:918',
        ]);

        try {
            $response = $this->smsService->sendSms(
                $request->phone,
                $request->message
            );

            if ($response['meta']['code'] === 200) {
                return back()->with('success', 'SMS sent successfully! Message ID: ' .
                    ($response['data']['messages'][0]['message_id'] ?? 'N/A'));
            }

            return back()->with('error', 'Failed to send SMS: ' . ($response['msg'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
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
        $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)->where('is_verified', 1)->first();
        if (!$verifiedNumber) {
            return response()->json(['status'=>false, 'status_bit'=>0,'message' => 'Phone number is not verified.']);
        }
        return response()->json(['status'=>true,'status_bit'=>1, 'message' => 'Phone number is already verified']);
    }

    //If phone is not verify then send verification code
    public function sendVerificationCode(Request $request)
    {
        $request->validate(['phone_number' => 'required']);
        $phoneNumber = $request->phone_number;
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        //Store or update verification code
        VerifiedNumber::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'verification_code' => $verificationCode,
                'is_verified' => false,
                'verified_at' => null
            ]
        );
        $result = $this->smsService->sendVerificationCode($phoneNumber, $verificationCode); //dd($result);
        if ($result['success']) {
            return response()->json(['message' => 'Verification code sent successfully']);
        }
        return response()->json(['message' => 'Failed to send verification code'], 500);
    }

    //verify Code
    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'verification_code' => 'required'
        ]);

        $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$verifiedNumber) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }
        $verifiedNumber->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_code' => null
        ]);
        return response()->json(['message' => 'Phone number verified successfully']);
    }

}







