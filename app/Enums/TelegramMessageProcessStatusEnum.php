<?php

namespace App\Enums;

enum TelegramMessageProcessStatusEnum: string
{
    use Enum;

    case PROCESSING = 'processing';
    case VALID = 'valid';
    case IS_BOT = 'is_bot';
    case NOT_VALID = 'not_valid';
}
