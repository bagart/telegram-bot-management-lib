<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\Queue\TgRequestCorrelation;
use BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRequestCorrelationContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueConsumerContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueProducerContract;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider that wires Queue contracts to Laravel Queue implementations.
 *
 * Use this provider when you want to dispatch outbound requests via Laravel's
 * native queue system (php artisan queue:work) instead of the custom Redis daemon.
 *
 * Register in config/app.php or your application's providers array:
 *
 *   BAGArt\TelegramBotManagement\Queue\TgOutboundLaravelQueueServiceProvider::class,
 *
 * Then use QueuedTgBotApiDTOClient via the container:
 *
 *   $queuedClient = app(QueuedTgBotApiDTOClient::class);
 *   $response = $queuedClient->request($token, $dto);
 *
 * Run queue worker:
 *   php artisan queue:work
 */
class TgOutboundLaravelQueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            QueueProducerContract::class,
            LaravelQueueProducer::class,
        );

        $this->app->singleton(
            QueueConsumerContract::class,
            LaravelQueueConsumer::class,
        );

        $this->app->singleton(
            TgRequestCorrelationContract::class,
            function (): TgRequestCorrelation {
                return new TgRequestCorrelation(
                    instanceId: config('app.name', 'tg-daemon'),
                );
            },
        );
    }
}
