<?php

namespace App\Http\Controllers;
use App\Facades\ActiveContext;


use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{

    public function index()
    {
         $items = Wishlist::query()
        ->where('buyer_type', ActiveContext::type())
        ->where('buyer_id', ActiveContext::id())
        ->with('product')
        ->latest()
        ->paginate(8);

        return view(
            'dashboard.buyer.wishlist.index',
            compact('items')
        );
    }


    


    public function toggle(Product $product)
{
    $query = Wishlist::query()
        ->where('buyer_type', ActiveContext::type())
        ->where('buyer_id', ActiveContext::id())
        ->where('product_id', $product->id);

    $wishlist = $query->first();

    if ($wishlist) {

        $wishlist->delete();

        $status = 'removed';

    } else {

      Wishlist::firstOrCreate([
    'buyer_type' => ActiveContext::type(),
    'buyer_id'   => ActiveContext::id(),
    'product_id' => $product->id,
], [
    'created_by' => auth()->id(),
]);

        $status = 'added';
    }

    return response()->json([
        'status' => $status,

        'count' => Wishlist::where('buyer_type', ActiveContext::type())
            ->where('buyer_id', ActiveContext::id())
            ->count()
    ]);
}

public function count()
{
    return response()->json([
        'count' => Wishlist::where('buyer_type', ActiveContext::type())
            ->where('buyer_id', ActiveContext::id())
            ->count()
    ]);
}

}
