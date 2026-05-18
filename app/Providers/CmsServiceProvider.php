<?php

namespace App\Providers;

use App\Services\SitemapService;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton(SitemapService::class, fn () => new SitemapService);
    }
}
