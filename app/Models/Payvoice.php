<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Payvoice
 *
 * @property int $id
 * @property string|null $ip
 * @property string|null $method
 * @property string|null $controller
 * @property string|null $useragent_device
 * @property string|null $useragent_platform
 * @property string|null $useragent_browser
 * @property string|null $useragent_robot
 * @property string|null $useragent
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Payvoice extends Model
{
    protected $table = 'payvoices';

    protected $casts = [
        'blog_id' => 'int',
    ];

    protected $fillable = [
        'ip',
        'method',
        'controller',
        'useragent_device',
        'useragent_platform',
        'useragent_browser',
        'useragent_robot',
        'useragent',
        'blog_id',
    ];
}
