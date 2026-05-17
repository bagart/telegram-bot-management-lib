<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue;

use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundRequestDTO;
use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundResponseDTO;
use BAGArt\TelegramBot\Contracts\ApiCommunication\QueueConsumerContract;
use Illuminate\Support\Facades\Cache;

final class LaravelQueueConsumer implements QueueConsumerContract
{
    public function connect(): void
    {
    }

    public function consume(): ?TgOutboundRequestDTO
    {
        return null;
    }

    public function consumeResponseQueue(string $queueName): ?TgOutboundResponseDTO
    {
        $payload = Cache::store('redis')->get($queueName);

        if ($payload === null) {
            return null;
        }

        Cache::store('redis')->forget($queueName);

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
