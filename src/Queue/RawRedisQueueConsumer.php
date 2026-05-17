<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundRequestDTO;
use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundResponseDTO;
use BAGArt\TelegramBot\ApiCommunication\Queue\TgRequestExecutionConfig;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueConsumerContract;
use Illuminate\Support\Facades\Redis;
use Throwable;

final class RawRedisQueueConsumer implements QueueConsumerContract
{
    private const string DEFAULT_REQUEST_QUEUE = 'tg-outbound-requests';

    private const int DEFAULT_BLOCK_TIMEOUT = 2;

    public function __construct(
        private readonly string $requestQueue = self::DEFAULT_REQUEST_QUEUE,
        private readonly string $redisConnection = 'default',
        private readonly int $blockTimeoutSeconds = self::DEFAULT_BLOCK_TIMEOUT,
    ) {
    }

    public function connect(): void
    {
    }

    public function consume(): ?TgOutboundRequestDTO
    {
        try {
            $result = Redis::connection($this->redisConnection)
                ->blpop(
                    $this->requestQueue,
                    $this->blockTimeoutSeconds,
                );
        } catch (Throwable) {
            return null;
        }

        if ($result === null || $result === false || !is_array($result)) {
            return null;
        }

        $payload = is_array($result) ? ($result[1] ?? null) : $result;

        if ($payload === null) {
            return null;
        }

        $unserialized = unserialize($payload, [
            'allowed_classes' => [
                TgOutboundRequestDTO::class,
                TgRequestExecutionConfig::class,
            ],
        ]);

        return $unserialized instanceof TgOutboundRequestDTO
            ? $unserialized
            : null;
    }

    public function consumeResponseQueue(string $queueName): ?TgOutboundResponseDTO
    {
        try {
            $result = Redis::connection($this->redisConnection)
                ->blpop(
                    $queueName,
                    $this->blockTimeoutSeconds,
                );
        } catch (Throwable) {
            return null;
        }

        if ($result === null || $result === false || !is_array($result)) {
            return null;
        }

        $payload = $result[1] ?? null;

        if ($payload === null) {
            return null;
        }

        $unserialized = unserialize($payload, [
            'allowed_classes' => [
                TgOutboundResponseDTO::class,
            ],
        ]);

        return $unserialized instanceof TgOutboundResponseDTO
            ? $unserialized
            : null;
    }
}
