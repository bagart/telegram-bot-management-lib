<?php

namespace BAGArt\TelegramBotManagement\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tg_bot_modules', function (Blueprint $table) {
            $table->uuid('tg_bot_module_uuid');
            $table->uuid('tg_bot_uuid');
            $table->integer('chat_id')->nullable();
            $table->integer('message_thread_id')->nullable();
            $table->jsonb('module_names')->default('[]')->index()->using('gin');
            $table->unique(['tg_bot_uuid', 'chat_id', 'message_thread_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tg_bot_modules');
    }
};
