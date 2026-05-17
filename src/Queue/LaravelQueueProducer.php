<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundRequestDTO;
use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundResponseDTO;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueProducerContract;
use BAGArt\TelegramBotManagement\Queue\Jobs\ProcessTgOutboundRequestJob;
use Illuminate\Support\Facades\Queue;

final class LaravelQueueProducer implements QueueProducerContract
{
    private const string DEFAULT_QUEUE = 'default';

    public function __construct(
        private readonly string $queue = self::DEFAULT_QUEUE,
    ) {
    }

    public function connect(): void
    {
    }

    public function publish(TgOutboundRequestDTO $request): void
    {
        $job = new ProcessTgOutboundRequestJob(
            serializedRequest: serialize($request),
        );

        Queue::connection()->push(
            $job,
            queue: $this->queue,
        );
    }

    public function publishResponse(TgOutboundResponseDTO $response): void
    {
    }
}
