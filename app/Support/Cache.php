<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache as BaseCache;

class Cache extends BaseCache
{
    public static function keyShowSummary($blogId)
    {
        return "summaries:{$blogId}:show";
    }
}
