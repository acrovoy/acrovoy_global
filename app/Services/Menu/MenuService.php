<?php

namespace App\Services\Menu;

use App\Models\User;

class MenuService
{
    public static function get(string $context, array $metrics = [])
    {
        return match ($context) {

            'buyer' => self::buyer($metrics),
            'supplier' => self::supplier($metrics),
            'logistics' => self::logistics($metrics),

            default => [],
        };
    }

    private static function buyer($m)
    {
        return [
            ['type' => 'header', 'label' => 'Overview'],
            ['type' => 'link', 'label' => 'Dashboard', 'route' => 'dashboard.home'],

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
            ['type' => 'link', 'label' => 'Projects', 'route' => 'buyer.projects.index'],

            ['type' => 'header', 'label' => 'Shopping'],
            ['type' => 'link', 'label' => 'Cart', 'route' => 'buyer.cart.index'],
            ['type' => 'link', 'label' => 'Wishlist', 'route' => 'buyer.wishlist.index'],

            ['type' => 'header', 'label' => 'Communication'],
            ['type' => 'link', 'label' => 'Messages', 'route' => 'buyer.messages'],

            ['type' => 'header', 'label' => 'Account'],
            ['type' => 'link', 'label' => 'Billing', 'route' => 'manufacturer.home'],
            ['type' => 'link', 'label' => 'Settings', 'route' => 'manufacturer.home'],
        ];
    }

    /* =========================
     * SUPPLIER MENU (ONLY ACCESS FIXED)
     * ========================= */
    private static function supplier($m)
    {
        return [
            ['type' => 'header', 'label' => 'Overview'],
            ['type' => 'link', 'label' => 'Dashboard', 'route' => 'dashboard.home'],

            ['type' => 'header', 'label' => 'Sales'],

            [
                'type' => 'link',
                'label' => 'Orders',
                'route' => 'manufacturer.orders',
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
                'route' => 'supplier.rfqs.index',
                'can' => ['salesAccess', User::class],
            ],

            ['type' => 'header', 'label' => 'Products'],

            [
                'type' => 'link',
                'label' => 'Add Product',
                'route' => 'manufacturer.products.create',
                'can' => ['salesAccess', User::class],
            ],
            [
                'type' => 'link',
                'label' => 'Product List',
                'route' => 'manufacturer.products.index',
                'can' => ['salesAccess', User::class],
            ],

            ['type' => 'header', 'label' => 'Fulfillment'],

            [
                'type' => 'link',
                'label' => 'Shipping Center',
                'route' => 'supplier.shipping-templates.index',
                'can' => ['logisticsAccess', User::class],
            ],

            ['type' => 'header', 'label' => 'Team'],

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

            ['type' => 'header', 'label' => 'Company'],
            ['type' => 'link', 'label' => 'Company Profile', 'route' => 'supplier.company.show'],

            ['type' => 'header', 'label' => 'Communication'],
            ['type' => 'link', 'label' => 'Messages', 'route' => 'manufacturer.messages'],

            ['type' => 'header', 'label' => 'Billing'],
            ['type' => 'link', 'label' => 'Premium Plans', 'route' => 'manufacturer.premium-plans'],
        ];
    }

    private static function logistics($m)
    {
        return [
            ['type' => 'header', 'label' => 'Overview'],
            ['type' => 'link', 'label' => 'Dashboard', 'route' => 'logistics.home'],

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

            ['type' => 'header', 'label' => 'Company'],
            ['type' => 'link', 'label' => 'Company Profile', 'route' => 'logistics.company.show'],

            ['type' => 'header', 'label' => 'Settings'],
            ['type' => 'link', 'label' => 'Settings', 'route' => 'logistics.settings'],
        ];
    }
}