<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $casts = [
        'blog_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'blog_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::blogUpdatedboot();
    }
}
