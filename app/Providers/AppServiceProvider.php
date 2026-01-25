<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
                        'site_name' => 'Papion Print',
                        'primary_color' => '#7367F0',
                        'secondary_color' => '#EA5455',
                    ]);
                }
                \Illuminate\Support\Facades\View::share('site_settings', $setting);
            }
        } catch (\Exception $e) {
            // Failsafe for initial migrations
        }
    }
}
