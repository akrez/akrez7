<?php

use App\Enums\ContactTypeEnum;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->enum('contact_type', [
                ContactTypeEnum::ADDRESS->value,
                ContactTypeEnum::TELEGRAM->value,
                ContactTypeEnum::WHATSAPP->value,
                ContactTypeEnum::PHONE->value,
                ContactTypeEnum::EMAIL->value,
                ContactTypeEnum::INSTAGRAM->value,
            ])->nullable();
            $table->string('contact_key', 1023)->nullable();
            $table->string('contact_value', 1023)->nullable();
            $table->string('contact_link', 1023)->nullable();
            $table->decimal('contact_order')->nullable();
            $table->boolean('presenter_visible')->nullable();
            $table->boolean('invoice_visible')->nullable();
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
