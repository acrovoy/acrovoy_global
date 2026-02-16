<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\SupplierOrderAccessService;
use App\Services\OrderStatusService;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderDispute;
use App\Models\Country;
use App\Models\OrderItemShipment;
use App\Models\Location;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class ManufacturerOrderController extends Controller
{
    /**
     * Orders list
     */
    public function index()
    {
        $supplierId = auth()->user()->supplier->id;

        $ordersQuery = OrderItem::query()
            ->where(function ($q) use ($supplierId) {

                // 🟢 Заказы из каталога
                $q->whereHas('product', function ($q) use ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                })

                // 🟣 Заказы из RFQ
                ->orWhereHas('order.rfqOffer', function ($q) use ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                });

            })
            ->with([
                'order.user',
                'product',
                'order.rfqOffer.rfq'
            ])
            ->orderByDesc('created_at');

        // 🔹 Фильтр по статусу
        if ($status = request('status')) {
            $ordersQuery->whereHas('order', fn ($q) => $q->where('status', $status));
        }

        $orders = $ordersQuery->get()
    ->groupBy(fn ($item) => $item->order->id)
    ->map(function ($items) {

        $first = $items->first();
        $order = $first->order;

        return [
            'id'       => $order->id,
            'customer' => $order->first_name . ' ' . $order->last_name,

            'items' => $items->map(function ($item) {
                return [
                    'product' => $item->product->name
                        ?? $item->order->rfqOffer?->rfq?->title
                        ?? 'Custom RFQ',
                    'qty'     => $item->quantity,
                    'price'   => $item->price,
                    'total'   => $item->quantity * $item->price,
                ];
            }),

            'total'  => $items->sum(fn ($i) => $i->quantity * $i->price),
            'delivery_price' => $order->delivery_price,
            'status' => $order->status,
            'date'   => $order->created_at->format('d M Y'),
        ];
    })
    ->values();

        // ID заказов со спором
        $disputedOrderIds = OrderDispute::whereHas('order.items.product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->orWhereHas('order.rfqOffer', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->whereIn('status', [
                'pending',
                'supplier_offer',
                'rejected',
                'admin_review'
            ])
            ->pluck('order_id')
            ->unique()
            ->toArray();

        return view('dashboard.manufacturer.orders', compact(
            'orders',
            'disputedOrderIds'
        ));
    }

    /**
     * View single order
     */
    public function show(int $id, SupplierOrderAccessService $access)
    {

    
     $supplierId = auth()->user()->supplier->id;

        $order = Order::with([
            'items.product',
            'items.product.mainImage',
            'items.shipments.originCountry',
            'items.shipments.originRegion',
            'items.shipments.originCity',
            'rfqOffer.rfq',
            'user',
            'disputes',
            'statusHistory',
        ])->findOrFail($id);

        // ✅ ЕДИНАЯ ПРОВЕРКА ДОСТУПА (каталог + RFQ)
        abort_if(! $access->canAccess($order, auth()->user()->supplier), 404);



        

        // Показываем только items этого поставщика
    $orderItems = $order->items->filter(function ($item) use ($supplierId) {
        // Если это товар из каталога — проверяем supplier_id
        if ($item->product && $item->product->supplier_id === $supplierId) {
            return true;
        }




        // Если это RFQ — проверяем supplier_id в rfqOffer
        if ($item->order->rfqOffer && $item->order->rfqOffer->supplier_id === $supplierId) {
            return true;
        }

        return false;
    });


        $countries = Country::orderBy('name')->get();
    

        return view('dashboard.manufacturer.order-show', [
            'order' => [
                'id'       => $order->id,
                'status'   => $order->status,
                'customer' => trim($order->first_name . ' ' . $order->last_name),
                'email'    => $order->user->email ?? null,
                'date'     => $order->created_at->format('d M y'),

                // CONTACT & SHIPPING
                'first_name'  => $order->first_name,
                'last_name'   => $order->last_name,
                'country'     => $order->country,
                'city'        => $order->city,
                'region'      => $order->region,
                'street'      => $order->street,
                'postal_code' => $order->postal_code,
                'phone'       => $order->phone,

                // STATUS HISTORY
                'status_history' => $order->statusHistory,

                // ITEMS
                'items' => $orderItems->map(fn ($item) => [
                    'product'        => $item->product->name
                                        ?? $order->rfqOffer?->rfq?->title
                                        ?? 'RFQ item',
                    'product_object' => $item->product,
                    'image'          => $item->product && $item->product->mainImage
                                        ? $item->product->mainImage->image_path
                                        : null, // <-- здесь добавляем ключ 'image'
                    'qty'            => $item->quantity,
                    'price'          => $item->price,
                    'total'          => $item->quantity * $item->price,
                ]),

                'total' => $orderItems->sum(fn ($item) => $item->quantity * $item->price),
                'delivery_price' => $order->delivery_price ?? 0,
                'delivery_method' => $order->delivery_method ?? 0,
                'tracking_number' => $order->tracking_number,
                'invoice_file'    => $order->invoice_file,

                // DISPUTES
                'disputes' => $order->disputes()->orderBy('created_at', 'desc')->get(),
            ],
            'countries' => $countries,
            'order_items' => $orderItems,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(
        Request $request,
        Order $order,
        SupplierOrderAccessService $access
    ) {
        abort_if(! $access->canAccess($order, auth()->user()->supplier), 403);

        $data = $request->validate([
            'status'  => 'required|string',
            'comment' => 'nullable|string|max:500',
        ]);

        OrderStatusService::change(
            $order,
            $data['status'],
            $data['comment'] ?? null
        );

        return back()->with('success', 'Order status updated.');
    }

    /**
     * Update tracking & invoice
     */
    public function updateTracking(
        Request $request,
        int $id,
        SupplierOrderAccessService $access
    ) {
        $order = Order::findOrFail($id);

        abort_if(! $access->canAccess($order, auth()->user()->supplier), 403);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot update a completed or cancelled order.');
        }

        $data = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'invoice_file'    => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('invoice_file')) {
            $data['invoice_file'] = $request
                ->file('invoice_file')
                ->store('invoices', 'public');
        }

        $order->update($data);

        return back()->with('success', 'Tracking number and invoice updated successfully.');
    }


    public function storeOrigin(Request $request, OrderItem $item)
{
    $user = auth()->user();

    $request->validate([
        'origin_country_id' => 'required|integer',
        'origin_region_id'  => 'nullable|integer',
        'origin_city_id'    => 'nullable|integer',
        'origin_city_manual'=> 'nullable|string|max:255',
        'origin_address'    => 'required|string|max:255',
        'origin_contact_name'  => 'required|string|max:255',
        'origin_contact_phone' => 'required|string|max:255',
    ]);

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Определяем город
    |--------------------------------------------------------------------------
    */

    $cityId = null;
    $finalCityName = null;

    // Если введён вручную
    if ($request->filled('origin_city_manual')) {

        $finalCityName = trim($request->origin_city_manual);

        $existingCity = Location::where('name', $finalCityName)
            ->where('parent_id', $request->origin_region_id)
            ->first();

        if ($existingCity) {
            $cityId = $existingCity->id;
        } else {
            $newCity = Location::create([
                'name'       => $finalCityName,
                'parent_id'  => $request->origin_region_id ?: null,
                'country_id' => $request->origin_country_id,
                'updated_by' => $user->id,
            ]);

            $cityId = $newCity->id;
        }

    }
    // Если выбран из списка
    elseif ($request->filled('origin_city_id')) {

        $cityId = (int) $request->origin_city_id;
        $cityModel = Location::find($cityId);
        $finalCityName = $cityModel?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Сохраняем адрес поставщика (если нужно)
    |--------------------------------------------------------------------------
    */

    if ($request->boolean('save_as_new')) {

        UserAddress::firstOrCreate(
            [
                'user_id' => $user->id,
                'country' => $request->origin_country_id,
                'region'  => $request->origin_region_id,
                'city'    => $finalCityName,
                'street'  => $request->origin_address,
                'phone'   => $request->origin_contact_phone,
            ],
            [
                'is_default' => false
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Обновляем ORIGIN shipment
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use ($item, $request, $cityId) {

        OrderItemShipment::updateOrCreate(
            [
                'shippable_type' => OrderItem::class,
                'shippable_id'   => $item->id,
            ],
            [
                'order_id'               => $item->order_id,

                // 🔥 ВАЖНО: теперь origin, а не destination
                'origin_country_id'      => (int) $request->origin_country_id,
                'origin_region_id'       => (int) $request->origin_region_id,
                'origin_city_id'         => (int) $cityId,
                'origin_address'         => $request->origin_address,
                'origin_contact_name'    => $request->origin_contact_name,
                'origin_contact_phone'   => $request->origin_contact_phone,

                // Остальное пока пустое
                'shipping_price' => 0,
                'delivery_time'  => null,
                'status'         => 'pending',
            ]
        );
    });

    return back()->with('success', 'Pickup (origin) address saved successfully.');
}



}
