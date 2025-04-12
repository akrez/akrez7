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
            $table->unsignedBigInteger('id')->primary('id');
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->json('message_json')->nullable();
            $table->string('processor')->nullable();
            $table->unsignedBigInteger('blog_id')->index();
            $table->unsignedBigInteger('bot_id')->index();
            $table->timestamp('responsed_at')->nullable();
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
