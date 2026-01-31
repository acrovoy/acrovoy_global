<?php

namespace App\Providers;
use App\Models\Product;
use App\Models\ShippingTemplate;
use App\Models\Order;
use App\Models\Rfq;
use App\Models\RfqOffer;

use App\Policies\ProductPolicy;
use App\Policies\ShippingTemplatePolicy;
use App\Policies\OrderPolicy;
use App\Policies\RfqPolicy;
use App\Policies\RfqOfferPolicy;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        ShippingTemplate::class => ShippingTemplatePolicy::class,
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        Rfq::class => RfqPolicy::class,
        RfqOffer::class => RfqOfferPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
