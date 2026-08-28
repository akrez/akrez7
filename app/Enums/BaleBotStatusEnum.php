<?php

namespace App\Enums;

enum BaleBotStatusEnum: string
{
    use Enum;

    case DEACTIVE = 'deactive';
    case ACTIVE = 'active';
}
