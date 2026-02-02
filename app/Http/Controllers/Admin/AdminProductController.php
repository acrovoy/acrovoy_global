<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $supplierFilter = $request->get('supplier');

    $query = Product::query()
        ->with([
            'supplier.user', // кто создал
            'category',
            'images',
            'priceTiers',
            'mainImage',
        ])
        ->where('status', 'pending'); // 🔴 ключевой момент

    // Фильтр по саплаеру
    if ($supplierFilter) {
        $query->whereHas('supplier.user', function ($q) use ($supplierFilter) {
            $q->where('name', 'like', "%{$supplierFilter}%");
        });
    }

    // Сортировка
    match ($sort) {
        'oldest' => $query->orderBy('created_at', 'asc'),
        default  => $query->orderBy('created_at', 'desc'),
    };

    $products = $query->paginate(20)->withQueryString();

    return view('dashboard.admin.products.index', compact(
        'products',
        'sort',
        'supplierFilter'
    ));
}

public function show(Product $product)
    {
        $product->load([
            'images',
            'mainImage',
            'specifications',
            'priceTiers',
            'supplier',
            'category',
            'colors',
            'colors.linkedProduct',
            'user',
        ]);

      
        return view('dashboard.admin.products.show', [
            'product1' => $product
        ]);
    }

    public function approve(Product $product)
    {
        $product->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success','Product approved');
    }

    public function reject(Request $request, Product $product)
{
    // Валидируем причину отклонения
    $request->validate([
        'reject_reason' => 'required|string|max:1000',
    ]);

    // Обновляем статус и сохраняем причину
    $product->update([
        'status' => 'rejected',
        'approved_by' => auth()->id(),
        'reject_reason' => $request->input('reject_reason'),
    ]);

    return back()->with('error', 'Product rejected with reason.');
}
}

