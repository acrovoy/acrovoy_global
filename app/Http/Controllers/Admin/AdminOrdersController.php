<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDispute;

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

    // 🔹 Заказы с запросом стоимости доставки (Acrovoy + цена 0)
    $ordersWithTransportRequest = $orders->filter(function ($order) {
        return $order->delivery_method === 'Acrovoy Delivery'
            && $order->delivery_price == 0;
    });

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
            'ordersWithTransportRequest',
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



}
