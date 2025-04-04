<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class Model extends BaseModel
{
    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query->orderBy('updated_at', 'desc');
    }

    public static function getClassName($remove = '')
    {
        return Str::remove($remove, class_basename(static::class));
    }
}
