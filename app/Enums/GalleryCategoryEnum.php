<?php

namespace App\Enums;

enum GalleryCategoryEnum: string
{
    use Enum;

    case PRODUCT_IMAGE = 'product_image';
    case BLOG_LOGO = 'blog_logo';
}
