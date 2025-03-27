<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Traits\ScopeDefaultTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property int $blog_id
 * @property ProductStatusEnum $product_status
 * @property float|null $product_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Product extends Model
{
    use HasFactory, ScopeDefaultTrait;

    protected $table = 'products';

    protected $casts = [
        'blog_id' => 'int',
        'product_order' => 'float',
        'product_status' => ProductStatusEnum::class,
    ];

    protected $fillable = [
        'name',
        'code',
        'blog_id',
        'product_status',
        'product_order',
    ];

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderBy('product_order', 'DESC')
            ->orderBy('name', 'ASC')
            ->orderBy('created_at', 'ASC');
    }
}
