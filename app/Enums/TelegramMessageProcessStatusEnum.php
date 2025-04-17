<?php

namespace App\Enums;

enum TelegramMessageProcessStatusEnum: string
{
    use Enum;

    case PROCESSING = 'processing';
    case IS_BOT = 'is_bot';
    case NOT_VALID = 'not_valid';
    case BOT_NOT_FOUND = 'bot_not_found';
    case ERROR_ON_UPDATE = 'error_on_update';
    case OK = 'ok';
}
