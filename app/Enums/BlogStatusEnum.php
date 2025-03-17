<?php

namespace App\Enums;

enum BlogStatusEnum: string
{
    use Enum;

    case DEACTIVE = 'deactive';
    case ACTIVE = 'active';
}
