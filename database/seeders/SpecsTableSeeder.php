<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('specifications')->insert([
            [
                'product_id' => 1,
                'key' => 'Material',
                'value' => 'Rattan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'key' => 'Dimensions',
                'value' => '200x80x75 cm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'key' => 'Material',
                'value' => 'Glass & Metal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

