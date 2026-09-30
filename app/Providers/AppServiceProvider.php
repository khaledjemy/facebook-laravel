<?php

namespace App\Providers;

use App\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('site_settings')) {
            config(['app.name' => SiteSetting::getValue('site_name', config('app.name'))]);
        }

        View::composer('*', function ($view) {
            $settings = Schema::hasTable('site_settings')
                ? SiteSetting::allWithDefaults()
                : SiteSetting::DEFAULTS;
            $view->with('siteSettings', $settings);
        });
    }
}
