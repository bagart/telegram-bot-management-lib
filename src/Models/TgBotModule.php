<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tg_bot_module_uuid
 * @property string $tg_bot_uuid
 * @property int $chat_id
 * @property int $message_thread_id
 * @property string[] $module_names
 */
class TgBotModule extends Model
{
    use HasTimestamps;
    use HasUuids;

    protected $primaryKey = 'tg_bot_module_uuid';

    protected $fillable = [
        'tg_bot_module_uuid',
        'tg_bot_uuid',
        'chat_id',
        'message_thread_id',
        'module_names',
    ];

    protected $casts = [
        'module_names' => 'array',
    ];
}
