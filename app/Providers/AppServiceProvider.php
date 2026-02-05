<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

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
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $setting = \App\Models\Setting::first();
                if (!$setting) {
                     // Fallback in case migration ran but no seed, though migration inserts it.
                     // Just an empty object or defaults to avoid errors.
                    $setting = new \App\Models\Setting([
                        'site_name' => 'Papion System',
                        'primary_color' => '#7367F0',
                        'secondary_color' => '#EA5455',
                    ]);
                }
                \Illuminate\Support\Facades\View::share('site_settings', $setting);
            }
        } catch (\Exception $e) {
            // Failsafe for initial migrations
        }

        Schema::defaultStringLength(191);

        Carbon::setLocale('ar');

        // Register Cart Composer
        \Illuminate\Support\Facades\View::composer('components.navbar', \App\Http\ViewComposers\CartComposer::class);
        
        // Register Notification Composer
        \Illuminate\Support\Facades\View::composer('components.navbar', \App\Http\ViewComposers\NotificationComposer::class);
    }
}
