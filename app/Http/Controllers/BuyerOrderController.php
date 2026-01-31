<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuyerOrderController extends Controller
{
    /**
     * Buyer orders (TEST DATA)
     */
    public function index()
    {
        $orders = collect([
            (object) [
                'id' => 1001,
                'status' => 'Processing',
                'total' => 580,
                'created_at' => now()->subDays(2),
            ],
            (object) [
                'id' => 1002,
                'status' => 'Completed',
                'total' => 240,
                'created_at' => now()->subDays(10),
            ],
        ]);

        return view('dashboard.buyer.orders', compact('orders'));
    }


    public function show($id)
    {
        // TEST DATA
        $order = (object) [
            'id' => $id,
            'total' => 580,
            'items' => [
                (object)['name' => 'Product A', 'quantity' => 2, 'price' => 200],
                (object)['name' => 'Product B', 'quantity' => 1, 'price' => 180],
            ],
            'status_history' => [
                ['status' => 'Pending',    'date' => now()->subDays(5)],
                ['status' => 'Processing', 'date' => now()->subDays(3)],
                ['status' => 'Shipped',    'date' => now()->subDays(1)],
                ['status' => 'Completed',  'date' => now()],
            ],
        ];

        return view('dashboard.buyer.order-show', compact('order'));
    }



}
