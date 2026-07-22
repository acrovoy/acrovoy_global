<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

use App\View\Composers\NavigationComposer;

use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\Language;

use App\Domain\Conversation\Services\ConversationHeaderService;

use App\Domain\Conversation\Resolvers\ProductHeaderResolver;
use App\Domain\Conversation\Resolvers\SupportConversationHeaderResolver;
use App\Domain\Conversation\Contracts\ConversationHeaderResolver;

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

    $this->app->singleton(
        ConversationHeaderService::class,
        function ($app) {

            return new ConversationHeaderService([

                $app->make(ProductHeaderResolver::class),
                $app->make(SupportConversationHeaderResolver::class),

            ]);

        }
    );

    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('supplier.certificate-card', \App\View\Components\Supplier\CertificateCard::class);
        Category::observe(CategoryObserver::class);
        Paginator::useTailwind();
         View::composer('layouts.navigation', NavigationComposer::class);
       
    }
}
