<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Media\Infrastructure\Storage\LocalStorageAdapter;
use App\Domain\Media\Infrastructure\Storage\CloudflareStorageAdapter;
use App\Domain\Media\Contracts\StorageInterface;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
{
    $driver = config('media.storage_driver');

$map = [
    'local' => LocalStorageAdapter::class,
    'cloudflare' => CloudflareStorageAdapter::class,
];

$this->app->singleton(
    StorageInterface::class,
    $map[$driver]
);
}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
