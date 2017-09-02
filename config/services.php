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
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'currency' => 'gbp',
    ],

    'aws' => [
        'cloudfront' => [
            'private_key' => base_path('pk-cloudfront.pem'),
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'protocol' => env('AWS_CLOUDFRONT_PROTOCOL'),
            'domain' => env('AWS_CLOUDFRONT_DOMAIN'),
            'access_key' => env('AWS_CLOUDFRONT_ACCESS_KEY'),
            'region' => env('AWS_CLOUDFRONT_REGION'),
            'url' => env('AWS_CLOUDFRONT_PROTOCOL').'://'.env('AWS_CLOUDFRONT_DOMAIN'),
        ],

        'ets' => [
            'preset_id' => env('AWS_ETS_PRESETID'),
            'pipeline_id' => env('AWS_ETS_PIPELINEID'),
            'region' => env('AWS_ETS_REGION'),
        ],
    ],

];
