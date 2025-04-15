<?php

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
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id')->index()->nullable();
            $table->string('telegram_token', 64);
            $table->json('content_json');
            //
            $table->string('process_status')->nullable();
            $table->unsignedBigInteger('bot_id')->index()->nullable();
            //
            $table->unsignedBigInteger('update_id')->index()->nullable();
            $table->unsignedBigInteger('chat_id')->index()->nullable();
            $table->longText('message_text')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_messages');
    }
};
