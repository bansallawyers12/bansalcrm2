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
        if (! in_array($event->mailer ?? '', $this->deliveryService->trackedMailers(), true)) {
            return;
        }

        $this->deliveryService->handleMessageSent($event);
    }
}
