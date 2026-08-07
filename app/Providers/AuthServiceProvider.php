<?php

namespace App\Providers;
use App\Models\Product;
use App\Models\ShippingTemplate;
use App\Models\Order;
use App\Models\Rfq;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Models\Warehouse;
use App\Models\User;


use App\Policies\ProductPolicy;
use App\Policies\ShippingTemplatePolicy;
use App\Policies\OrderPolicy;
use App\Policies\RfqPolicy;
use App\Domain\Negotiation\Policies\RfqOfferPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\TeamPolicy;

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
        Warehouse::class => WarehousePolicy::class,
        ShippingTemplate::class => ShippingTemplatePolicy::class,
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        
        User::class => TeamPolicy::class,
        RfqOffer::class => RfqOfferPolicy::class,

    
        \App\Domain\Negotiation\Policies\RfqOfferParticipantPolicy::class,
        \App\Domain\Negotiation\Models\RfqOfferVersion::class
        => \App\Domain\Negotiation\Policies\RfqOfferVersionPolicy::class,
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
