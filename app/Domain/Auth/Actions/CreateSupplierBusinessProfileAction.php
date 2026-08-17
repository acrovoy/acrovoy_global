<?php

namespace App\Domain\Auth\Actions;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Str;

class CreateSupplierBusinessProfileAction
{
    public function execute(User $user): Supplier
    {
        $name = $user->name ?: 'Supplier';

        $slug = Str::slug($name);

        // На случай, если такой slug уже существует
        $originalSlug = $slug;
        $counter = 1;

        while (Supplier::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return Supplier::create([
            'user_id' => $user->id,

            'supplierable_type' => User::class,
            'supplierable_id'   => $user->id,

            'name' => $name,
            'slug' => $slug,

            'email' => $user->email,

            'status' => 'active',
            'is_verified' => false,
            'is_trusted' => false,
            'is_premium' => false,
        ]);
    }
}