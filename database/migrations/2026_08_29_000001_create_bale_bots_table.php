<?php

use App\Enums\BaleBotStatusEnum;
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
        Schema::create('bale_bots', function (Blueprint $table) {
            $table->id();
            $table->enum('bale_bot_status', [
                BaleBotStatusEnum::ACTIVE->value,
                BaleBotStatusEnum::DEACTIVE->value,
            ]);
            $table->string('bale_token', 64);
            $table->boolean('user_service')->nullable()->default(false);
            $table->boolean('notify_admin_on_invoice')->nullable()->default(false);
            $table->string('admin', 128)->nullable();
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bale_bots');
    }
};
