<?php

namespace App\Models;

use App\Enums\GalleryCategoryEnum;
use App\Traits\ScopeDefaultTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Gallery
 *
 * @property string $name
 * @property string $gallery_type
 * @property int $gallery_id
 * @property int $blog_id
 * @property string $ext
 * @property ?GalleryCategoryEnum $gallery_category
 * @property float|null $gallery_order
 * @property Carbon|null $selected_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Blog $blog
 */
class Gallery extends Model
{
    use ScopeDefaultTrait;

    protected $table = 'galleries';

    protected $casts = [
        'gallery_id' => 'int',
        'blog_id' => 'int',
        'gallery_order' => 'float',
        'selected_at' => 'datetime',
        'gallery_category' => GalleryCategoryEnum::class,
    ];

    protected $fillable = [
        'name',
        'gallery_type',
        'gallery_id',
        'blog_id',
        'ext',
        'gallery_category',
        'gallery_order',
        'selected_at',
    ];

    public function scopeDefaultOrder(Builder $query)
    {
        $query = $query
            ->orderBy('gallery_type', 'ASC')
            ->orderBy('gallery_id', 'ASC')
            ->orderBy('selected_at', 'DESC')
            ->orderBy('gallery_order', 'DESC')
            ->orderBy('created_at', 'ASC');
    }
}
