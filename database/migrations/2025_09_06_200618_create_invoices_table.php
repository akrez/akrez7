<?php

use App\Enums\InvoiceStatusEnum;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_uuid');
            $table->enum('invoice_status', [
                InvoiceStatusEnum::COMPLETED->value,
                InvoiceStatusEnum::ON_HOLD->value,
                InvoiceStatusEnum::PENDING->value,
                InvoiceStatusEnum::PROCESSING->value,
                InvoiceStatusEnum::REFUNDED->value,
                InvoiceStatusEnum::SHIPPED->value,
                InvoiceStatusEnum::WAITING_FOR_PAYMENT->value,
                InvoiceStatusEnum::WAITING_FOR_PAYMENT_VERIFICATION->value,
            ]);
            $table->json('invoice_params');
            $table->unsignedBigInteger('blog_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
