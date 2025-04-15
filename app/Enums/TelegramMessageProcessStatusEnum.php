<?php

namespace App\Enums;

enum TelegramMessageProcessStatusEnum: string
{
    use Enum;

    case PROCESSING = 'processing';
    case VALID = 'valid';
    case NOT_VALID = 'not_valid';
}
