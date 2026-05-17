<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Queue\Jobs;

use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundRequestDTO;
use BAGArt\TelegramBot\ApiCommunication\Queue\TgOutboundResponseDTO;
use BAGArt\TelegramBot\ApiCommunication\TgBotApiDTOClient;
use BAGArt\TelegramBot\Http\Pure\TgApiResponse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class ProcessTgOutboundRequestJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $serializedRequest,
    ) {
    }

    public function handle(TgBotApiDTOClient $dtoClient): void
    {
        $request = $this->unserializeRequest();

        if ($request === null) {
            return;
        }

        try {
            $response = $dtoClient->request(
                token: $request->token,
                dto: $request->dto,
            );

            $responseDTO = new TgOutboundResponseDTO(
                requestId: $request->requestId,
                success: true,
                result: $response,
                responseQueue: $request->responseQueue,
                completedAt: time(),
            );
        } catch (Throwable $e) {
            $responseDTO = new TgOutboundResponseDTO(
                requestId: $request->requestId,
                success: false,
                error: $e->getMessage(),
                errorCode: $e->getCode() !== 0 ? $e->getCode() : null,
                responseQueue: $request->responseQueue,
                completedAt: time(),
            );
        }

        $this->storeResponse($responseDTO);
    }

    private function unserializeRequest(): ?TgOutboundRequestDTO
    {
        $result = unserialize($this->serializedRequest, [
            'allowed_classes' => [
                TgOutboundRequestDTO::class,
                \BAGArt\TelegramBot\ApiCommunication\Queue\TgRequestExecutionConfig::class,
            ],
        ]);

        return $result instanceof TgOutboundRequestDTO
            ? $result
            : null;
    }

    private function storeResponse(TgOutboundResponseDTO $responseDTO): void
    {
        if ($responseDTO->responseQueue === null || $responseDTO->responseQueue === '') {
            return;
        }

        Cache::store('redis')
            ->set(
                $responseDTO->responseQueue,
                serialize($responseDTO),
                300,
            );
    }
}
