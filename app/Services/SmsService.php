<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.cellcast.api_key');
        $this->apiUrl = 'https://cellcast.com.au/api/v3';
        $this->client = new Client();
    }

    public function sendSms($to, $message)
    {
        try {
            // Convert single number to array if needed
            $numbers = is_array($to) ? $to : [$to];

            $payload = [
                'sms_text' => $message,
                'numbers' => $numbers,
                'from' => 'BANSALIMMI'
            ];

            Log::info('Sending SMS', [
                'url' => $this->apiUrl . '/send-sms',
                'payload' => $payload
            ]);

            $response = $this->client->post($this->apiUrl . '/send-sms', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('SMS API Response', ['response' => $result]);

            //return $result;
            return ['success' => true, 'message' => 'SMS sent successfully!'];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            //Log::error('SMS API Error', ['error' => $errorResponse]);
            //throw new \Exception($errorResponse['msg'] ?? 'Failed to send SMS');
            return ['success' => false, 'message' => 'Failed to send SMS'];
        } catch (\Exception $e) {
            //Log::error('SMS Service Error', ['error' => $e->getMessage()]);
            //throw $e;
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function getSmsStatus($messageId)
    {
        try {
            $response = $this->client->get($this->apiUrl . '/get-sms', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => ['message_id' => $messageId]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('SMS Status Check Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    public function getResponses($page = 1)
    {
        try {
            $response = $this->client->get($this->apiUrl . '/get-responses', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'page' => $page,
                    'type' => 'sms'
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('SMS Responses Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    //Send verification code sms
    public function sendVerificationCodeSMS($to, $message)
    {
        try {
            // Convert single number to array if needed
            $numbers = is_array($to) ? $to : [$to];
            $payload = [
                'sms_text' => $message,
                'numbers' => $numbers
            ];

            Log::info('Sending SMS', [
                'url' => $this->apiUrl . '/send-sms',
                'payload' => $payload
            ]);

            $response = $this->client->post($this->apiUrl . '/send-sms', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('SMS API Response', ['response' => $result]);

            //return $result;
            return ['success' => true, 'message' => 'SMS sent successfully!'];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            //Log::error('SMS API Error', ['error' => $errorResponse]);
            //throw new \Exception($errorResponse['msg'] ?? 'Failed to send SMS');

            return ['success' => false, 'message' => 'Failed to send SMS' ];
        } catch (\Exception $e) {
            //Log::error('SMS Service Error', ['error' => $e->getMessage()]);
            //throw $e;

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    //Send verification code
    public function sendVerificationCode($to, $code)
    {
        return $this->sendVerificationCodeSMS($to, "Your verification code is: $code");
    }
}
