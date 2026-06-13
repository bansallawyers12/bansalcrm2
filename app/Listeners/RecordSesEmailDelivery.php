<?php

namespace App\Listeners;

use App\Services\SesEmailDeliveryService;
use Illuminate\Mail\Events\MessageSent;

class RecordSesEmailDelivery
{
    public function __construct(
        private SesEmailDeliveryService $deliveryService
    ) {}

    public function handle(MessageSent $event): void
    {
        // Laravel 11+ stores the mailer name in $event->data['mailer'], not $event->mailer.
        $mailer = (string) ($event->data['mailer'] ?? '');
        if (! in_array($mailer, $this->deliveryService->trackedMailers(), true)) {
            return;
        }

        $this->deliveryService->handleMessageSent($event);
    }
}
