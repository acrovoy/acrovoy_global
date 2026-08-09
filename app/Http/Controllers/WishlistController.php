<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;

use App\Services\Company\ActiveContextService;

class WishlistController extends Controller
{
    public function __construct(
        private readonly ActiveContextService $context
    ) {
       
    }

    /**
     * WISHLIST
     */
    public function index()
    {

            $entity = $this->context->buyerProfile();

        abort_unless($entity, 403);

        $items = Wishlist::query()
            ->where('buyer_type', $entity::class)
            ->where('buyer_id', $entity->getKey())
            ->with('product')
            ->latest()
            ->paginate(8);

        return view(
            'dashboard.buyer.wishlist.index',
            compact('items')
        );
    }

    /**
     * TOGGLE
     */
    public function toggle(Product $product): JsonResponse
    {
        $entity = $this->context->buyerProfile();

        abort_unless($entity, 403);

        $query = Wishlist::query()
            ->where('buyer_type', $entity::class)
            ->where('buyer_id', $entity->getKey())
            ->where('product_id', $product->id);

        $wishlist = $query->first();

        if ($wishlist) {

            $wishlist->delete();

            $status = 'removed';

        } else {

            Wishlist::firstOrCreate(
                [
                    'buyer_type' => $entity::class,
                    'buyer_id' => $entity->getKey(),
                    'product_id' => $product->id,
                ],
                [
                    'created_by' => auth()->id(),
                ]
            );

            $status = 'added';
        }

        $count = Wishlist::query()
            ->where('buyer_type', $entity::class)
            ->where('buyer_id', $entity->getKey())
            ->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
        ]);
    }

    /**
     * COUNT
     */
    public function count(): JsonResponse
    {
        $entity = $this->context->buyerProfile();

        abort_unless($entity, 403);

        $count = Wishlist::query()
            ->where('buyer_type', $entity::class)
            ->where('buyer_id', $entity->getKey())
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}