<?php

namespace App\Models;

use App\Enums\PackageStatusEnum;
use App\Traits\ScopeDefaultTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Package
 *
 * @property int $id
 * @property PackageStatusEnum $package_status
 * @property float $price
 * @property int $color_id
 * @property int $blog_id
 * @property int $product_id
 * @property string|null $guaranty
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Package extends Model
{
    use ScopeDefaultTrait, SoftDeletes;

    protected $table = 'packages';

    protected $casts = [
        'price' => 'float',
        'color_id' => 'int',
        'blog_id' => 'int',
        'product_id' => 'int',
        'package_status' => PackageStatusEnum::class,
    ];

    protected $fillable = [
        'package_status',
        'price',
        'color_id',
        'blog_id',
        'product_id',
        'guaranty',
        'description',
    ];
}
