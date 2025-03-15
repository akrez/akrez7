<?php

use App\Enums\BlogStatus;
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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->nullable();
            $table->string('short_description', 120)->nullable();
            $table->string('description', 512)->nullable();
            $table->unsignedBigInteger('created_by', 512)->index();
            $table->enum('blog_status', [
                BlogStatus::ACTIVE->value,
                BlogStatus::DEACTIVE->value,
            ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
