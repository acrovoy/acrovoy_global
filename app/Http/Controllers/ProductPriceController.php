<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    /**
     * Store / Update price tiers for product
     */
    public function updatePriceTiers(Request $request, Product $product)
{
    $this->authorize('update', $product); // Проверка прав

    $tiers = $request->input('tiers', []);

    // Удаляем старые tiers
    $product->priceTiers()->delete();

    // Создаём новые
    foreach ($tiers as $tier) {
        $product->priceTiers()->create([
            'min_qty' => $tier['min_qty'] ?? 0,
            'max_qty' => $tier['max_qty'] ?? 0,
            'price'   => $tier['price'] ?? 0,
        ]);
    }

    // Отправляем обратно обновлённые tiers
    return response()->json([
        'success' => true,
        'tiers' => $product->priceTiers()->get(),
    ]);
}


}
