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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->index();
            $table->morphs('gallery');
            $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
            $table->string('ext', 8);
            $table->string('gallery_category');
            $table->decimal('gallery_order')->nullable();
            $table->timestamp('selected_at', 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
