<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\OrderDispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OrderStatusService;
use App\Models\UserAddress;
use App\Models\Country;  
use App\Models\Location;





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
    ->flatMap(function ($item) {
        return $item->product->shippingTemplates->map(function ($template) use ($item) {
            // Добавляем вычисленную цену доставки
            $template->computed_price = $item->product->computeShippingPrice($template);
            return $template;
        });
    })
    ->unique('id')
    ->values();

        

    // Получаем все адресные шаблоны пользователя (по убыванию даты)
    $savedAddresses = $user->addresses()->orderByDesc('updated_at')->get();

    // Берём последний сохранённый шаблон
    $lastAddress = $savedAddresses->first();

    $regions = collect(); // пустой по умолчанию

    $countries = Country::withCurrentTranslation()
    ->orderBy('name')->get();

    

if ($lastAddress && $lastAddress->country) {
    $regions = Location::whereNull('parent_id')
        ->where('country_id', $lastAddress->country)
        ->orderBy('name')
        ->get();
}




    return view('dashboard.buyer.orders.checkout', [
        'cartItems'        => $cartItems,
        'total'            => $total,
        'shippingOptions'  => $allShippingTemplates,
        'savedAddresses'   => $savedAddresses, // для селекта
        'lastAddress'      => $lastAddress,    // для предзаполнения формы
        'countries'        => $countries,
        'regions'          => $regions,   
    ]);
}





    // Сохраняем данные чекаута
    public function store(Request $request)
{


    $user = auth()->user();

    $finalCity = $request->city_manual ?: null;
    $cityId = null;

    // 1️⃣ Если пользователь ввёл новый город вручную
    if ($request->filled('city_manual')) {
        $existingLocation = \App\Models\Location::where('name', $finalCity)
                            ->where('parent_id', $request->region)
                            ->first();

        if ($existingLocation) {
            $cityId = $existingLocation->id;
        } else {
            $newLocation = \App\Models\Location::create([
                'name'       => $finalCity,
                'parent_id'  => $request->region ?: null,
                'country_id' => $request->country,
                'updated_by' => $user->id,
            ]);
            $cityId = $newLocation->id;
        }
    }

    // 2️⃣ Если город выбран из списка
    elseif ($request->filled('city')) {
        // Здесь важно, чтобы в форме приходил ID выбранного города, а не название
        $cityId = (int) $request->city;  
    }

    $cityModel = \App\Models\Location::find($cityId);
    $finalCity = $cityModel?->name ?? '';



/**
 * 1️⃣ Адрес из формы (ВСЕГДА снепшот)
 */
$formAddress = [
    'first_name'  => $request->first_name,
    'last_name'   => $request->last_name,
    'country'     => $request->country,
    'city'        => $finalCity,
    'region'      => $request->region,
    'street'      => $request->street,
    'postal_code' => $request->postal_code,
    'phone'       => $request->phone,
];

/**
 * 2️⃣ Выбранный сохранённый адрес (если есть)
 */
$selectedAddress = null;

if ($request->filled('saved_address_id')) {
    $selectedAddress = $user->addresses()
        ->where('id', $request->saved_address_id)
        ->firstOrFail();
}

/**
 * 3️⃣ Решаем — сохраняем адрес или нет
 */
if ($request->boolean('save_as_new')) {

    UserAddress::firstOrCreate(
        ['user_id' => $user->id] + $formAddress,
        ['is_default' => false]
    );

} elseif (!$selectedAddress) {

    // пользователь не выбирал адрес → первый checkout
    UserAddress::firstOrCreate(
        ['user_id' => $user->id] + $formAddress,
        ['is_default' => false]
    );
}



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
    $shippingPrice = 0;

    if ($shippingTemplate) {
        // считаем для каждого товара и суммируем
        foreach ($cartItems as $item) {
            $shippingPrice += $item->product->computeShippingPrice($shippingTemplate) * $item->quantity;
        }
    }

    $providerType = null;
    $providerId   = null;

if ($shippingTemplate?->manufacturer_id) {
    $providerType = \App\Models\Supplier::class;
    $providerId   = $shippingTemplate->manufacturer_id;
}

if ($shippingTemplate?->logistic_company_id) {
    $providerType = \App\Models\LogisticCompany::class;
    $providerId   = $shippingTemplate->logistic_company_id;
}

    // Общая сумма
    $total = $itemsTotal + $shippingPrice;

    DB::transaction(function () use (
        $request,
        $cartItems,
        $total,
        $shippingPrice,
        $shippingTemplate,
        $formAddress,
        $cityId,
        $providerType,
        $providerId,
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
            'first_name'  => $formAddress['first_name'],
            'last_name'   => $formAddress['last_name'],
            'country'     => $formAddress['country'],
            'city'        => $formAddress['city'],
            'region'      => $formAddress['region'],
            'street'      => $formAddress['street'],
            'postal_code' => $formAddress['postal_code'],
            'phone'       => $formAddress['phone'],
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

         $product = $item->product;
    $dimensions = $product->shippingDimensions;

    $shippingPrice = $product->computeShippingPrice($shippingTemplate);


            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name ?? 'Product unavailable',
                'price' => $item->price,
                'quantity' => $item->quantity,
            ]);

            // 🔥 Создаём shipment для каждого order item
          
    \App\Models\OrderItemShipment::create([
        'order_id'       => $order->id,
        'shippable_type' => \App\Models\OrderItem::class,
        'shippable_id'   => $orderItem->id,

        
        'provider_type'  => $providerType,
        'provider_id'    => $providerId,

        'destination_country_id' => (int)$request->country,
        'destination_region_id'  => (int)$request->region,
        'destination_city_id'    => (int)$cityId, 
        'destination_address'    => $formAddress['street'],
        'destination_contact_name' => $formAddress['first_name'] . ' ' . $formAddress['last_name'],
        'destination_contact_phone' => $formAddress['phone'],

        
        'weight' => $dimensions?->weight,
        'length' => $dimensions?->length,
        'width'  => $dimensions?->width,
        'height' => $dimensions?->height,

        'shipping_price' => $item->product->computeShippingPrice($shippingTemplate),
        'price_unit'     => $shippingTemplate?->price_unit,
        'delivery_time'  => $shippingTemplate->delivery_time,
        'status'         => 'pending',
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

    $user = auth()->user();
    // Проверяем, что заказ принадлежит текущему пользователю
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    $order->load([
        'items.product.images',
        'items.shipments',
        'statusHistory',
        'user.addresses',
        'countryRelation',
        'regionRelation',
        'cityRelation',
    ]);

    $countries = Country::withCurrentTranslation()
    ->orderBy('name')->get();

    // Получаем все адресные шаблоны пользователя (по убыванию даты)
    $savedAddresses = $user->addresses()->orderByDesc('updated_at')->get();

    // Берём последний сохранённый шаблон
    $lastAddress = $savedAddresses->first();

    $regions = collect(); // пустой по умолчанию
    
    
    // Определяем доступные действия
    $canCancel = in_array($order->status, ['pending', 'confirmed', 'paid']);
    $canEditAddress = in_array($order->status, ['pending', 'confirmed', 'paid']);
    $canTrack = in_array($order->status, ['shipped', 'delivered']);

    return view('dashboard.buyer.orders.show', [
        'order' => $order,
        'canCancel' => $canCancel,
        'canEditAddress' => $canEditAddress,
        'canTrack' => $canTrack,
        'countries' => $countries,
        'lastAddress' => $lastAddress,
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

        $countries = Country::withCurrentTranslation()
    ->orderBy('name')->get();

        // Доступные статусы для изменения
        $availableStatuses = OrderStatusService::availableStatuses($order->status);

        // Получаем все сохранённые адреса пользователя
        $savedAddresses = auth()->user()->addresses()->orderByDesc('updated_at')->get();

        // Опционально: последний использованный адрес
        $lastAddress = $savedAddresses->first();

        $orderItems = $order->items->load('product.priceTiers')->map(function($item) {
            $product = $item->product;
            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price ?? 0,
                'priceTiers' => $product
                    ? $product->priceTiers->map(fn($tier) => [
                        'min_qty' => $tier->min_qty,
                        'max_qty' => $tier->max_qty,
                        'price' => $tier->price,
                    ])->values()->toArray()
                    : [],
                
            ];
        })->values()->toArray();



        return view('dashboard.buyer.orders.edit', compact('order', 'savedAddresses', 'lastAddress', 'orderItems', 'order', 'countries'));
    }

    /**
     * Сохранение изменений
     */
    public function update(Request $request, int $id)
{

    $user = auth()->user();

    $finalCity = $request->city_manual ?: null;
    $cityId = null;

    // 1️⃣ Если пользователь ввёл новый город вручную
    if ($request->filled('city_manual')) {
        $existingLocation = \App\Models\Location::where('name', $finalCity)
                            ->where('parent_id', $request->region)
                            ->first();

        if ($existingLocation) {
            $cityId = $existingLocation->id;
        } else {
            $newLocation = \App\Models\Location::create([
                'name'       => $finalCity,
                'parent_id'  => $request->region ?: null,
                'country_id' => $request->country,
                'updated_by' => $user->id,
            ]);
            $cityId = $newLocation->id;
        }
    }

    // 2️⃣ Если город выбран из списка
    elseif ($request->filled('city')) {
        // Здесь важно, чтобы в форме приходил ID выбранного города, а не название
        $cityId = (int) $request->city;  
    }

    $cityModel = \App\Models\Location::find($cityId);
    $finalCity = $cityModel?->name ?? '';



    $order = Order::where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending') // только pending
        ->firstOrFail();

    

    // Обновляем контактные данные
    $order->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'country' => $request->country,
        'city' => $finalCity,
        'region' => $request->region,
        'street' => $request->street,
        'postal_code' => $request->postal_code,
        'phone' => $request->phone,
    ]);

    // Обновляем товары в заказе
    foreach ($request->input('items') as $itemData) {

    $orderItem = $order->items()->with('product.priceTiers')->find($itemData['id']);
    if (!$orderItem) continue;

    $quantity = max(1, intval($itemData['quantity']));
    $orderItem->quantity = $quantity;

    if ($order->type === 'rfq') {
        $orderItem->price = $itemData['price'];
    } else {
        if ($orderItem->product) {

            $priceTier = $orderItem->product->priceTiers()
                ->where('min_qty', '<=', $quantity)
                ->where(function($q) use ($quantity) {
                    $q->where('max_qty', '>=', $quantity)
                      ->orWhereNull('max_qty');
                })
                ->orderBy('min_qty', 'desc')
                ->first();

            $orderItem->price = $priceTier->price
                ?? $orderItem->product->price
                ?? 0;
        } else {
            $orderItem->price = 0;
        }
    }

    $orderItem->save();

    
$shipment = $orderItem->shipment;

if ($shipment) {
    $shipment->update([
        'destination_country_id' => (int)$request->country,
        'destination_region_id'  => (int)$request->region,
        'destination_city_id'    => $cityId,
        'destination_address'    => $request->street,
        'destination_contact_name' => $request->first_name . ' ' . $request->last_name,
        'destination_contact_phone' => $request->phone,
    ]);
}


}

   

    return redirect()
        ->route('buyer.orders.show', $order->id)
        ->with('success', 'Заказ успешно обновлён ');
}


public function updateAddress(Request $request, Order $order)
{
    $user = auth()->user();

    $finalCity = $request->city_manual ?: null;
    $cityId = null;

    if ($request->filled('city_manual')) {

        $existingLocation = \App\Models\Location::where('name', $finalCity)
            ->where('parent_id', $request->region)
            ->first();

        if ($existingLocation) {
            $cityId = $existingLocation->id;
        } else {
            $newLocation = \App\Models\Location::create([
                'name'       => $finalCity,
                'parent_id'  => $request->region ?: null,
                'country_id' => $request->country,
                'updated_by' => $user->id,
            ]);

            $cityId = $newLocation->id;
        }

    } elseif ($request->filled('city')) {

        $cityId = (int)$request->city;
    }

    $cityModel = \App\Models\Location::find($cityId);
    $finalCity = $cityModel?->name ?? '';

    // Обновляем сам заказ
    $order->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'country' => $request->country,
        'city' => $finalCity,
        'region' => $request->region,
        'street' => $request->street,
        'postal_code' => $request->postal_code,
        'phone' => $request->phone,
    ]);

    // 🔥 Обновляем shipment каждого айтема
    foreach ($order->items()->with('shipment')->get() as $orderItem) {

        if ($orderItem->shipment) {
            $orderItem->shipment->update([
                'destination_country_id' => (int)$request->country,
                'destination_region_id'  => (int)$request->region,
                'destination_city_id'    => $cityId,
                'destination_address'    => $request->street,
                'destination_contact_name' => $request->first_name . ' ' . $request->last_name,
                'destination_contact_phone' => $request->phone,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Address updated successfully!');
}


public function confirmDeliveryPrice($orderId)
{
    $order = Order::findOrFail($orderId);

    // Например, сохраняем флаг, что покупатель подтвердил стоимость доставки
    $order->delivery_price_confirmed = true; // добавь поле в таблицу orders, если нужно
    $order->save();

    return redirect()->back()->with('success', 'Delivery price confirmed successfully.');
}

}