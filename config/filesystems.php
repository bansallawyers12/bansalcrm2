<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'invoices' => [
            'driver' => 'local',
            'root'   => public_path() . '/invoices',
        ],
        's3' => [
            'driver' => 's3',
            // File uploads (documents, attachments, etc.) — separate from SES/email credentials.
            'key' => env('AWS_ACCESS_KEY_ID_FILE_UPLOAD'),
            'secret' => env('AWS_SECRET_ACCESS_KEY_FILE_UPLOAD'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        /*
         * Dedicated disk for SES inbound emails (raw .eml objects stored by SES rule).
         * Bucket: SES_INBOUND_BUCKET (defaults to AWS_BUCKET, e.g. bansalcrm)
         * Prefix: SES_INBOUND_PREFIX (e.g. emails/incoming/)
         */
        's3_inbound' => [
            'driver' => 's3',
            // SES inbound email sync — uses email credentials, not file-upload credentials.
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-2'),
            'bucket' => env('SES_INBOUND_BUCKET', env('AWS_BUCKET', 'bansalcrm')),
            'root' => env('SES_INBOUND_PREFIX', 'emails/incoming'),
            'url' => env('AWS_URL'),
            'throw' => false,
        ],

    ],

];
