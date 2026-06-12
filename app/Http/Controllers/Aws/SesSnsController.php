<?php

namespace App\Http\Controllers\Aws;

use App\Http\Controllers\Controller;
use App\Services\SesEmailDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SesSnsController extends Controller
{
    public function handle(Request $request, SesEmailDeliveryService $deliveryService): \Illuminate\Http\Response
    {
        $raw = $request->getContent();
        if ($raw === '') {
            return response('empty', 400);
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload)) {
            Log::warning('ses.sns.invalid_json');

            return response('invalid json', 400);
        }

        Log::info('ses.sns.received', [
            'type' => $payload['Type'] ?? null,
            'topic' => $payload['TopicArn'] ?? null,
        ]);

        try {
            $deliveryService->handleSnsPayload($payload);
        } catch (\Throwable $e) {
            Log::error('ses.sns.handler_error', ['error' => $e->getMessage()]);

            return response('error', 500);
        }

        return response('ok', 200);
    }
}
