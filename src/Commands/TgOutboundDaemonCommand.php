<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Commands;

use BAGArt\TelegramBot\ApiCommunication\Async\Scheduler\FiberScheduler;
use BAGArt\TelegramBot\ApiCommunication\Daemon\TgOutboundDaemon;
use BAGArt\TelegramBot\ApiCommunication\Daemon\TgOutboundRequestExecutor;
use BAGArt\TelegramBot\ApiCommunication\Daemon\TgRequestOrderingManager;
use BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRateLimiterContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\ClientServices\TgRetryPolicyContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueConsumerContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueProducerContract;
use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiTransportContract;
use BAGArt\TelegramBot\Wrappers\TgBotLogWrapper;
use Illuminate\Console\Command;

class TgOutboundDaemonCommand extends Command
{
    protected $signature = 'tgbm:outbound-daemon
                            {--transport= : Transport type (guzzle, curl-multi)}
                            {--memory-limit=256M : PHP memory limit for daemon}
                            {--debug : Enable debug logging}';

    protected $description = 'Start the Telegram outbound request daemon';

    public function handle(
        QueueConsumerContract $consumer,
        QueueProducerContract $producer,
        TgBotApiTransportContract $transport,
        TgRateLimiterContract $rateLimiter,
        TgRetryPolicyContract $retryPolicy,
        TgBotLogWrapper $logger,
    ): int {
        $this->configureEnvironment();

        $this->line('Starting TgOutboundDaemon...');
        $this->line(sprintf('  Transport: %s', $transport::class));
        $this->line(sprintf('  Consumer:  %s', $consumer::class));
        $this->line(sprintf('  Producer:  %s', $producer::class));

        $scheduler = new FiberScheduler(
            transport: $transport,
            logger: $logger,
        );

        $executor = new TgOutboundRequestExecutor(
            transport: $transport,
            logger: $logger,
        );

        $ordering = new TgRequestOrderingManager(
            scheduler: $scheduler,
            logger: $logger,
        );

        $daemon = new TgOutboundDaemon(
            consumer: $consumer,
            producer: $producer,
            scheduler: $scheduler,
            executor: $executor,
            ordering: $ordering,
            rateLimiter: $rateLimiter,
            retryPolicy: $retryPolicy,
            logger: $logger,
        );

        $daemon->run();

        return self::SUCCESS;
    }

    private function configureEnvironment(): void
    {
        $memoryLimit = (string) $this->option('memory-limit');

        ini_set('memory_limit', $memoryLimit);

        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }

        if ($this->option('debug')) {
            $this->line('Debug mode enabled');
        }
    }
}
