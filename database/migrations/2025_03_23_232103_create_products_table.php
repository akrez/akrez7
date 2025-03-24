<?php

use App\Enums\ProductStatusEnum;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->nullable();
            $table->string('code', 32)->nullable();
            $table->unsignedBigInteger('blog_id')->index();
            $table->enum('product_status', [
                ProductStatusEnum::ACTIVE->value,
                ProductStatusEnum::DEACTIVE->value,
            ])->nullable();
            $table->decimal('product_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
