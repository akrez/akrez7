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
        Schema::create('payvoices', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 16)->nullable();
            $table->string('method', 16)->nullable();
            $table->string('controller', 64)->nullable();
            $table->string('useragent_device', 32)->nullable();
            $table->string('useragent_platform', 32)->nullable();
            $table->string('useragent_browser', 32)->nullable();
            $table->string('useragent_robot', 32)->nullable();
            $table->string('useragent', 2048)->nullable();
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payvoices');
    }
};
