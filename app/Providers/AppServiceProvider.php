<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\Language;

use Illuminate\Support\ServiceProvider;

use App\Domain\Media\Services\MediaProcessingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация в контейнере под алиасом media.processor
        $this->app->singleton('media.processor', function ($app) {
            return new MediaProcessingService();
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('supplier.certificate-card', \App\View\Components\Supplier\CertificateCard::class);
        Category::observe(CategoryObserver::class);
       
    }
}
