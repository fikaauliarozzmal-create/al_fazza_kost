<?php

namespace App\Providers;

use App\Models\Pengaduan;
use Illuminate\Support\Facades\View;
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
        View::composer('admin.partials.sidebar', function ($view): void {
            $view->with('keluhanBaruCount', Pengaduan::where('status', 'Baru')->count());
        });
    }
}
