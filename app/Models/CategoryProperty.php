<?php

namespace App\Models;

use App\Enums\CategoryPropertyTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryProperty
 *
 * @property int $id
 * @property int $blog_id
 * @property int|null $category_id
 * @property string $name
 * @property CategoryPropertyTypeEnum $filter_type
 * @property string|null $unit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CategoryProperty extends Model
{
    protected $table = 'category_properties';

    protected $casts = [
        'blog_id' => 'int',
        'category_id' => 'int',
        'filter_type' => CategoryPropertyTypeEnum::class,
    ];
    protected $fillable = [
        'blog_id',
        'category_id',
        'name',
        'filter_type',
        'unit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::blogUpdatedboot();
    }

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderByRaw('category_id IS NULL')
            ->orderBy('category_id', 'ASC')
            ->orderBy('name', 'ASC')
            ->orderBy('created_at', 'ASC');
    }
}
