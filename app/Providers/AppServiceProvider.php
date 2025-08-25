<?php

namespace App\Providers;

use App\Services\CartServices;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartServices::class, function () {
            return new CartServices();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // we are gonna define schedule here for the payout command to vendors
        Schedule::command('payout:vendors')->monthlyOn(1, '00:00')->withoutOverlapping();
        Vite::prefetch(concurrency: 3);
    }
}
