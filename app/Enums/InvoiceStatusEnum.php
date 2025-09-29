<?php

namespace App\Enums;

enum InvoiceStatusEnum: string
{
    use Enum;

    case PENDING = 'pending';
    case ON_HOLD = 'on_hold';
    case WAITING_FOR_PAYMENT = 'waiting_for_payment';
    case WAITING_FOR_PAYMENT_VERIFICATION = 'waiting_for_payment_verification';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';
}
