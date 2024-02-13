<?php

namespace TheWebbakery\CDN;

use Illuminate\Support\ServiceProvider;

class CDNServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->offerPublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cdn.php',
            'cdn'
        );
    }

    public function offerPublishing(): void
    {
        if (!function_exists('config_path'))
        {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/cdn.php' => config_path('cdn.php'),
        ], 'cdn-config');
    }
}
