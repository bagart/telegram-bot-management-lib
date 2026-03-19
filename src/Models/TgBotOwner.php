<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotManagement\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TgBotOwner extends Model
{
    use HasTimestamps;
    use HasUuids;

    protected $primaryKey = 'tg_bot_owner_uuid';

    protected $fillable = [
        'tg_bot_owner_uuid',
        'tg_bot_uuid',
        'user_id',
    ];
}
