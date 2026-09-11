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

        if ($this->app->environment('local')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(
                request()->getSchemeAndHttpHost()
            );
        }
    }
    
    public function register(): void
    {

    }

    
}
