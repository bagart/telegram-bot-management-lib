<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\Daemon\TgOutboundRequestDTO;
use BAGArt\TelegramBot\ApiCommunication\Daemon\TgOutboundResponseDTO;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueProducerContract;
use Illuminate\Support\Facades\Redis;

final class LaravelQueueProducer implements QueueProducerContract
{
    private const string DEFAULT_REQUEST_QUEUE = 'tg-outbound-requests';

    public function __construct(
        private readonly string $requestQueue = self::DEFAULT_REQUEST_QUEUE,
        private readonly string $redisConnection = 'default',
    ) {
    }

    public function publish(TgOutboundRequestDTO $request): void
    {
        Redis::connection($this->redisConnection)
            ->rpush(
                $this->requestQueue,
                serialize($request),
            );
    }

    public function publishResponse(TgOutboundResponseDTO $response): void
    {
        $queueName = $response->responseQueue;

        if ($queueName === null || $queueName === '') {
            return;
        }

        Redis::connection($this->redisConnection)
            ->rpush(
                $queueName,
                serialize($response),
            );
    }
}
