<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tg_bot_uuid
 * @property string $token
 */
class TgBot extends Model
{
    use HasTimestamps;
    use HasUuids;

    protected $primaryKey = 'tg_bot_uuid';

    protected $fillable = [
        'tg_bot_uuid',
        'token',
    ];

    protected $hidden = [
        'token',
    ];
}
