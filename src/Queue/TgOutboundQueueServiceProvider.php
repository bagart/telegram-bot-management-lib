<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\ClientServices\TgRateLimiter;
use BAGArt\TelegramBot\ApiCommunication\ClientServices\TgRetryPolicy;
use BAGArt\TelegramBot\ApiCommunication\Daemon\TgRequestCorrelation;
use BAGArt\TelegramBot\ApiCommunication\Transport\TgCurlMultiTransport;
use BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRateLimiterContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRetryPolicyContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueConsumerContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueProducerContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiTransportContract;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider that wires Queue contracts to Redis implementations.
 *
 * Register this in config/app.php or your application's providers array:
 *
 *   BAGArt\TelegramBotManagement\Queue\TgOutboundQueueServiceProvider::class,
 *
 * Then use QueuedTgBotApiDTOClient via the container:
 *
 *   $queuedClient = app(QueuedTgBotApiDTOClient::class);
 *   $response = $queuedClient->request($token, $dto);
 */
class TgOutboundQueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TgRateLimiterContract::class, function (): TgRateLimiterContract {
            return new TgRateLimiter();
        });

        $this->app->singleton(TgRetryPolicyContract::class, function (): TgRetryPolicyContract {
            return new TgRetryPolicy();
        });

        $this->app->singleton(TgBotApiTransportContract::class, function (): TgBotApiTransportContract {
            return new TgCurlMultiTransport();
        });

        $this->app->singleton(
            QueueProducerContract::class,
            LaravelQueueProducer::class,
        );

        $this->app->singleton(
            QueueConsumerContract::class,
            LaravelQueueConsumer::class,
        );

        $this->app->singleton(
            \BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRequestCorrelationContract::class,
            function (): TgRequestCorrelation {
                return new TgRequestCorrelation(
                    instanceId: config('app.name', 'tg-daemon'),
                );
            },
        );
    }
}
