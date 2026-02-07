<?php

namespace App\Services\Sms;

use App\Models\PhoneVerification;
use App\Models\ClientPhone;
use App\Models\Lead;
use App\Helpers\PhoneValidationHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationService
{
    protected $smsManager;
    protected $otpValidMinutes = 5;
    protected $resendCooldownSeconds = 30;
    protected $maxAttemptsPerHour = 3;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->smsManager = $smsManager;
    }

    /**
     * Send OTP to client phone (ClientPhone)
     */
    public function sendOTP($clientPhoneId)
    {
        $clientPhone = ClientPhone::findOrFail($clientPhoneId);

        if (PhoneValidationHelper::isPlaceholderNumber($clientPhone->client_phone ?? '')) {
            return ['success' => false, 'message' => 'Cannot verify placeholder phone numbers'];
        }

        if (!$clientPhone->isAustralianNumber()) {
            return ['success' => false, 'message' => 'Phone verification is only available for Australian numbers'];
        }

        if (!$this->canSendOTP($clientPhone->client_phone ?? '', $clientPhone->client_country_code ?? '')) {
            return ['success' => false, 'message' => 'Too many OTP requests. Please try again later.'];
        }

        $otpCode = PhoneVerification::generateOTP();
        $expiresAt = Carbon::now()->addMinutes($this->otpValidMinutes);

        PhoneVerification::where('client_phone_id', $clientPhoneId)->where('is_verified', false)->delete();

        $fullNumber = trim(($clientPhone->client_country_code ?? '') . '' . ($clientPhone->client_phone ?? ''));
        $verification = PhoneVerification::create([
            'client_phone_id' => $clientPhoneId,
            'lead_id' => null,
            'client_id' => $clientPhone->client_id,
            'phone' => $clientPhone->client_phone,
            'country_code' => $clientPhone->client_country_code ?? '+61',
            'otp_code' => $otpCode,
            'otp_sent_at' => now(),
            'otp_expires_at' => $expiresAt,
            'is_verified' => false,
            'attempts' => 0,
            'max_attempts' => 3,
        ]);

        $message = "BANSAL IMMIGRATION: Your verification code is {$otpCode}. This code expires in {$this->otpValidMinutes} minutes.";
        $smsResult = $this->smsManager->sendSms($fullNumber, $message, 'verification', ['client_id' => $clientPhone->client_id]);

        if (!$smsResult['success']) {
            $verification->delete();
            return ['success' => false, 'message' => 'Failed to send SMS. Please try again.'];
        }

        return [
            'success' => true,
            'message' => 'Verification code sent successfully',
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_in_seconds' => $this->otpValidMinutes * 60,
        ];
    }

    /**
     * Verify OTP for client phone
     */
    public function verifyOTP($clientPhoneId, $otpCode)
    {
        $verification = PhoneVerification::where('client_phone_id', $clientPhoneId)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (!$verification) {
            return ['success' => false, 'message' => 'No verification request found'];
        }
        if ($verification->isExpired()) {
            return ['success' => false, 'message' => 'Verification code has expired'];
        }
        if (!$verification->canAttempt()) {
            return ['success' => false, 'message' => 'Maximum verification attempts exceeded'];
        }
        if ($verification->otp_code !== $otpCode) {
            $verification->incrementAttempts();
            return ['success' => false, 'message' => 'Invalid verification code', 'attempts_remaining' => $verification->max_attempts - $verification->attempts];
        }

        $verification->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => Auth::id()]);
        ClientPhone::where('id', $clientPhoneId)->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => Auth::id()]);

        return ['success' => true, 'message' => 'Phone number verified successfully'];
    }

    /**
     * Send OTP for lead phone
     */
    public function sendOTPForLead($leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $phone = $lead->phone ?? '';
        $countryCode = $lead->country_code ?? '+61';

        if (PhoneValidationHelper::isPlaceholderNumber($phone)) {
            return ['success' => false, 'message' => 'Cannot verify placeholder phone numbers'];
        }
        if (!$lead->isAustralianNumber()) {
            return ['success' => false, 'message' => 'Phone verification is only available for Australian numbers'];
        }
        if (!$this->canSendOTP($phone, $countryCode)) {
            return ['success' => false, 'message' => 'Too many OTP requests. Please try again later.'];
        }

        $otpCode = PhoneVerification::generateOTP();
        $expiresAt = Carbon::now()->addMinutes($this->otpValidMinutes);

        PhoneVerification::where('lead_id', $leadId)->where('is_verified', false)->delete();

        $fullNumber = trim($countryCode . '' . $phone);
        $verification = PhoneVerification::create([
            'client_phone_id' => null,
            'lead_id' => $leadId,
            'client_id' => null,
            'phone' => $phone,
            'country_code' => $countryCode,
            'otp_code' => $otpCode,
            'otp_sent_at' => now(),
            'otp_expires_at' => $expiresAt,
            'is_verified' => false,
            'attempts' => 0,
            'max_attempts' => 3,
        ]);

        $message = "BANSAL IMMIGRATION: Your verification code is {$otpCode}. This code expires in {$this->otpValidMinutes} minutes.";
        $smsResult = $this->smsManager->sendSms($fullNumber, $message, 'verification', []);

        if (!$smsResult['success']) {
            $verification->delete();
            return ['success' => false, 'message' => 'Failed to send SMS. Please try again.'];
        }

        return ['success' => true, 'message' => 'Verification code sent successfully', 'expires_at' => $expiresAt->toIso8601String()];
    }

    /**
     * Verify OTP for lead
     */
    public function verifyOTPForLead($leadId, $otpCode)
    {
        $verification = PhoneVerification::where('lead_id', $leadId)->where('is_verified', false)->latest()->first();
        if (!$verification) {
            return ['success' => false, 'message' => 'No verification request found'];
        }
        if ($verification->isExpired()) {
            return ['success' => false, 'message' => 'Verification code has expired'];
        }
        if (!$verification->canAttempt()) {
            return ['success' => false, 'message' => 'Maximum verification attempts exceeded'];
        }
        if ($verification->otp_code !== $otpCode) {
            $verification->incrementAttempts();
            return ['success' => false, 'message' => 'Invalid verification code'];
        }

        $verification->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => Auth::id()]);
        Lead::where('id', $leadId)->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => Auth::id()]);

        return ['success' => true, 'message' => 'Phone number verified successfully'];
    }

    public function canResendOTP($clientPhoneId)
    {
        $last = PhoneVerification::where('client_phone_id', $clientPhoneId)->latest('otp_sent_at')->first();
        if (!$last) {
            return true;
        }
        return Carbon::now()->diffInSeconds($last->otp_sent_at) >= $this->resendCooldownSeconds;
    }

    public function canResendOTPForLead($leadId)
    {
        $last = PhoneVerification::where('lead_id', $leadId)->latest('otp_sent_at')->first();
        if (!$last) {
            return true;
        }
        return Carbon::now()->diffInSeconds($last->otp_sent_at) >= $this->resendCooldownSeconds;
    }

    protected function canSendOTP($phone, $countryCode)
    {
        $recent = PhoneVerification::forPhone($phone, $countryCode)
            ->where('otp_sent_at', '>', Carbon::now()->subHour())
            ->count();
        return $recent < $this->maxAttemptsPerHour;
    }
}
