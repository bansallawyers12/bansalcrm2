<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
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
                'to' => $numbers,
                'message_length' => strlen($message)
            ]);

            $response = $this->client->post($this->apiUrl . '/send-sms', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('SMS API Response', ['response' => $result]);

            // Check if API returned success
            if (isset($result['meta']['code']) && $result['meta']['code'] === 200) {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully!',
                    'meta' => $result['meta'] ?? [],
                    'data' => $result['data'] ?? [],
                    'message_id' => $result['data']['messages'][0]['message_id'] ?? null
                ];
            }

            // API returned error
            $errorMessage = $result['msg'] ?? 'Failed to send SMS';
            Log::error('SMS API Error', ['error' => $errorMessage, 'response' => $result]);
            return [
                'success' => false,
                'message' => $errorMessage,
                'meta' => $result['meta'] ?? [],
                'msg' => $errorMessage
            ];

        } catch (ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMessage = $errorResponse['msg'] ?? 'Failed to send SMS';
            Log::error('SMS API Client Error', [
                'error' => $errorMessage,
                'status' => $e->getResponse()->getStatusCode(),
                'response' => $errorResponse
            ]);
            return [
                'success' => false,
                'message' => $errorMessage,
                'msg' => $errorMessage
            ];

        } catch (RequestException $e) {
            Log::error('SMS API Request Error', [
                'error' => $e->getMessage(),
                'url' => $this->apiUrl
            ]);
            return [
                'success' => false,
                'message' => 'Network error. Please check your connection and try again.'
            ];

        } catch (\Exception $e) {
            Log::error('SMS Service Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.'
            ];
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
                'numbers' => $numbers,
                'from' => 'BANSALIMMI'
            ];

            Log::info('Sending Verification SMS', [
                'url' => $this->apiUrl . '/send-sms',
                'to' => $numbers,
                'message_length' => strlen($message)
            ]);

            $response = $this->client->post($this->apiUrl . '/send-sms', [
                'headers' => [
                    'APPKEY' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('SMS API Response', ['response' => $result]);

            // Check if API returned success
            if (isset($result['meta']['code']) && $result['meta']['code'] === 200) {
                return [
                    'success' => true, 
                    'message' => 'SMS sent successfully!',
                    'message_id' => $result['data']['messages'][0]['message_id'] ?? null
                ];
            }

            // API returned error
            $errorMessage = $result['msg'] ?? 'Failed to send SMS';
            Log::error('SMS API Error', ['error' => $errorMessage, 'response' => $result]);
            return ['success' => false, 'message' => $errorMessage];

        } catch (ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMessage = $errorResponse['msg'] ?? 'Failed to send SMS';
            Log::error('SMS API Client Error', [
                'error' => $errorMessage,
                'status' => $e->getResponse()->getStatusCode(),
                'response' => $errorResponse
            ]);
            return ['success' => false, 'message' => $errorMessage];

        } catch (RequestException $e) {
            Log::error('SMS API Request Error', [
                'error' => $e->getMessage(),
                'url' => $this->apiUrl
            ]);
            return ['success' => false, 'message' => 'Network error. Please check your connection and try again.'];

        } catch (\Exception $e) {
            Log::error('SMS Service Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'An unexpected error occurred. Please try again.'];
        }
    }

    //Send verification code with formatted message
    public function sendVerificationCode($to, $code)
    {
        // Format message similar to migrationmanager2 style
        $message = "BANSAL IMMIGRATION: Your phone verification code is {$code}. Please provide this code to our staff to verify your phone number. This code expires in 5 minutes.";
        
        return $this->sendVerificationCodeSMS($to, $message);
    }
}
