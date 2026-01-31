<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuyerCartController extends Controller
{
    /**
     * Show buyer cart (TEST DATA)
     */
    public function index()
    {
        // 🧪 Тестовые данные корзины
        $cartItems = collect([
            (object) [
                'id' => 1,
                'product_name' => 'Wooden Chair',
                'quantity' => 2,
                'price' => 120,
            ],
            (object) [
                'id' => 2,
                'product_name' => 'Oak Table',
                'quantity' => 1,
                'price' => 340,
            ],
        ]);

        $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

        return view('dashboard.buyer.cart', compact('cartItems', 'total'));
    }

    /**
     * Remove item from cart (UI stub)
     */

    public function update(Request $request, $id)
{
    // TEST: позже заменится на session / DB
    // сейчас просто редирект

    return redirect()
        ->route('buyer.cart')
        ->with('success', 'Cart updated');
}


    public function destroy($id)
    {
        // позже будет логика удаления из session / DB
        return redirect()
            ->route('buyer.cart')
            ->with('success', 'Item removed from cart');
    }


    


}
