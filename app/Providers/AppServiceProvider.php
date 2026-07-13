<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;



class AppServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        Carbon::setLocale('uz_Latn');
    }
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
}
