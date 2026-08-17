<?php

namespace App\Domain\Auth\Actions;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Support\Str;

class CreateBuyerBusinessProfileAction
{
    public function execute(User $user): Buyer
    {
        $name = $user->name ?: 'Buyer';

        $slug = Str::slug($name);

        // На случай, если такой slug уже существует
        $originalSlug = $slug;
        $counter = 1;

        while (Buyer::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return Buyer::create([
            'user_id' => $user->id,

            'buyerable_type' => User::class,
            'buyerable_id'   => $user->id,

            'name' => $name,
            'slug' => $slug,

            'email' => $user->email,

            'status' => 'active',
            'is_verified' => false,
            'is_premium' => false,
            'reputation' => 0,
        ]);
    }
}