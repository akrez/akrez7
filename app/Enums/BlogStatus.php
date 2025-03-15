<?php

namespace App\Enums;

enum BlogStatus: string
{
    use Enum;

    case DEACTIVE = 'deactive';
    case ACTIVE = 'active';
}
