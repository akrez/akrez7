<?php

namespace App\Enums;

enum PresenterEnum: string
{
    use Enum;

    case FRONT = 'front';
    case DOMAIN = 'domain';
    case PREVIEW = 'preview';
}
