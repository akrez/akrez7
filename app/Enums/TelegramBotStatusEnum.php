<?php

namespace App\Enums;

enum TelegramBotStatusEnum: string
{
    use Enum;

    case DEACTIVE = 'deactive';
    case ACTIVE = 'active';
}
