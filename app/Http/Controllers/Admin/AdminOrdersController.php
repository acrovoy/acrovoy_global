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

        // 🔹 Запрос к заказам
        $ordersQuery = Order::with([
            'user',          // Показываем данные пользователя
            'items.product', // Товары
            'disputes',      // Споры
        ]);

        if ($status) {
            $ordersQuery->where('status', $status);
        }

        if ($userFilter) {
            $ordersQuery->whereHas('user', function($q) use ($userFilter) {
                $q->where('name', 'like', "%{$userFilter}%")
                  ->orWhere('email', 'like', "%{$userFilter}%");
            });
        }

        // Сортировка
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

        // Преобразуем для блейда
        $orders = $orders->map(fn($order) => [
            'id'             => $order->id,
            'customer'       => $order->first_name . ' ' . $order->last_name,
            'user_name'      => $order->user->name ?? 'User',
            'user_last_name' => $order->user->last_name ?? 'User',
            'email'          => $order->user->email ?? null,
            'status'         => $order->status,
            'created_at'     => $order->created_at,
            'tracking_number'=> $order->tracking_number,
            'invoice_file'   => $order->invoice_file,
            'items'          => $order->items->map(fn($item) => [
                'product' => $item->product->name ?? 'Custom item',
                'qty'     => $item->quantity,
                'price'   => $item->price,
                'total'   => $item->quantity * $item->price,
            ]),
            'total'          => $order->items->sum(fn($item) => $item->quantity * $item->price),
            'disputes'       => $order->disputes,
        ]);


        $ordersWithOpenDisputes = $orders->map(function($order) {
    // Берем только первый открытый спор (или можно объединять, если их несколько)
    $openStatuses = ['pending', 'supplier_offer', 'rejected', 'admin_review'];
    $openDispute = $order['disputes']->first(fn($d) => in_array($d->status, $openStatuses));

    return array_merge($order, [
        'dispute_status' => $openDispute->status ?? null
    ]);
})->filter(fn($order) => $order['dispute_status'] !== null); // только с открытыми спорами

        return view('dashboard.admin.orders.index', compact('orders',
    'ordersWithOpenDisputes', 'sort', 'status', 'userFilter'));
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
