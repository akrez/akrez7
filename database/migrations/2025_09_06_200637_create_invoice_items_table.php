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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 24, 0)->unsigned();
            $table->integer('cnt');
            $table->unsignedBigInteger('package_id')->index();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
