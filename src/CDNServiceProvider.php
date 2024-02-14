<?php

namespace TheWebbakery\CDN;

use Illuminate\Contracts\Foundation\Application;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class CDNServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->offerPublishing();

        Storage::extend(config('cdn.storage_driver_name'), function (Application $app, array $config) {
            $adapter = new CDNAdapter(new CDNClient(
                $config['id'] ?? null, $config['secret'] ?? null
            ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
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
