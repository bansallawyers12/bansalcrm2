<?php

namespace App\Services\Sms;

use App\Models\ActivitiesLog;
use App\Models\ClientPhone;
use App\Models\SmsLog;
use App\Helpers\PhoneValidationHelper;
use App\Services\Sms\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * UnifiedSmsManager
 *
 * Centralized SMS service that handles all SMS operations with:
 * - Automatic provider selection (Cellcast for AU, Twilio for others)
 * - Comprehensive activity logging
 * - Error handling and retry logic
 * - Template support
 * - Delivery status tracking
 */
class UnifiedSmsManager
{
    protected SmsProviderInterface $cellcastService;
    protected SmsProviderInterface $smsService;

    public function __construct(CellcastProvider $cellcastService, TwilioProvider $smsService)
    {
        $this->cellcastService = $cellcastService;
        $this->smsService = $smsService;
    }

    /**
     * Send SMS with automatic provider selection and activity logging
     *
     * @param string $to Phone number (9-10 digits for AU numbers)
     * @param string $message SMS message content
     * @param string $type Message type: verification|notification|manual|reminder
     * @param array<string, mixed> $context Additional context (client_id, contact_id, template_id)
     * @return array<string, mixed> Result with success status and data
     */
    public function sendSms(string $to, string $message, string $type = 'manual', array $context = []): array
    {
        try {
            // Replace client/template placeholders when client_id is in context
            $message = SmsTemplateVariableResolver::apply(
                $message,
                (int) ($context['client_id'] ?? 0),
                $context
            );

            // Validate phone number
            $validation = PhoneValidationHelper::validatePhoneNumber($to);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }

            // Check if placeholder number
            if ($validation['is_placeholder'] ?? false) {
                return [
                    'success' => false,
                    'message' => 'Cannot send SMS to placeholder numbers'
                ];
            }

            // Format phone number for SMS
            $formatted = PhoneValidationHelper::formatForSMS($to);

            if (!$formatted) {
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format'
                ];
            }

            // Determine provider
            $provider = PhoneValidationHelper::getProviderForNumber($to);

            Log::info('UnifiedSmsManager: Sending SMS', [
                'to' => $formatted,
                'provider' => $provider,
                'type' => $type,
                'client_id' => $context['client_id'] ?? null
            ]);

            // Send via appropriate provider
            $result = $this->sendViaProvider($provider, $formatted, $message);

            // Extract provider message ID
            $providerMessageId = null;
            if ($result['success']) {
                if ($provider === 'twilio' && isset($result['results'][0]['sid'])) {
                    $providerMessageId = $result['results'][0]['sid'];
                } elseif ($provider === 'cellcast' && isset($result['data']['messages'][0]['message_id'])) {
                    $providerMessageId = $result['data']['messages'][0]['message_id'];
                }
                // Cellcast v3 may use different structure; try data directly
                if (!$providerMessageId && $provider === 'cellcast' && isset($result['data']['message_id'])) {
                    $providerMessageId = $result['data']['message_id'];
                }
            }

            // Extract country code from phone number or contact record
            $countryCode = $this->resolveCountryCode($context, $to, $formatted);

            // Log SMS activity to database
            $smsLog = $this->logSmsActivity([
                'client_id' => $context['client_id'] ?? null,
                'client_contact_id' => $context['contact_id'] ?? null,
                'sender_id' => $context['sender_id'] ?? Auth::id(),
                'recipient_phone' => $to,
                'country_code' => $countryCode,
                'formatted_phone' => $formatted,
                'message_content' => $message,
                'message_type' => $type,
                'template_id' => $context['template_id'] ?? null,
                'provider' => $provider,
                'provider_message_id' => $providerMessageId,
                'status' => $result['success'] ? 'sent' : 'failed',
                'error_message' => $result['success'] ? null : ($result['message'] ?? $result['error'] ?? 'Unknown error'),
                'cost' => 0,
                'sent_at' => $result['success'] ? now() : null,
            ]);

            // Add SMS log ID to result (if logging succeeded)
            if ($smsLog !== null && $smsLog->id) {
                $result['sms_log_id'] = $smsLog->id;
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('UnifiedSmsManager: Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @deprecated Use SmsTemplateVariableResolver::apply() directly.
     *
     * @param array<string, mixed> $context
     */
    protected function replaceSmsPlaceholders(string $message, array $context): string
    {
        $clientId = (int) ($context['client_id'] ?? 0);

        return SmsTemplateVariableResolver::apply($message, $clientId, $context);
    }

    /**
     * @deprecated Use SmsTemplateVariableResolver::placeholderReplacements().
     *
     * @param array<string, mixed> $context
     * @return array<string, string>
     */
    protected function buildClientPlaceholderReplacements(int $clientId, array $context): array
    {
        return SmsTemplateVariableResolver::placeholderReplacements($clientId, $context);
    }

    /**
     * Resolve country code from client phone record or the recipient number.
     *
     * @param array<string, mixed> $context
     */
    protected function resolveCountryCode(array $context, string $to, string $formatted): string
    {
        $countryCode = '+61';

        if (! empty($context['contact_id'])) {
            $contact = ClientPhone::find($context['contact_id']);
            if ($contact && ! empty($contact->client_country_code)) {
                $countryCode = $contact->client_country_code;
            }
        }

        if ($countryCode === '+61' && preg_match('/^(\+\d{1,3})/', $to, $matches)) {
            $countryCode = $matches[1];
        } elseif ($countryCode === '+61' && preg_match('/^(\+\d{1,3})/', $formatted, $matches)) {
            $countryCode = $matches[1];
        }

        return $countryCode;
    }

    /**
     * Send SMS via specific provider
     *
     * @return array<string, mixed>
     */
    protected function sendViaProvider(string $provider, string $phone, string $message): array
    {
        if ($provider === 'cellcast') {
            return $this->cellcastService->sendSms($phone, $message);
        } else {
            return $this->smsService->sendSms($phone, $message);
        }
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function sendVerificationCode(string $to, string $code, array $context = []): array
    {
        $message = "BANSAL IMMIGRATION: Your verification code is {$code}. This code expires in 5 minutes.";

        return $this->sendSms($to, $message, 'verification', $context);
    }

    /**
     * Send SMS from template
     *
     * @param array<string, string> $variables
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function sendFromTemplate(string $to, int $templateId, array $variables = [], array $context = []): array
    {
        try {
            $template = \App\Models\SmsTemplate::find($templateId);

            if (!$template || !$template->is_active) {
                return [
                    'success' => false,
                    'message' => 'Template not found or inactive'
                ];
            }

            // Replace variables in message
            $message = $this->replaceTemplateVariables($template->message, $variables);

            // Add template ID to context
            $context['template_id'] = $templateId;

            // Update template usage count
            $template->increment('usage_count');

            return $this->sendSms($to, $message, 'manual', $context);

        } catch (\Exception $e) {
            Log::error('UnifiedSmsManager: Template error', [
                'template_id' => $templateId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Template processing error'
            ];
        }
    }

    /**
     * @param array<string, string> $variables
     */
    protected function replaceTemplateVariables(string $message, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function logSmsActivity(array $data): ?SmsLog
    {
        try {
            // Create SMS log entry
            $smsLog = SmsLog::create($data);

            // Auto-create activity log entry for client timeline
            if (!empty($data['client_id'])) {
                ActivitiesLog::create([
                    'client_id' => $data['client_id'],
                    'created_by' => $data['sender_id'],
                    'subject' => $this->getActivitySubject($data['message_type'], $data['status']),
                    'description' => $this->formatActivityDescription($data),
                    'sms_log_id' => $smsLog->id,
                    'activity_type' => 'sms',
                    'task_status' => 0,
                    'pin' => 0,
                ]);
            }

            return $smsLog;
        } catch (\Exception $e) {
            // Log the error but don't fail the SMS sending
            Log::error('UnifiedSmsManager: Failed to log SMS activity', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            // Return null to prevent breaking the flow
            return null;
        }
    }

    protected function getActivitySubject(string $type, string $status): string
    {
        $statusText = $status === 'sent' ? 'sent' : 'failed to send';

        switch ($type) {
            case 'verification':
                return "{$statusText} verification SMS";
            case 'notification':
                return "{$statusText} notification SMS";
            case 'reminder':
                return "{$statusText} reminder SMS";
            case 'manual':
            default:
                return "{$statusText} SMS";
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function formatActivityDescription(array $data): string
    {
        $messageContent = trim($data['message_content']);
        $statusBadge = $data['status'] === 'sent'
            ? '<span class="badge badge-success">Sent</span>'
            : '<span class="badge badge-danger">Failed</span>';

        $providerBadge = '<span class="badge badge-info">' . strtoupper($data['provider']) . '</span>';

        $errorSection = '';
        if ($data['error_message']) {
            $errorSection = '<p class="text-danger mt-2"><small><strong>Error:</strong> '
                . htmlspecialchars($data['error_message'])
                . '</small></p>';
        }

        return "
            <div class='sms-activity'>
                <p><strong>To:</strong> {$data['formatted_phone']} {$statusBadge} {$providerBadge}</p>
                <p style='margin-bottom: 5px;'><strong>Message:</strong></p>
                <p style='background: #f8f9fa; padding: 8px; border-radius: 4px; margin: 0; white-space: pre-wrap; word-wrap: break-word;'>{$messageContent}</p>
                {$errorSection}
            </div>
        ";
    }

    /**
     * @return array<string, mixed>
     */
    public function getDeliveryStatus(int|string $smsLogId): array
    {
        try {
            $smsLog = SmsLog::find($smsLogId);

            if (!$smsLog) {
                return [
                    'success' => false,
                    'message' => 'SMS log not found'
                ];
            }

            if (!$smsLog->provider_message_id) {
                return [
                    'success' => false,
                    'message' => 'No provider message ID available'
                ];
            }

            // Query provider for status
            if ($smsLog->provider === 'cellcast') {
                $result = $this->cellcastService->getSmsStatus($smsLog->provider_message_id);
            } else {
                $result = $this->smsService->getSmsStatus($smsLog->provider_message_id);
            }

            // Update SMS log status if changed
            if ($result['success'] && isset($result['status'])) {
                $smsLog->update([
                    'status' => $result['status'],
                    'delivered_at' => $result['status'] === 'delivered' ? now() : null
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('UnifiedSmsManager: Status check error', [
                'sms_log_id' => $smsLogId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Status check failed'
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = SmsLog::query();

        if ($startDate) {
            $query->where('sent_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('sent_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'by_provider' => [
                'cellcast' => (clone $query)->where('provider', 'cellcast')->count(),
                'twilio' => (clone $query)->where('provider', 'twilio')->count(),
            ],
            'by_type' => [
                'verification' => (clone $query)->where('message_type', 'verification')->count(),
                'notification' => (clone $query)->where('message_type', 'notification')->count(),
                'manual' => (clone $query)->where('message_type', 'manual')->count(),
                'reminder' => (clone $query)->where('message_type', 'reminder')->count(),
            ],
            'total_cost' => (clone $query)->sum('cost'),
        ];
    }
}
