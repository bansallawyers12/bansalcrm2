<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\VerifiedNumber;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class SMSTwilioController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function showForm()
    {
        $verifiedNumbers = VerifiedNumber::where('is_verified', true)
            ->orderBy('verified_at', 'desc')
            ->get();
        return view('sms.form', compact('verifiedNumbers'));
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['phone_number' => 'required']);

        $phoneNumber = $request->phone_number;
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store or update verification code
        VerifiedNumber::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'verification_code' => $verificationCode,
                'is_verified' => false,
                'verified_at' => null
            ]
        );

        $result = $this->twilioService->sendVerificationCode($phoneNumber, $verificationCode);
        //dd($result);
        if ($result['success']) {
            return response()->json(['message' => 'Verification code sent successfully']);
        }

        return response()->json(['message' => 'Failed to send verification code'], 500);
    }

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

        $result = $this->twilioService->sendSMS(
            $request->phone_number,
            $request->message
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }



    //Check phone is verify or not
    /*public function isPhoneVerifyOrNot(Request $request)
    {
        $request->validate(['phone_number' => 'required']);
        $verifiedNumber = VerifiedNumber::where('phone_number', $request->phone_number)->where('is_verified', 1)->first();
        if (!$verifiedNumber) {
            return response()->json(['status'=>false, 'status_bit'=>0,'message' => 'Phone number is not verified.']);
        }
        return response()->json(['status'=>true,'status_bit'=>1, 'message' => 'Phone number is already verified']);
    }*/
}
