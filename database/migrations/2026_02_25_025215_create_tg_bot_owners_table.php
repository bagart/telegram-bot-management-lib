<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tg_bot_owners', function (Blueprint $table) {
            $table->uuid('tg_bot_owner_uuid');
            $table->uuid('tg_bot_uuid');
            $table->integer('user_id')->index();
            $table->unique(['tg_bot_uuid', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tg_bot_owners');
    }
};
