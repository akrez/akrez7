<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\GalleryCategoryEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
}
