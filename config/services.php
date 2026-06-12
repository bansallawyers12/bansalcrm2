<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY', env('AWS_ACCESS_KEY_ID')),
        'secret' => env('SES_SECRET', env('AWS_SECRET_ACCESS_KEY')),
        'region' => env('SES_REGION', env('AWS_DEFAULT_REGION', 'ap-southeast-2')),
    ],

    /*
    | Education Elite outbound (AWS SES). Credentials use services.ses above.
    | SES_ELITE_SENDERS: comma-separated verified @educationelite.com.au addresses.
    */
    'ses_elite' => [
        'senders' => env('SES_ELITE_SENDERS', env('SES_ELITE_FROM_EMAIL', 'info@educationelite.com.au')),
        'from_email' => env('SES_ELITE_FROM_EMAIL', 'info@educationelite.com.au'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\Admin::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    'facebook' => [
        'client_id'     => env('FB_ID'),
        'client_secret' => env('FB_SECRET'),
        'redirect'      => env('FB_URL'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_ID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect'      => env('GOOGLE_URL'),
    ],
    
    //Add these Configurations
    'recaptcha' => [
        'key' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SITE_SECRET'),
    ],
  
    'twilio' => [
        'account_sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_PHONE_NUMBER'),
        'timeout' => env('TWILIO_TIMEOUT', 30),
    ],

    'cellcast' => [
        'api_key' => env('CELLCAST_API_KEY'),
        'base_url' => env('CELLCAST_BASE_URL', 'https://cellcast.com.au/api/v3'),
        'sender_id' => env('CELLCAST_SENDER_ID', 'BANSALIMMI'),
        'timeout' => env('CELLCAST_TIMEOUT', 30),
    ],
  
     'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'base_url' => env('SENDGRID_BASE_URL', 'https://api.sendgrid.com'),
        'from_email' => env('SENDGRID_FROM_EMAIL', ''),
        'senders' => env('SENDGRID_SENDERS', ''),
    ],

    /*
    | Second SendGrid account (e.g. Education Elite subuser).
    | API: config('services.sendgrid_elite.api_key')
    | SMTP: Mail::mailer('sendgrid_elite')->...
    | Falls back to SENDGRID_API_KEY only if SENDGRID_ELITE_API_KEY is empty.
    */
    'sendgrid_elite' => [
        'api_key' => env('SENDGRID_ELITE_API_KEY'),
        'base_url' => env('SENDGRID_ELITE_BASE_URL', env('SENDGRID_BASE_URL', 'https://api.sendgrid.com')),
        'from_email' => env('SENDGRID_ELITE_FROM_EMAIL', ''),
        'senders' => env('SENDGRID_ELITE_SENDERS', ''),
    ],

];
