<?php

namespace App\Providers;

use App\Support\ActiveBlog;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/blogs/index';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (0) {
            DB::listen(function (QueryExecuted $query) {
                info(($query->time > 1000 ? '🔴' : '🔵')."\t".$query->toRawSql());
            });
        }
        $this->app->singleton('ActiveBlog', function () {
            return new ActiveBlog;
        });
        $this->app->alias('Arr', Arr::class);
        //
        Blade::directive('spaceless', function () {
            return '<?php ob_start(); ob_implicit_flush(false); ?>';
        });
        Blade::directive('endspaceless', function () {
            return "<?php echo trim(preg_replace('/>\s+</', '><', ob_get_clean())); ?>";
        });
        //
        Paginator::useBootstrapFive();
    }
}
