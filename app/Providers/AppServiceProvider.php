<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Bawaan Laravel merender paginasi dengan markup Tailwind. Proyek ini
        // memakai Bootstrap lewat CDN, jadi paginator diarahkan ke view Bootstrap 5.
        Paginator::useBootstrapFive();
    }
}
