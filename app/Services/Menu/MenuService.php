<?php

namespace App\Services\Menu;

use App\Models\User;

class MenuService
{
    public static function get(string $context, array $metrics = [])
    {
        return match ($context) {
            'buyer'              => self::buyer($metrics),
            'supplier'           => self::supplier($metrics),
            'supplier_individual'=> self::supplierIndividual($metrics),
            'buyer_individual'   => self::buyerIndividual($metrics),
            'logistics'          => self::logistics($metrics),

            default => [],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | CORPORATE BUYER
    |--------------------------------------------------------------------------
    */

    private static function buyer($m)
    {
        return [
            // Overview
            ['type' => 'header', 'label' => 'Overview'],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'route' => 'dashboard.home',
            ],

            // Purchasing
            ['type' => 'header', 'label' => 'Purchasing'],
            [
                'type' => 'link',
                'label' => 'My Orders',
                'route' => 'buyer.orders.index',
                'badge' => $m['openDisputeCount'] ?? null,
            ],
            [
                'type' => 'link',
                'label' => 'RFQs',
                'route' => 'buyer.rfqs.index',
                'badge' => $m['newOfferCount'] ?? null,
            ],
            [
                'type' => 'link',
                'label' => 'Projects',
                'route' => 'buyer.projects.index',
            ],

            // Purchasing Tools
            ['type' => 'header', 'label' => 'Purchasing Tools'],
            [
                'type' => 'link',
                'label' => 'Cart',
                'route' => 'buyer.cart.index',
            ],
            [
                'type' => 'link',
                'label' => 'Wishlist',
                'route' => 'buyer.wishlist.index',
            ],
            [
                'type' => 'link',
                'label' => 'Saved Suppliers',
                'route' => 'buyer.saved-suppliers.index',
            ],
            [
                'type' => 'link',
                'label' => 'Delivery Addresses',
                'route' => 'buyer.addresses.index',
            ],

            // Communication
            ['type' => 'header', 'label' => 'Communication'],
            [
                'type' => 'link',
                'label' => 'Messages',
                'route' => 'buyer.messenger.index',
            ],

            // Company
            ['type' => 'header', 'label' => 'Company'],
            [
                'type' => 'link',
                'label' => 'Company Profile',
                'route' => 'buyer.profile.show',
            ],

            // Team
            ['type' => 'header', 'label' => 'Team'],
            [
                'type' => 'link',
                'label' => 'Team Members',
                'route' => 'supplier.team.members',
                'can' => ['viewAny', User::class],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CORPORATE SUPPLIER
    |--------------------------------------------------------------------------
    */

    private static function supplier($m)
    {
        return [
            // Overview
            ['type' => 'header', 'label' => 'Overview'],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'route' => 'dashboard.home',
            ],

            // Sales
            ['type' => 'header', 'label' => 'Sales'],
            [
                'type' => 'link',
                'label' => 'Orders',
                'route' => 'supplier.orders',
                'badge' => $m['openDisputeCount'] ?? null,
                'can' => ['salesAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'RFQs',
                'route' => 'supplier.rfqs.index',
                'badge' => $m['acceptedOfferCount'] ?? null,
                'can' => ['salesAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Project RFQs',
                'route' => 'supplier.projects.index',
                'can' => ['salesAccess', User::class],
            ],

            // Product Catalog
            ['type' => 'header', 'label' => 'Product Catalog'],
            [
                'type' => 'link',
                'label' => 'Add Product',
                'route' => 'supplier.products.create',
            ],
            [
                'type' => 'link',
                'label' => 'Product List',
                'route' => 'supplier.products.index',
                'can' => ['salesAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Materials & Colors',
                'route' => 'supplier.materials.index',
                'can' => ['salesAccess', User::class],
            ],

            // Fulfillment
            ['type' => 'header', 'label' => 'Fulfillment'],
            [
                'type' => 'link',
                'label' => 'Shipping Center',
                'route' => 'supplier.shipping-templates.index',
                'can' => ['logisticsAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Payments',
                'route' => 'supplier.payments.index',
            ],

            [
                'type' => 'link',
                'label' => 'Return Policies',
                'route' => 'supplier.return-policies.index',
            ],

           
            

            // Company
            ['type' => 'header', 'label' => 'Company'],
            [
                'type' => 'link',
                'label' => 'Company Profile',
                'route' => 'supplier.profile.show',
            ],

            // Team
            ['type' => 'header', 'label' => 'Team'],
            [
                'type' => 'link',
                'label' => 'Team Overview',
                'route' => 'supplier.team.index',
                'can' => ['viewAny', User::class],
            ],

            [
                'type' => 'link',
                'label' => 'Team Members',
                'route' => 'supplier.team.members',
                'can' => ['viewAny', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Invite Users',
                'route' => 'supplier.team.invite',
                'can' => ['invite', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Roles & Permissions',
                'route' => 'supplier.team.roles',
                'can' => ['manageRoles', User::class],
            ],

            // Communication
            ['type' => 'header', 'label' => 'Communication'],
            [
                'type' => 'link',
                'label' => 'Messages',
                'route' => 'supplier.messenger.index',
            ],

            // Billing
            ['type' => 'header', 'label' => 'Billing'],
            [
                'type' => 'link',
                'label' => 'Premium Plans',
                'route' => 'supplier.premium-plans',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGISTICS COMPANY
    |--------------------------------------------------------------------------
    */

    private static function logistics($m)
    {
        return [
            // Overview
            ['type' => 'header', 'label' => 'Overview'],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'route' => 'logistics.home',
            ],

            // Operations
            ['type' => 'header', 'label' => 'Operations'],
            [
                'type' => 'link',
                'label' => 'Shipping Templates',
                'route' => 'logistics.templates.index',
                'can' => ['logisticsAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Offers',
                'route' => 'logistics.offers.index',
                'can' => ['logisticsAccess', User::class],
            ],

            // Company
            ['type' => 'header', 'label' => 'Company'],
            [
                'type' => 'link',
                'label' => 'Company Profile',
                'route' => 'logistics.company.show',
            ],

            // Settings
            ['type' => 'header', 'label' => 'Settings'],
            [
                'type' => 'link',
                'label' => 'Settings',
                'route' => 'logistics.settings',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL SUPPLIER
    |--------------------------------------------------------------------------
    */

    private static function supplierIndividual($m)
    {
        return [
            // Overview
            ['type' => 'header', 'label' => 'Overview'],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'route' => 'dashboard.home',
            ],

            // Sales
            ['type' => 'header', 'label' => 'Sales'],
            [
                'type' => 'link',
                'label' => 'Orders',
                'route' => 'supplier.orders',
                'badge' => $m['openDisputeCount'] ?? null,
            ],
            [
                'type' => 'link',
                'label' => 'RFQs',
                'route' => 'supplier.rfqs.index',
                'badge' => $m['acceptedOfferCount'] ?? null,
            ],
            [
                'type' => 'link',
                'label' => 'Project RFQs',
                'route' => 'supplier.projects.index',
            ],

            // Product Catalog
            ['type' => 'header', 'label' => 'Product Catalog'],
            [
                'type' => 'link',
                'label' => 'Add Product',
                'route' => 'supplier.products.create',
            ],
            [
                'type' => 'link',
                'label' => 'Product List',
                'route' => 'supplier.products.index',
            ],
            [
                'type' => 'link',
                'label' => 'Materials & Colors',
                'route' => 'supplier.materials.index',
            ],

            // Fulfillment
            ['type' => 'header', 'label' => 'Fulfillment'],
            [
                'type' => 'link',
                'label' => 'Shipping Center',
                'route' => 'supplier.shipping-templates.index',
            ],
            [
                'type' => 'link',
                'label' => 'Return Policies',
                'route' => 'supplier.return-policies.index',
            ],

            // Business Profile
            ['type' => 'header', 'label' => 'Business Profile'],
            [
                'type' => 'link',
                'label' => 'Business Profile',
                'route' => 'supplier.profile.show',
            ],

            // Companies
            ['type' => 'header', 'label' => 'Company'],
            [
                'type' => 'link',
                'label' => 'My Companies',
                'route' => 'dashboard.companies.index',
            ],
            [
                'type' => 'link',
                'label' => 'Create Company',
                'route' => 'dashboard.companies.create',
            ],

            // Communication
            ['type' => 'header', 'label' => 'Communication'],
            [
                'type' => 'link',
                'label' => 'Messages',
                'route' => 'supplier.messenger.index',
            ],

            // Billing
            ['type' => 'header', 'label' => 'Billing'],
            [
                'type' => 'link',
                'label' => 'Premium Plans',
                'route' => 'supplier.premium-plans',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL BUYER
    |--------------------------------------------------------------------------
    */

    private static function buyerIndividual($m)
    {
        return [
            // Overview
            ['type' => 'header', 'label' => 'Overview'],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'route' => 'dashboard.home',
            ],

            // Communication
            ['type' => 'header', 'label' => 'Communication'],
            [
                'type' => 'link',
                'label' => 'Messages',
                'route' => 'buyer.messenger.index',
            ],
            

            // Purchasing
            ['type' => 'header', 'label' => 'Purchasing'],
            [
                'type' => 'link',
                'label' => 'My Orders',
                'route' => 'buyer.orders.index',
                'badge' => $m['openDisputeCount'] ?? null,
            ],
            [
                'type' => 'link',
                'label' => 'RFQs',
                'route' => 'buyer.rfqs.index',
            ],
            [
                'type' => 'link',
                'label' => 'Projects',
                'route' => 'buyer.projects.index',
            ],

            // Purchasing Tools
            ['type' => 'header', 'label' => 'Purchasing Tools'],
            [
                'type' => 'link',
                'label' => 'Cart',
                'route' => 'buyer.cart.index',
            ],
            [
                'type' => 'link',
                'label' => 'Wishlist',
                'route' => 'buyer.wishlist.index',
            ],
            [
                'type' => 'link',
                'label' => 'Saved Suppliers',
                'route' => 'buyer.saved-suppliers.index',
            ],
            [
                'type' => 'link',
                'label' => 'Delivery Addresses',
                'route' => 'buyer.addresses.index',
            ],

            // Business Profile
            ['type' => 'header', 'label' => 'Business Profile'],
            [
                'type' => 'link',
                'label' => 'Business Profile',
                'route' => 'buyer.profile.show',
            ],

            // Companies
            ['type' => 'header', 'label' => 'Company'],
            [
                'type' => 'link',
                'label' => 'My Companies',
                'route' => 'dashboard.companies.index',
            ],
            [
                'type' => 'link',
                'label' => 'Create Company',
                'route' => 'dashboard.companies.create',
            ],

            
        ];
    }
}
