<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('suppliers')->insert([
            ['name' => 'Supplier One', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supplier Two', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
