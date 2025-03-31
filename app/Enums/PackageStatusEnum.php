<?php

namespace App\Enums;

enum PackageStatusEnum: string
{
    use Enum;

    case ACTIVE = 'active';
    case DEACTIVE = 'deactive';
    case OUT_OF_STOCK = 'out_of_stock';
}
