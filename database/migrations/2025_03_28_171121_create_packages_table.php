<?php

use App\Enums\PackageStatusEnum;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->enum('package_status', [
                PackageStatusEnum::ACTIVE->value,
                PackageStatusEnum::DEACTIVE->value,
                PackageStatusEnum::OUT_OF_STOCK->value,
            ]);
            $table->decimal('price', 24, 0)->unsigned();
            $table->unsignedBigInteger('color_id')->nullable()->index();
            $table->unsignedBigInteger('blog_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('guaranty', 256)->nullable();
            $table->string('unit', 256)->nullable();
            $table->boolean('show_price')->default(false)->nullable();
            $table->string('description', 2048)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
