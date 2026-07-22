<?php

namespace App\Providers;

use App\Models\BaseModel;
use App\Observers\BaseModelObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

//    	URL::forceRootUrl($request->getSchemeAndHttpHost());
        // if (env('APP_ENV') != 'local') {
             //URL::forceScheme('https');
        // }

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, BaseModel::class)) {
                $class::observe(BaseModelObserver::class);
            }
        }
    }
}
