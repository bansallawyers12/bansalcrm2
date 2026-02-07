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
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
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





];
