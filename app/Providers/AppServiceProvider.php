<?php

namespace App\Providers;

use Carbon\Carbon;
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
        // Tanggal & nama bulan mengikuti bahasa aplikasi (APP_LOCALE=id),
        // jadi tampil "17 September 2026", bukan "September 17, 2026".
        Carbon::setLocale(config('app.locale'));
    }
}
