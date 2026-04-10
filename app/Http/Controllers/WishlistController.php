<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class WishlistController extends Controller
{

    public function index()
    {
        $items = auth()
            ->user()
            ->wishlist()
            ->latest()
            ->paginate(8);

        return view(
            'dashboard.buyer.wishlist.index',
            compact('items')
        );
    }


    


    public function toggle(Product $product)
{
    $user = auth()->user();

    if ($user->wishlist()->where('product_id', $product->id)->exists()) {

        $user->wishlist()->detach($product->id);

        $status = 'removed';

    } else {

        $user->wishlist()->attach($product->id);

        $status = 'added';

    }

    return response()->json([
        'status' => $status,
        'count' => $user->wishlist()->count()
    ]);
}

public function count()
{
    return response()->json([
        'count' => auth()->user()
            ->wishlist()
            ->count()
    ]);
}

}
