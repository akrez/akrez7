<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache as BaseCache;

class Cache extends BaseCache
{
    const KEY_SUMMARIES = 'summaries';
}
