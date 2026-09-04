<?php

namespace App\Models;

use App\Enums\CategoryStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property CategoryStatusEnum $status
 * @property float|null $category_order
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $casts = [
        'blog_id' => 'int',
        'status' => CategoryStatusEnum::class,
        'category_order' => 'float',
    ];

    protected $fillable = [
        'name',
        'status',
        'category_order',
        'blog_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::blogUpdatedboot();
    }

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderBy('category_order', 'DESC')
            ->orderBy('name', 'ASC')
            ->orderBy('created_at', 'ASC');
    }
}
