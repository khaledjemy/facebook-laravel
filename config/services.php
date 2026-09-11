<?php

return [

    'websocket_url' => env('WEBSOCKET_URL', 'ws://127.0.0.1:8081'),
    'websocket_secret' => env('WEBSOCKET_SECRET', env('APP_KEY')),
    'webrtc' => [
        'stun_url' => env('WEBRTC_STUN_URL', 'stun:stun.l.google.com:19302'),
        'turn_url' => env('WEBRTC_TURN_URL'),
        'turn_username' => env('WEBRTC_TURN_USERNAME'),
        'turn_credential' => env('WEBRTC_TURN_CREDENTIAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, SparkPost and others. This file provides a sane default
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

];
