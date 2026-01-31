<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HelpArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $articles = [
            // Buyers
            [
                'title' => 'How to Place an Order',
                'slug' => Str::slug('How to Place an Order'),
                'content' => 'Step by step guide on placing orders on Acrovoy platform.',
                'category' => 'buyers',
                'published' => 1,
            ],
            [
                'title' => 'Tracking Your Orders',
                'slug' => Str::slug('Tracking Your Orders'),
                'content' => 'Learn how to track the status of your orders and deliveries.',
                'category' => 'buyers',
                'published' => 1,
            ],

            // Sellers
            [
                'title' => 'How to List Your Products',
                'slug' => Str::slug('How to List Your Products'),
                'content' => 'Step by step guide for sellers to add products to Acrovoy.',
                'category' => 'sellers',
                'published' => 1,
            ],
            [
                'title' => 'Managing Your Orders as a Seller',
                'slug' => Str::slug('Managing Your Orders as a Seller'),
                'content' => 'How to view and process orders from buyers.',
                'category' => 'sellers',
                'published' => 1,
            ],

            // Payments
            [
                'title' => 'Payment Methods',
                'slug' => Str::slug('Payment Methods'),
                'content' => 'Information about supported payment methods on Acrovoy.',
                'category' => 'payments',
                'published' => 1,
            ],
            [
                'title' => 'Invoice Management',
                'slug' => Str::slug('Invoice Management'),
                'content' => 'How to download and manage your invoices.',
                'category' => 'payments',
                'published' => 1,
            ],

            // Shipping
            [
                'title' => 'Shipping Options',
                'slug' => Str::slug('Shipping Options'),
                'content' => 'Learn about available shipping options for your orders.',
                'category' => 'shipping',
                'published' => 1,
            ],
            [
                'title' => 'Tracking Shipments',
                'slug' => Str::slug('Tracking Shipments'),
                'content' => 'Step by step guide to track your shipments in real time.',
                'category' => 'shipping',
                'published' => 1,
            ],

            // Account & Profile
            [
                'title' => 'Updating Your Profile',
                'slug' => Str::slug('Updating Your Profile'),
                'content' => 'How to update your profile information and preferences.',
                'category' => 'account-profile',
                'published' => 1,
            ],
            [
                'title' => 'Changing Your Password',
                'slug' => Str::slug('Changing Your Password'),
                'content' => 'Guide to safely change your account password.',
                'category' => 'account-profile',
                'published' => 1,
            ],

            // Orders
            [
                'title' => 'Order History',
                'slug' => Str::slug('Order History'),
                'content' => 'View all your past orders in one place.',
                'category' => 'orders',
                'published' => 1,
            ],
            [
                'title' => 'Resolving Order Issues',
                'slug' => Str::slug('Resolving Order Issues'),
                'content' => 'Steps to report and resolve issues with your orders.',
                'category' => 'orders',
                'published' => 1,
            ],
        ];

        DB::table('help_articles')->insert($articles);
    }
}
