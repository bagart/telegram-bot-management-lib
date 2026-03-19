<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TgBotManagerMigrate extends Command
{
    protected $signature = 'migrate:tgbm';

    protected $description = 'Telegram Bot Manager Migration';

    public function handle(): int
    {
        $path = str_replace(
            base_path().'/',
            '',
            __DIR__.'/../../database/migrations/',
        );
        Artisan::call('migrate', ['--path' => $path], $this->getOutput());

        return static::SUCCESS;
    }
}
