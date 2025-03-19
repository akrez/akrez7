<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait ScopeDefaultTrait
{
    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query->orderBy('id', 'desc');
    }

    public static function getClassName($remove = '')
    {
        return Str::remove($remove, class_basename(static::class));
    }
}
