<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PriceTier;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('product')
            ->get();

        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        return view('dashboard.buyer.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $user = auth()->user();

        /** 1. MOQ продукта */
        $moq = $product->moq; // например поле moq в products

        /** 2. Ищем подходящий ценовой диапазон */
        $priceTier = PriceTier::where('product_id', $product->id)
            ->where('min_qty', '<=', $moq)
            ->where(function ($q) use ($moq) {
                $q->where('max_qty', '>=', $moq)
                  ->orWhereNull('max_qty');
            })
            ->orderBy('min_qty')
            ->first();

        if (! $priceTier) {
            return back()->withErrors('Price not found for MOQ');
        }

        /** 3. Если товар уже есть в корзине — не дублируем */
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            return back()->with('info', 'Product already in cart');
        }

        /** 4. Записываем в корзину */
        CartItem::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => $moq,
            'price'      => $priceTier->price,
        ]);

        return back()->with('success', 'Product added to cart');
    }

    public function update(Request $request, CartItem $cartItem)
{
    $action = $request->input('action');

    // Изменяем количество
    if ($action === 'increase') {
        $cartItem->quantity += 1;
    } elseif ($action === 'decrease' && $cartItem->quantity > 1) {
        $cartItem->quantity -= 1;
    }

    // Подбираем цену по новой количественной ступени
    $priceTier = $cartItem->product->priceTiers()
                       ->where('min_qty', '<=', $cartItem->quantity)
                       ->where(function($q) use ($cartItem) {
                           $q->where('max_qty', '>=', $cartItem->quantity)
                             ->orWhereNull('max_qty');
                       })
                       ->orderBy('min_qty', 'asc')
                       ->first();

    // Если цена для ступени не найдена, берём ближайшую доступную
    $cartItem->price = $priceTier->price
                        ?? $cartItem->product->priceTiers()->whereNotNull('price')->max('price')
                        ?? $cartItem->product->price
                        ?? 0;

    $cartItem->save();

    return redirect()->back()->with('success', 'Cart updated successfully');
}



    public function remove(CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $cartItem->delete();

        return back();
    }
}