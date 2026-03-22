<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement;

use Illuminate\Support\ServiceProvider;

class TelegramBotManagementServiceProvider extends ServiceProvider
{
    protected array $commands = [
        \BAGArt\TelegramBotManagement\Commands\TgBMMonitorCommand::class,
        \BAGArt\TelegramBotManagement\Commands\TgBMPollerCommand::class,
        \BAGArt\TelegramBotManagement\Commands\TgBotManagerMigrate::class,
        \BAGArt\TelegramBotManagement\Commands\TgBotManagerInit::class,
    ];

    public function register(): void
    {
        $this->commands($this->commands);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
