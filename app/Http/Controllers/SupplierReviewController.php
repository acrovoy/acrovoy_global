<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\SupplierReview;
use Illuminate\Support\Facades\Auth;

class SupplierReviewController extends Controller
{
    public function create(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Оценивать продавца можно только после завершения заказа.');
        }

        return view('buyer.orders.supplier_review', compact('order'));
    }

    public function store(Request $request, Order $order)
{
    $this->authorize('view', $order);

    if ($order->status !== 'completed') {
        return redirect()->back()->with('error', 'Невозможно оставить отзыв для незавершенного заказа.');
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    
    
     // Подгружаем связи
    $order->load(['items.product', 'rfqOffer']);

    $firstItem = $order->items->first();
    $supplierId = null;

    // Попытка получить поставщика через продукт
    if ($firstItem?->product?->supplier_id) {
        $supplierId = $firstItem->product->supplier_id;
        

    // Попытка получить поставщика через RFQ
    } elseif ($order->rfqOffer?->supplier_id) {
        $supplierId = $order->rfqOffer->supplier_id;
       
    }



    // Проверяем, есть ли уже отзыв от этого пользователя для этого продавца и заказа
    $existingReview = SupplierReview::where('supplier_id', $supplierId)
                                    ->where('order_id', $order->id)
                                    ->where('user_id', Auth::id())
                                    ->first();

    if ($existingReview) {
        return redirect()->route('buyer.orders.show', $order->id)
                         ->with('error', 'Вы уже оставляли отзыв о этом продавце для данного заказа.');
    }

    // Создаём новый отзыв
    SupplierReview::create([
        'supplier_id' => $supplierId,
        'order_id' => $order->id,
        'user_id' => Auth::id(),
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return redirect()->route('buyer.orders.show', $order->id)
                     ->with('success', 'Ваш отзыв о продавце успешно добавлен!');
}

}
