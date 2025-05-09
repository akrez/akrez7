<?php

namespace App\Enums;

enum ContactTypeEnum: string
{
    use Enum;

    case ADDRESS = 'address';
    case TELEGRAM = 'telegram';
    case WHATSAPP = 'whatsapp';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case INSTAGRAM = 'instagram';
}
