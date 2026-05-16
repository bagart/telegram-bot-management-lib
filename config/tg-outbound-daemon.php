<?php

declare(strict_types=1);

/**
 * Laravel Queue Integration Configuration for TgOutboundDaemon.
 *
 * This file demonstrates how to wire the QueueProducerContract and
 * QueueConsumerContract to Laravel Queue for the Telegram outbound daemon.
 *
 * Usage:
 *   1. Copy this file to config/tg-outbound-daemon.php in your Laravel app
 *   2. Add bindings in AppServiceProvider or a dedicated service provider
 *   3. Run: php artisan tgbm:outbound-daemon
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Outbound Request Queue Connection
    |--------------------------------------------------------------------------
    |
    | The Laravel queue connection to use for outbound Telegram requests.
    | Must be the same for both producer (Laravel app) and consumer (daemon).
    |
    */
    'queue_connection' => env('TG_OUTBOUND_QUEUE_CONNECTION', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Outbound Request Queue Name
    |--------------------------------------------------------------------------
    |
    | The queue name where Telegram API requests are published.
    |
    */
    'request_queue' => env('TG_OUTBOUND_REQUEST_QUEUE', 'tg-outbound-requests'),

    /*
    |--------------------------------------------------------------------------
    | Response Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection for Telegram API responses.
    | For sync requests, each has a unique response queue name.
    |
    */
    'response_queue_connection' => env('TG_OUTBOUND_RESPONSE_QUEUE_CONNECTION', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Default Request Execution Config
    |--------------------------------------------------------------------------
    |
    | Default settings for queued Telegram API requests.
    |
    */
    'default_config' => [
        'mode' => 'sync',
        'ordered' => false,
        'ordering_key' => null,
        'timeout_seconds' => 30,
        'max_retry_attempts' => 3,
        'retry_base_delay_ms' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Daemon Settings
    |--------------------------------------------------------------------------
    |
    | Settings for the outbound daemon process.
    |
    */
    'daemon' => [
        'memory_limit' => env('TG_OUTBOUND_DAEMON_MEMORY_LIMIT', '256M'),
        'transport' => env('TG_OUTBOUND_TRANSPORT', 'curl-multi'),
    ],
];
