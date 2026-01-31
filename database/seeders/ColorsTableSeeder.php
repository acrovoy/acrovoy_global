<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('colors')->insert([
            [
                'product_id' => 1,
                'color' => '#ffffff',
                'texture' => null,
                'linked_product_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'color' => '#000000',
                'texture' => null,
                'linked_product_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
