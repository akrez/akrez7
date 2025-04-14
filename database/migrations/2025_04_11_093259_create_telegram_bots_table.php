<?php

use App\Enums\TelegramBotStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->enum('telegram_bot_status', [
                TelegramBotStatusEnum::ACTIVE->value,
                TelegramBotStatusEnum::DEACTIVE->value,
            ]);
            $table->string('telegram_token', 64);
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_bots');
    }
};
