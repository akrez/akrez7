<?php

namespace App\Models;

use App\Enums\BlogStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Blog
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $short_description
 * @property string|null $description
 * @property int $created_by
 * @property string|null $blog_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 * @property Collection|User[] $users
 */
class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    protected $casts = [
        'blog_status' => BlogStatusEnum::class,
        'created_by' => 'int',
    ];

    protected $fillable = [
        'name',
        'short_description',
        'description',
        'created_by',
        'blog_status',
    ];
}
