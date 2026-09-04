<?php

namespace App\Enums;

enum CategoryStatusEnum: string
{
    use Enum;

    case ACTIVE = 'active';
    case DEACTIVE = 'deactive';
}
