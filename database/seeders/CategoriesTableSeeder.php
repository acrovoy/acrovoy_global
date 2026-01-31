<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Корневые категории
        $furnitureId = DB::table('categories')->insertGetId([
            'name' => 'Furniture',
            'slug' => Str::slug('Furniture'),
            'parent_id' => null,
            'level' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $decorId = DB::table('categories')->insertGetId([
            'name' => 'Decor',
            'slug' => Str::slug('Decor'),
            'parent_id' => null,
            'level' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Подкатегории
        $chairsId = DB::table('categories')->insertGetId([
            'name' => 'Chairs',
            'slug' => Str::slug('Chairs'),
            'parent_id' => $furnitureId,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vasesId = DB::table('categories')->insertGetId([
            'name' => 'Vases',
            'slug' => Str::slug('Vases'),
            'parent_id' => $decorId,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Под-подкатегории
        DB::table('categories')->insert([
            [
                'name' => 'Armchairs',
                'slug' => Str::slug('Armchairs'),
                'parent_id' => $chairsId,
                'level' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Decorative Vases',
                'slug' => Str::slug('Decorative Vases'),
                'parent_id' => $vasesId,
                'level' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

