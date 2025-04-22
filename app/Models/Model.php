<?php

namespace App\Models;

use App\Services\SummaryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class Model extends BaseModel
{
    protected static function blogUpdatedboot()
    {
        static::created(function ($model) {
            SummaryService::new()->forgetCachedApiResponse($model->blog_id);
        });
        static::updated(function ($model) {
            SummaryService::new()->forgetCachedApiResponse($model->blog_id);
        });
        static::deleted(function ($model) {
            SummaryService::new()->forgetCachedApiResponse($model->blog_id);
        });
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                SummaryService::new()->forgetCachedApiResponse($model->blog_id);
            });
        }
        if (method_exists(static::class, 'forceDeleted')) {
            static::forceDeleted(function ($model) {
                SummaryService::new()->forgetCachedApiResponse($model->blog_id);
            });
        }
    }

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query->orderBy('updated_at', 'desc');
    }

    public static function getClassName($remove = '')
    {
        return Str::remove($remove, class_basename(static::class));
    }
}
