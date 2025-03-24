<?php

namespace App\Enums;

enum ProductStatusEnum: string
{
    use Enum;

    case ACTIVE = 'active';
    case DEACTIVE = 'deactive';
}
