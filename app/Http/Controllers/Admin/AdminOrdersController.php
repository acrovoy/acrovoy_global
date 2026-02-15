<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDispute;
use App\Models\OrderItemShipment;

class AdminOrdersController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->get('sort', '');
    $status = $request->get('status', '');
    $userFilter = $request->get('user', '');

    $ordersQuery = Order::with([
        'user',
        'items.product',
        'disputes',
    ]);

    // 🔹 Фильтр по статусу
    if ($status) {
        $ordersQuery->where('status', $status);
    }

    // 🔹 Фильтр по пользователю
    if ($userFilter) {
        $ordersQuery->whereHas('user', function ($q) use ($userFilter) {
            $q->where('name', 'like', "%{$userFilter}%")
              ->orWhere('email', 'like', "%{$userFilter}%");
        });
    }

    // 🔹 Сортировка
    switch ($sort) {
        case 'oldest':
            $ordersQuery->orderBy('created_at', 'asc');
            break;
        case 'status':
            $ordersQuery->orderBy('status', 'asc');
            break;
        default:
            $ordersQuery->orderBy('created_at', 'desc');
    }

    $orders = $ordersQuery->get();

   

    // 🔹 Заказы с открытыми спорами
    $openStatuses = ['pending', 'supplier_offer', 'rejected', 'admin_review'];

    $ordersWithOpenDisputes = $orders->filter(function ($order) use ($openStatuses) {
        return $order->disputes
            ->whereIn('status', $openStatuses)
            ->isNotEmpty();
    });

    return view(
        'dashboard.admin.orders.index',
        compact(
            'orders',
            'ordersWithOpenDisputes',
            'sort',
            'status',
            'userFilter'
        )
    );
}


    public function show(int $id)
{
    $order = Order::with([
        'items.product',
        'user',
        'disputes',
        'statusHistory',
    ])->findOrFail($id);

    // Подготовка данных для блейда
    $orderData = [
        'id'             => $order->id,
        'status'         => $order->status,
        'customer'       => trim($order->first_name . ' ' . $order->last_name),
        'user_name'      => $order->user->name ?? 'User',
        'email'          => $order->user->email ?? null,
        'date'           => $order->created_at->format('Y-m-d H:i'),

        // CONTACT & SHIPPING
        'first_name'     => $order->first_name,
        'last_name'      => $order->last_name,
        'country'        => $order->country,
        'city'           => $order->city,
        'region'         => $order->region,
        'street'         => $order->street,
        'postal_code'    => $order->postal_code,
        'phone'          => $order->phone,

        // STATUS HISTORY
        'status_history' => $order->statusHistory,

        // ITEMS
        'items' => $order->items->map(fn($item) => [
            'product' => $item->product->name ?? 'Custom item',
            'qty'     => $item->quantity,
            'price'   => $item->price,
            'total'   => $item->quantity * $item->price,
        ]),

        'tracking_number' => $order->tracking_number,
        'invoice_file'    => $order->invoice_file,

        // DISPUTES
        'disputes' => $order->disputes()->orderBy('created_at', 'desc')->get(),
    ];

    return view('dashboard.admin.orders.show', compact('orderData'));
}

public function shipments($orderId)
{
    $order = Order::with([
    'items.shipments' => function($q) {
        $q->with('orderItem'); // чтобы была возможность вывести product_name и quantity
    }
])->findOrFail($orderId);

    return view('dashboard.admin.orders.shipments', compact('order'));
}


public function addDisputeAdminComment(Request $request, OrderDispute $dispute)
{
    $request->validate([
        'admin_comment' => 'required|string|max:2000',
    ]);

    $dispute->update([
        'admin_comment' => $request->admin_comment,
        'status' => 'admin_review', // важно: админ вмешался
    ]);

    return back()->with('success', 'Admin comment added.');
}

public function update(Request $request, OrderDispute $dispute)
{
    $request->validate([
        'admin_comment' => 'nullable|string|max:2000',
        'status' => 'required|in:pending,resolved',
    ]);

    $dispute->update([
        
        'status' => $request->status,
    ]);

    return back()->with('success', 'Dispute updated');
}


public function updateShipment(Order $order, OrderItemShipment $orderItemShipment, Request $request)
{
    $orderItemShipment->update($request->only([
        'weight', 'length', 'width', 'height',
        'delivery_time', 'shipping_price', 'status', 'tracking_number'
    ]));

    return response()->json(['success' => true, 'shipment' => $orderItemShipment]);
}

public function uploadInvoiceDelivery(Request $request, Order $order)
{
    $request->validate([
        'invoice_delivery_file' => 'required|file|mimes:pdf|max:10240', // максимум 10 МБ
    ]);

    if ($request->hasFile('invoice_delivery_file')) {
        // Удаляем старый файл, если есть
        if ($order->invoice_delivery_file && \Storage::exists($order->invoice_delivery_file)) {
            \Storage::delete($order->invoice_delivery_file);
        }

        $path = $request->file('invoice_delivery_file')->store('invoices/delivery', 'public');
        $order->invoice_delivery_file = $path;
        $order->save();
    }

    return redirect()->back()->with('success', 'Delivery invoice uploaded successfully.');
}

public function calculateDeliveryPrice(Order $order)
{
    // Считаем сумму всех shipping_price всех shipments этого заказа
    $totalShipping = $order->items->flatMap->shipments->sum('shipping_price');

    // Сохраняем в поле delivery_price
    $order->delivery_price = $totalShipping;
    $order->save();

    // Можно отправить уведомление покупателю (если нужно)
    // Mail::to($order->user->email)->send(new DeliveryPriceCalculated($order));

    return response()->json([
        'success' => true,
        'totalShipping' => $totalShipping,
        'message' => 'Total shipping saved successfully!'
    ]);
}

}
