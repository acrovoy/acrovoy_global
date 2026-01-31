<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HelpCategory;

class HelpCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Buyers', 'slug' => 'buyers'],
            ['name' => 'Sellers', 'slug' => 'sellers'],
            ['name' => 'Payments', 'slug' => 'payments'],
            ['name' => 'Shipping', 'slug' => 'shipping'],
            ['name' => 'Account & Profile', 'slug' => 'account-profile'],
            ['name' => 'Orders', 'slug' => 'orders'],
        ];

        foreach ($categories as $category) {
            HelpCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
