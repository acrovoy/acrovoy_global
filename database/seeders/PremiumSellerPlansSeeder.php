<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PremiumSellerPlan;

class PremiumSellerPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => '$0',
                'popular' => false,
                'features' => [
                    'Products limit' => '3',
                    'Product visibility' => 'Basic',
                    'Analytics' => 'No',
                    'Priority placement' => 'No',
                    'Support' => 'Community',
                ],
            ],
            [
                'name' => 'Starter',
                'price' => '$9 / month',
                'popular' => false,
                'features' => [
                    'Products limit' => '10',
                    'Product visibility' => 'Standard',
                    'Analytics' => 'Basic',
                    'Priority placement' => 'No',
                    'Support' => 'Email',
                ],
            ],
            [
                'name' => 'Professional',
                'price' => '$29 / month',
                'popular' => true,
                'features' => [
                    'Products limit' => '100',
                    'Product visibility' => 'High',
                    'Analytics' => 'Advanced',
                    'Priority placement' => 'Yes',
                    'Support' => 'Priority',
                ],
            ],
            [
                'name' => 'Enterprise',
                'price' => '$99 / month',
                'popular' => false,
                'features' => [
                    'Products limit' => 'Unlimited',
                    'Product visibility' => 'Top',
                    'Analytics' => 'Full',
                    'Priority placement' => 'Yes',
                    'Support' => 'Dedicated manager',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            PremiumSellerPlan::create($plan);
        }
    }
}
