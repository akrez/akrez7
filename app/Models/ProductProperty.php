<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductProperty
 *
 * @property int $id
 * @property int $blog_id
 * @property int $product_id
 * @property string|null $property_key
 * @property string $property_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductProperty extends Model
{
    protected $table = 'product_properties';

    protected $casts = [
        'blog_id' => 'int',
        'product_id' => 'int',
    ];

    protected $fillable = [
        'blog_id',
        'product_id',
        'property_key',
        'property_value',
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
