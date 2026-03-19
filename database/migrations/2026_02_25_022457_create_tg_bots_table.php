<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('tg_bots', function (Blueprint $table) {
            $table->uuid('tg_bot_uuid')->primary();
            $table->string('token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tg_bots');
    }
};
