<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Message;
use Illuminate\Mail\SentMessage as LaravelSentMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;

class SesEmailDeliveryService
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_BOUNCED = 'bounced';

    public const STATUS_COMPLAINED = 'complained';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FAILED = 'failed';

    public function applyOutboundHeaders(Message $message): void
    {
        $set = trim((string) config('services.ses.configuration_set', ''));
        if ($set !== '') {
            $message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', $set);
        }
    }

    public function supportsTracking(): bool
    {
        return Schema::hasTable('emails')
            && Schema::hasColumn('emails', 'delivery_status');
    }

    /**
     * @return list<string>
     */
    public function trackedMailers(): array
    {
        return ['ses', 'ses_elite'];
    }

    public function markPending(Email $email): void
    {
        if (! $this->supportsTracking()) {
            return;
        }

        $email->delivery_status = self::STATUS_PENDING;
        $email->delivery_status_at = now();
        $email->saveQuietly();
    }

    public function markFailed(Email $email, string $reason): void
    {
        if (! $this->supportsTracking()) {
            return;
        }

        if ($email->delivery_status !== null
            && $email->delivery_status !== self::STATUS_PENDING
            && $email->delivery_status !== self::STATUS_FAILED) {
            return;
        }

        $email->delivery_status = self::STATUS_FAILED;
        $email->delivery_status_at = now();
        $email->delivery_detail = ['error' => $reason];
        $email->saveQuietly();
    }

    public function markAccepted(Email $email, ?string $sesMessageId): void
    {
        if (! $this->supportsTracking()) {
            return;
        }

        $normalizedId = $this->normalizeMessageId($sesMessageId);
        if ($normalizedId !== null) {
            $email->message_id = $normalizedId;
        }

        $email->delivery_status = self::STATUS_SENT;
        $email->delivery_status_at = now();
        $email->saveQuietly();
    }

    public function extractMessageId(mixed $sent): ?string
    {
        if ($sent === null) {
            return null;
        }

        if ($sent instanceof LaravelSentMessage) {
            $symfony = $sent->getSymfonySentMessage();
        } elseif ($sent instanceof SymfonySentMessage) {
            $symfony = $sent;
        } else {
            return null;
        }

        if ($symfony !== null) {
            $id = $this->normalizeMessageId($symfony->getMessageId());
            if ($id !== null) {
                return $id;
            }
        }

        if ($sent instanceof LaravelSentMessage) {
            $id = $this->normalizeMessageId($sent->getMessageId());
            if ($id !== null) {
                return $id;
            }
        }

        return null;
    }

    public function normalizeMessageId(?string $messageId): ?string
    {
        if ($messageId === null) {
            return null;
        }

        $id = trim($messageId);
        if ($id === '') {
            return null;
        }

        if (str_starts_with($id, '<') && str_ends_with($id, '>')) {
            $id = trim($id, '<>');
        }

        return $id !== '' ? $id : null;
    }

    public function handleMessageSent(MessageSent $event): void
    {
        if (! $this->supportsTracking()) {
            return;
        }

        $emailId = $this->headerValue($event, 'X-CRM-Email-Id');
        if ($emailId === null || ! ctype_digit($emailId)) {
            return;
        }

        $email = Email::find((int) $emailId);
        if ($email === null) {
            return;
        }

        $messageId = $this->extractMessageId($event->sent);
        $this->markAccepted($email, $messageId);
    }

    /**
     * Process an AWS SNS notification payload (SubscriptionConfirmation or Notification).
     */
    public function handleSnsPayload(array $sns): void
    {
        $type = (string) ($sns['Type'] ?? '');

        if ($type === 'SubscriptionConfirmation') {
            $subscribeUrl = (string) ($sns['SubscribeURL'] ?? '');
            if ($subscribeUrl !== '') {
                try {
                    $response = Http::timeout(15)->get($subscribeUrl);
                    if ($response->successful()) {
                        Log::info('ses.sns.subscription_confirmed', ['topic' => $sns['TopicArn'] ?? null]);
                    } else {
                        Log::error('ses.sns.subscription_failed', [
                            'status' => $response->status(),
                            'topic' => $sns['TopicArn'] ?? null,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('ses.sns.subscription_failed', ['error' => $e->getMessage()]);
                }
            }

            return;
        }

        if ($type !== 'Notification') {
            return;
        }

        $message = (string) ($sns['Message'] ?? '');
        if ($message === '') {
            return;
        }

        $payload = json_decode($message, true);
        if (! is_array($payload)) {
            Log::warning('ses.sns.invalid_message_json');

            return;
        }

        $this->applySesEvent($payload);
    }

    public function applySesEvent(array $payload): void
    {
        if (! $this->supportsTracking()) {
            return;
        }

        $eventType = strtolower((string) ($payload['eventType'] ?? $payload['notificationType'] ?? ''));
        $mail = is_array($payload['mail'] ?? null) ? $payload['mail'] : [];
        $messageId = $this->normalizeMessageId((string) ($mail['messageId'] ?? ''));

        if ($messageId === null) {
            return;
        }

        $email = Email::query()
            ->where(function ($query) use ($messageId) {
                $query->where('message_id', $messageId)
                    ->orWhere('message_id', '<'.$messageId.'>');
            })
            ->first();
        if ($email === null) {
            Log::info('ses.event.no_matching_email', ['message_id' => $messageId, 'event' => $eventType]);

            return;
        }

        $status = match ($eventType) {
            'send' => self::STATUS_SENT,
            'delivery' => self::STATUS_DELIVERED,
            'bounce' => self::STATUS_BOUNCED,
            'complaint' => self::STATUS_COMPLAINED,
            'reject' => self::STATUS_REJECTED,
            default => null,
        };

        if ($status === null) {
            return;
        }

        // Do not downgrade to "sent" once a later SES event was already recorded.
        $current = (string) ($email->delivery_status ?? '');
        if ($status === self::STATUS_SENT && in_array($current, [
            self::STATUS_DELIVERED,
            self::STATUS_BOUNCED,
            self::STATUS_COMPLAINED,
            self::STATUS_REJECTED,
            self::STATUS_FAILED,
        ], true)) {
            return;
        }

        $detail = $this->detailFromEvent($eventType, $payload);
        $timestamp = $this->eventTimestamp($eventType, $payload) ?? now();

        $email->delivery_status = $status;
        $email->delivery_status_at = $timestamp;
        if ($detail !== []) {
            $email->delivery_detail = $detail;
        }
        $email->saveQuietly();

        Log::info('ses.event.applied', [
            'email_id' => $email->id,
            'message_id' => $messageId,
            'status' => $status,
        ]);
    }

    public function labelForStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent to SES',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_BOUNCED => 'Bounced',
            self::STATUS_COMPLAINED => 'Complaint',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_FAILED => 'Send failed',
            default => 'Unknown',
        };
    }

    public function badgeClassForStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_DELIVERED => 'delivery-delivered',
            self::STATUS_SENT, self::STATUS_PENDING => 'delivery-sent',
            self::STATUS_BOUNCED, self::STATUS_REJECTED, self::STATUS_FAILED => 'delivery-failed',
            self::STATUS_COMPLAINED => 'delivery-complaint',
            default => 'delivery-unknown',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function publicStatusPayload(Email $email): array
    {
        $status = $email->delivery_status;
        $detail = is_array($email->delivery_detail) ? $email->delivery_detail : [];

        return [
            'delivery_status' => $status,
            'delivery_status_label' => $this->labelForStatus($status),
            'delivery_status_class' => $this->badgeClassForStatus($status),
            'delivery_status_at' => $email->delivery_status_at?->toIso8601String(),
            'delivery_detail' => $detail,
            'ses_message_id' => $email->message_id,
        ];
    }

    private function headerValue(MessageSent $event, string $name): ?string
    {
        $headers = $event->message->getHeaders();
        if (! $headers->has($name)) {
            return null;
        }

        $value = trim((string) $headers->get($name)->getBodyAsString());

        return $value !== '' ? $value : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function detailFromEvent(string $eventType, array $payload): array
    {
        if ($eventType === 'bounce' && is_array($payload['bounce'] ?? null)) {
            $bounce = $payload['bounce'];
            $recipient = $bounce['bouncedRecipients'][0] ?? [];

            return [
                'bounce_type' => $bounce['bounceType'] ?? null,
                'bounce_subtype' => $bounce['bounceSubType'] ?? null,
                'diagnostic' => $recipient['diagnosticCode'] ?? null,
                'email' => $recipient['emailAddress'] ?? null,
            ];
        }

        if ($eventType === 'complaint' && is_array($payload['complaint'] ?? null)) {
            $complaint = $payload['complaint'];
            $recipient = $complaint['complainedRecipients'][0] ?? [];

            return [
                'feedback_type' => $complaint['complaintFeedbackType'] ?? null,
                'email' => $recipient['emailAddress'] ?? null,
            ];
        }

        if ($eventType === 'reject' && is_array($payload['reject'] ?? null)) {
            return ['reason' => $payload['reject']['reason'] ?? null];
        }

        if ($eventType === 'delivery' && is_array($payload['delivery'] ?? null)) {
            return [
                'smtp_response' => $payload['delivery']['smtpResponse'] ?? null,
                'recipients' => $payload['delivery']['recipients'] ?? [],
            ];
        }

        return [];
    }

    private function eventTimestamp(string $eventType, array $payload): ?\Illuminate\Support\Carbon
    {
        $raw = null;
        if ($eventType === 'delivery' && is_array($payload['delivery'] ?? null)) {
            $raw = $payload['delivery']['timestamp'] ?? null;
        } elseif ($eventType === 'bounce' && is_array($payload['bounce'] ?? null)) {
            $raw = $payload['bounce']['timestamp'] ?? null;
        } elseif ($eventType === 'complaint' && is_array($payload['complaint'] ?? null)) {
            $raw = $payload['complaint']['timestamp'] ?? null;
        } elseif (is_array($payload['mail'] ?? null)) {
            $raw = $payload['mail']['timestamp'] ?? null;
        }

        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }
}
