<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'supplier_id' => 1,
                'category_id' => 1,
                'name' => 'Rattan Lounge Sofa',
                'undername' => 'Outdoor sofa',
                'description' => 'Comfortable rattan lounge sofa for garden and patio.',
                'moq' => '5 pcs',
                'lead_time' => '25–30 days',
                'customization' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_id' => 2,
                'category_id' => 2,
                'name' => 'Glass Coffee Table',
                'undername' => 'Modern design',
                'description' => 'Stylish glass coffee table with metal frame.',
                'moq' => '10 pcs',
                'lead_time' => '20–25 days',
                'customization' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
