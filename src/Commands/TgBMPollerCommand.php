<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Commands;

use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract;
use BAGArt\TelegramBot\TgApi\Methods\DTO\SendMessageMethodDTO;
use BAGArt\TelegramBot\TgApi\Types\DTO\UpdateTypeDTO;
use BAGArt\TelegramBot\Wrappers\TgBotLogWrapper;
use BAGArt\TelegramBotBasic\Commands\Traits\LongPollingCommandTrait;
use BAGArt\TelegramBotManagement\Models\TgBot;
use Illuminate\Console\Command;

class TgBMPollerCommand extends Command
{
    use LongPollingCommandTrait;

    protected $signature = 'tgbm:poll
                            {bot_uuid : TgBot UUID from database}
                            {--echo       : ECHO-mode(ping-pong)}
                            {--show       : Show messages}
                            {--timeout=30 : Long-polling server timeout in seconds}
                            {--limit=100  : Maximum updates per request (1–100)}
                            {--once       : Process one batch of updates and exit}';

    protected $description = 'Start the Telegram bot in long-polling mode';

    private bool $keepRunning = true;

    public function handle(
        TgBotApiDTOClientContract $tgDTOClient,
        TgBotLogWrapper $logger,
    ): int {
        $botUuid = $this->argument('bot_uuid');
        $tgBot = TgBot::find($botUuid);
        if ($tgBot === null) {
            $this->error("Bot not found: {$botUuid}");

            return self::FAILURE;
        }

        $token = $tgBot->token;
        $timeout = (int) $this->option('timeout');
        $limit = (int) $this->option('limit');
        $once = $this->option('once');
        $echoMode = $this->option('echo');
        $showMode = $this->option('show');

        return $this->longPolling(
            tgDTOClient: $tgDTOClient,
            logger: $logger,
            token: $token,
            fn: function (
                UpdateTypeDTO $update,
                int $total,
            ) use (
                $tgDTOClient,
                $token,
                $echoMode,
                $showMode,
                $once,
            ): ?bool {
                if ($showMode) {
                    if ($update->message) {
                        $this->line("{$update->message->chat->id}: {$update->message->text}");
                    } else {
                        $bp = 1;//@todo
                    }
                }
                if ($echoMode) {
                    if ($update->message) {
                        $sendMessageResponse = $tgDTOClient->request(
                            $token,
                            new SendMessageMethodDTO(
                                chatId: $update->message->chat->id,
                                text: "echo: {$update->message->text}",
                            ),
                        );
                        assert($sendMessageResponse->ok === true);
                    } else {
                        $bp = 1;//@todo
                    }
                }

                if ($once) {
                    return false;
                }

                return true;
            },
            timeout: $timeout,
            limit: $limit,
        );
    }
}
