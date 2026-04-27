<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Company\ActiveContextService;

class ActiveContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('active-context', function () {
            return new ActiveContextService();
        });
    }

    public function boot(): void
    {
        //
    }
}