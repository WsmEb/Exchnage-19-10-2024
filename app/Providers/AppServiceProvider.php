<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Facades\Blade;
use App\Models\Devise;

class AppServiceProvider extends ServiceProvider
{
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

       Blade::directive('ConvertedSymbolBase', function ($response) {
        return "<?php echo ConvertedSymbolBase($response); ?>";
        });

        PaginationPaginator::useBootstrapFive();
    }
}
