<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class OrderReviewController extends Controller
{
    public function create(Order $order)
    {
        $this->authorize('review', $order); // Проверка, что заказ принадлежит текущему пользователю

        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Вы можете оставлять отзывы только после завершения заказа.');
        }

        return view('buyer.orders.review', compact('order'));
    }

    public function store(Request $request, Order $order)
{
    $this->authorize('review', $order);

    if ($order->status !== 'completed') {
        return redirect()->back()->with('error', 'Невозможно оставить отзыв для незавершенного заказа.');
    }

    // Валидация массивов rating, match_rating и comment
    $request->validate([
        'rating.*' => 'required|integer|min:1|max:5',
        'match_rating.*' => 'required|integer|min:1|max:5', // новое поле
        'comment.*' => 'nullable|string|max:1000',
    ]);

    // Проходимся по всем товарам заказа и сохраняем отзыв на каждый товар
   foreach ($order->items as $item) {

        // Определяем тип отзыва и product_id
        if ($item->product_id) {
            $type = 'product';
            $productId = $item->product_id;
        } else {
            $type = 'rfq';
            $productId = null;
        }

        // Проверяем, есть ли уже отзыв от этого пользователя для этого товара/заказа
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('order_id', $order->id)
                                ->where('product_id', $productId)
                                ->where('type', $type)
                                ->first();

        if ($existingReview) {
            continue; // если отзыв уже есть, пропускаем
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'order_id' => $order->id,
            'type' => $type,
            'rating' => $request->input('rating')[$item->id],
            'match_rating' => $request->input('match_rating')[$item->id],
            'comment' => $request->input('comment')[$item->id] ?? null,
        ]);
    }

    // Можно тут же обновить репутацию продавца
    // $order->items->each(fn($item) => $item->product->supplier->recalculateReputation());

    return redirect()->route('buyer.orders.show', $order->id)
                     ->with('success', 'Ваши отзывы успешно добавлены!');
}

}