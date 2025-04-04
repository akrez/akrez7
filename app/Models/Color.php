<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Color
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Color extends Model
{
    protected $table = 'colors';

    protected $casts = [
        'blog_id' => 'int',
    ];

    protected $fillable = [
        'code',
        'name',
        'blog_id',
    ];
}
