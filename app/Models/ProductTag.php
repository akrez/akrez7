<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductTag
 *
 * @property int $id
 * @property int $blog_id
 * @property int $product_id
 * @property string $tag_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductTag extends Model
{
    protected $table = 'product_tags';

    protected $casts = [
        'blog_id' => 'int',
        'product_id' => 'int',
    ];

    protected $fillable = [
        'blog_id',
        'product_id',
        'tag_name',
    ];

    protected static function boot()
    {
        parent::boot();
        static::blogUpdatedboot();
    }

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query->orderBy('created_at', 'ASC');
    }
}
