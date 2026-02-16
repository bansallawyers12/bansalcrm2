<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmailManager extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         // Configure mailer from emails table (not .env) - required when job runs from queue
         $emailService = app(\App\Services\EmailService::class);
         $emailConfig = $emailService->configureMailerForEmail($this->array['from'] ?? null);
         if ($emailConfig) {
             $this->array['from'] = $emailConfig->email;
             $this->array['name'] = $emailConfig->display_name ?? $this->array['name'] ?? $emailConfig->email;
         }

         return $this->view($this->array['view'])
                     ->from($this->array['from'], $this->array['name'] ?? $this->array['from'])
                     ->subject($this->array['subject'])
                     ->attach($this->array['file'],[
                         'as' => $this->array['file_name'],
                         'mime' => 'application/pdf'
                     ]);
     }
 }
