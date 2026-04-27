<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Facades\ActiveContext;

use App\Enums\ShipmentStatus;
use App\Services\ShipmentStatusService;
use App\Services\SupplierOrderAccessService;
use App\Services\OrderStatusService;
use App\Services\Company\ActiveContextService;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderDispute;
use App\Models\Country;
use App\Models\OrderItemShipment;
use App\Models\Location;
use App\Models\UserAddress;




use Illuminate\Support\Facades\Auth;

class ManufacturerOrderController extends Controller
{
    /**
     * Orders list
     */
    public function index()
{
    $supplierId = ActiveContext::id();

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
                'type' => $order->type,
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

    return view('dashboard.supplier.orders', compact(
        'orders',
        'disputedOrderIds'
    ));
}
    /**
     * View single order
     */
    public function show(int $id, SupplierOrderAccessService $access)
    {

    
     $supplierId = ActiveContext::id();

     

        $order = Order::with([
            'items.product',
            'items.shipments.originCountry',
            'items.shipments.originRegion',
            'items.shipments.originCity',
            'rfqOffer.rfq',
            'user',
            'disputes',
            'statusHistory',
            'items.shipments' => function($q) {
        $q->with('orderItem'); // чтобы была возможность вывести product_name и quantity
    }
        ])->findOrFail($id);

        // ✅ ЕДИНАЯ ПРОВЕРКА ДОСТУПА (каталог + RFQ)
        $supplier = ActiveContext::company();

abort_if(
    ! $supplier || ! $access->canAccess($order, $supplier),
    404
);



        

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


        $countries = Country::withCurrentTranslation()
    ->orderBy('name')->get();
        $shipment = OrderItemShipment::where('order_id', $order->id)->first();
        $lastAddress = OrderItemShipment::where('order_id', $order->id)->first();
    

        return view('dashboard.supplier.order-show', [
            'order' => [
                'id'       => $order->id,
                'status'   => $order->status,
                'type'     => $order->type,
                'customer' => trim($order->first_name . ' ' . $order->last_name),
                'email'    => $order->user->email ?? null,
                'date'     => $order->created_at->format('d M y'),

                // CONTACT & SHIPPING
                'first_name'  => $order->first_name,
                'last_name'   => $order->last_name,
                'country'     => $order->country,
                'country_name'=> $order->countryRelation?->name,
                'city'        => $order->city,
                'region'      => $order->region,
                'region_name'=> $order->regionRelation?->name,
                'street'      => $order->street,
                'postal_code' => $order->postal_code,
                'phone'       => $order->phone,

                'delivery_price_confirmed' => $order->delivery_price_confirmed,
                'totalwithdelivery' => $order->delivery_price + $orderItems->sum(fn ($item) => $item->quantity * $item->price),
                'provider_type' => $shipment->provider_type,

                // STATUS HISTORY
                'status_history' => $order->statusHistory,

                // ITEMS
                'items' => $orderItems->map(fn ($item) => [
                    'product'        => $item->product->name
                                        ?? $order->rfqOffer?->rfq?->title
                                        ?? 'RFQ item',
                    'product_object' => $item->product,
                    'image' => $item->thumbnail_url,
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
            'r_order' => $order,
            'lastAddress' => $lastAddress,
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
        $supplier = ActiveContext::company();

abort_if(
    ! $supplier || ! $access->canAccess($order, $supplier),
    404
);

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

        $supplier = ActiveContext::company();

abort_if(
    ! $supplier || ! $access->canAccess($order, $supplier),
    404
);

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


    public function storeOrigin(Request $request, OrderItem $item, ActiveContextService $context)
{
    $user = $context->user();

    if (!$context->isCompany()) {
    abort(403);
}

if ($context->role() !== 'supplier') {
    abort(403);
}


    $request->validate([
    // Адрес погрузки
    'origin_country_id' => 'required|integer|exists:countries,id',
    'origin_region_id'  => 'nullable|integer|exists:locations,id',
    'origin_city_id'    => 'nullable|integer|exists:locations,id',
    'origin_city_manual'=> 'nullable|string|max:255',
    'origin_address'    => 'required|string|max:255',
    'origin_contact_name'  => 'required|string|max:255',
    'origin_contact_phone' => 'required|string|max:255',

    // Размеры и вес
    'weight' => 'nullable|numeric|min:0|max:10000',
    'length' => 'nullable|numeric|min:0|max:1000',
    'width'  => 'nullable|numeric|min:0|max:1000',
    'height' => 'nullable|numeric|min:0|max:1000',
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

        $shipment = OrderItemShipment::firstOrNew([
    'shippable_type' => OrderItem::class,
    'shippable_id'   => $item->id,
]);

// Обновляем только поля, которые пришли
if ($request->filled('origin_country_id')) {
    $shipment->origin_country_id = (int) $request->origin_country_id;
}

if ($request->filled('origin_region_id')) {
    $shipment->origin_region_id = (int) $request->origin_region_id;
}

if ($request->filled('origin_city_id')) {
    $shipment->origin_city_id = (int) $cityId;
}

if ($request->filled('origin_address')) {
    $shipment->origin_address = $request->origin_address;
}

if ($request->filled('origin_contact_name')) {
    $shipment->origin_contact_name = $request->origin_contact_name;
}

if ($request->filled('origin_contact_phone')) {
    $shipment->origin_contact_phone = $request->origin_contact_phone;
}

if ($request->filled('weight')) {
    $shipment->weight = $request->weight;
}

if ($request->filled('length')) {
    $shipment->length = $request->length;
}

if ($request->filled('width')) {
    $shipment->width = $request->width;
}

if ($request->filled('height')) {
    $shipment->height = $request->height;
}

if ($request->filled('shipping_price')) {
    $shipment->shipping_price = $request->shipping_price;
}

if ($request->filled('delivery_time')) {
    $shipment->delivery_time = $request->delivery_time;
}

if ($request->filled('status')) {
    $shipment->status = $request->status;
}

// Обязательно сохраняем
$shipment->order_id = $item->order_id;
$shipment->save();


    });

    return back()->with('success', 'Pickup (origin) address saved successfully.');
}



public function updateShipment(
    Order $order,
    OrderItemShipment $orderItemShipment,
    Request $request
) {
    try {

        $validated = $request->validate([
            'status' => 'required|string',
            'comment' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'delivery_time' => 'nullable|integer',
            'shipping_price' => 'nullable|numeric',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        // 🔥 Меняем статус через сервис
        if ($validated['status'] !== $orderItemShipment->status) {
    ShipmentStatusService::change(
        $orderItemShipment,
        $validated['status'],
        $validated['comment'] ?? null
    );
}

        // Обновляем остальные поля
        unset($validated['status'], $validated['comment']); // 👈 добавили comment
        $orderItemShipment->update($validated);

        return response()->json([
            'success' => true,
            'shipment' => $orderItemShipment,
        ]);

    } catch (\Throwable $e) {
        \Log::error($e);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ], 500);
    }
}





}
