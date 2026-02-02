<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\OrderDispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OrderStatusService;





class OrderController extends Controller
{


    public function index(Request $request)
{
    $query = Order::where('user_id', auth()->id());

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->orderBy('created_at', 'desc')->get();

    $disputedOrderIds = OrderDispute::whereHas('order', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
        ->pluck('order_id')
        ->toArray();

    return view('dashboard.buyer.orders.index', compact('orders',
        'disputedOrderIds'));
}

    // Переход на страницу чекаута
    public function checkout()
{
    $user = auth()->user();

    $cartItems = CartItem::where('user_id', $user->id)
        ->with(['product.shippingTemplates.translations'])
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('buyer.cart.index')
            ->with('error', 'Your cart is empty.');
    }

    $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);

    // Собираем все шаблоны доставки товаров
    $allShippingTemplates = $cartItems
        ->flatMap(fn($item) => $item->product->shippingTemplates)
        ->unique('id')
        ->values();

    // Получаем все адресные шаблоны пользователя (по убыванию даты)
    $savedAddresses = $user->addresses()->orderByDesc('updated_at')->get();

    // Берём последний сохранённый шаблон
    $lastAddress = $savedAddresses->first();

    return view('dashboard.buyer.orders.checkout', [
        'cartItems'        => $cartItems,
        'total'            => $total,
        'shippingOptions'  => $allShippingTemplates,
        'savedAddresses'   => $savedAddresses, // для селекта
        'lastAddress'      => $lastAddress,    // для предзаполнения формы
    ]);
}

    // Сохраняем данные чекаута
    public function store(Request $request)
{
    $cartItems = CartItem::where('user_id', auth()->id())
        ->with('product')
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('buyer.cart.index')
            ->with('error', 'Your cart is empty.');
    }

    // Сумма товаров
    $itemsTotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

    // Получаем доставку
    $shippingTemplate = \App\Models\ShippingTemplate::find($request->delivery_template_id);
    $shippingPrice = $shippingTemplate?->price ?? 0;

    // Общая сумма
    $total = $itemsTotal + $shippingPrice;

    DB::transaction(function () use (
        $request,
        $cartItems,
        $total,
        $shippingPrice,
        $shippingTemplate,
        &$order
    ) {

        // 1️⃣ Создаём заказ
        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'type' => 'product',
            'total' => $total,
            'delivery_price' => $shippingPrice,
            'delivery_method' => $shippingTemplate?->title,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'region' => $request->input('region'),
            'street' => $request->input('street'),
            'postal_code' => $request->input('postal_code'),
            'phone' => $request->input('phone'),
            'notes' => $request->input('notes'),
        ]);

        // 2️⃣ ОБЯЗАТЕЛЬНО фиксируем первый статус
        $order->statusHistory()->create([
            'status' => 'pending',
            'comment' => 'Заказ создан покупателем',
        ]);

        // 3️⃣ Удаляем старые позиции (safety)
        $order->items()->delete();

        // 4️⃣ Добавляем позиции заказа
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name ?? 'Product unavailable',
                'price' => $item->price,
                'quantity' => $item->quantity,
            ]);
        }

        // 5️⃣ Очищаем корзину
        CartItem::where('user_id', auth()->id())->delete();
    });

    return redirect()->route('buyer.orders.show', $order)
        ->with('success', 'Order placed successfully!');
}






public function show(Order $order)
{
    // Проверяем, что заказ принадлежит текущему пользователю
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    $order->load([
        'items.product',
        'statusHistory',
        'user.addresses',
    ]);

    // Определяем доступные действия
    $canCancel = in_array($order->status, ['pending', 'confirmed', 'paid']);
    $canEditAddress = in_array($order->status, ['pending', 'confirmed', 'paid']);
    $canTrack = in_array($order->status, ['shipped', 'delivered']);

    return view('dashboard.buyer.orders.show', [
        'order' => $order,
        'canCancel' => $canCancel,
        'canEditAddress' => $canEditAddress,
        'canTrack' => $canTrack,
    ]);
}

public function cancel(Order $order)
{
    if (!in_array($order->status, ['pending', 'paid'])) {
        return back()->with('error', 'Cannot cancel this order.');
    }

    \App\Services\OrderStatusService::change($order, 'cancelled', 'Cancelled by buyer');

    return back()->with('success', 'Order cancelled successfully.');
}

public function editAddress(Order $order)
{
    if (!in_array($order->status, ['pending', 'paid'])) {
        abort(403);
    }

    return view('dashboard.buyer.orders.edit-address', compact('order'));
}

public function invoice(Order $order)
{
    // Проверяем, что заказ принадлежит текущему пользователю
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    // Если инвойса нет
    if (!$order->invoice_file || !\Storage::disk('public')->exists($order->invoice_file)) {
        return back()->with('info', 'Invoice is not yet uploaded by the seller.');
    }

    // Отдаём файл
    return response()->download(storage_path('app/public/' . $order->invoice_file));
}

public function track(Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    if (!$order->tracking_number) {
        return back()->with('error', 'Tracking not available.');
    }

    // Перенаправление на сайт перевозчика
    return redirect("https://track.shippingcompany.com/{$order->tracking_number}");
}



public function edit(int $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending') // только pending
            ->with('items.product')
            ->firstOrFail();

        // Доступные статусы для изменения
        $availableStatuses = OrderStatusService::availableStatuses($order->status);

        // Получаем все сохранённые адреса пользователя
        $savedAddresses = auth()->user()->addresses()->orderByDesc('updated_at')->get();

        // Опционально: последний использованный адрес
        $lastAddress = $savedAddresses->first();

        return view('dashboard.buyer.orders.edit', compact('order', 'savedAddresses', 'lastAddress'));
    }

    /**
     * Сохранение изменений
     */
    public function update(Request $request, int $id)
{
    $order = Order::where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending') // только pending
        ->firstOrFail();

    // Валидация контактных данных
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'country' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'region' => 'nullable|string|max:255',
        'street' => 'required|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'phone' => 'required|string|max:50',
        'items' => 'required|array',
        'items.*.id' => 'required|exists:order_items,id',
        'items.*.product_name' => 'required|string|max:255',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ]);

    // Обновляем контактные данные
    $order->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'country' => $request->country,
        'city' => $request->city,
        'region' => $request->region,
        'street' => $request->street,
        'postal_code' => $request->postal_code,
        'phone' => $request->phone,
    ]);

    // Обновляем товары в заказе
    foreach ($request->items as $itemData) {
        $orderItem = $order->items()->where('id', $itemData['id'])->first();
        if ($orderItem) {
            $orderItem->update([
                'product_name' => $itemData['product_name'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
            ]);
        }
    }

    return redirect()
        ->route('buyer.orders.edit', $order->id)
        ->with('success', 'Заказ успешно обновлён ');
}



}