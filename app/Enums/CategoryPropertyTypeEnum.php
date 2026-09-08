<?php

namespace App\Enums;

enum CategoryPropertyTypeEnum: string
{
    use Enum;

    case OPTIONS = 'options';
    case RANGE = 'range';
}
