<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class SlugGeneratorService
{
    public function generate(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $i = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
}