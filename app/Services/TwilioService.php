<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendSMS($to, $message)
    {
        try {
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => config('services.twilio.phone'),
                    'body' => $message
                ]
            );

            return ['success' => true, 'message' => 'SMS sent successfully!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendVerificationCode($to, $code)
    {
        return $this->sendSMS($to, "Your verification code is: $code");
    }
}
