<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRFQRequest;

use App\Models\Rfq;
use App\Models\RfqOffer;
use App\Models\Category;
use App\Models\Order;


class BuyerRfqController extends Controller
{
    /**
     * Список моих RFQ
     */
    public function index()
{
    $rfqs = Rfq::where('buyer_id', auth()->id())
        ->withCount([
            'offers as unread_offers_count' => function ($q) {
                $q->whereNull('buyer_viewed_at');
            }
        ])
        ->latest()
        ->get();

    return view('dashboard.buyer.rfqs.index', compact('rfqs'));
}

    /**
     * Форма создания RFQ
     */
    public function create()
    {
        $categories = Category::where('type', 'project')
                          ->orderBy('name')
                          ->get();

        return view('dashboard.buyer.rfqs.create', compact('categories'));
    }

    /**
     * Сохранение RFQ
     */
    public function store(StoreRFQRequest $request)
    {
        $data = $request->validated();

        $data['buyer_id'] = auth()->id();

        // Сохраняем RFQ
        $rfq = Rfq::create($data);

        // Сохраняем файл, если есть
        if ($request->hasFile('attachment')) {
    // удалить старый файл если нужно
    if($rfq->attachment_path){
        Storage::disk('public')->delete($rfq->attachment_path);
    }

    // сохраняем на публичном диске
    $rfq->attachment_path = $request->file('attachment')
        ->store('rfq_attachments', 'public');

    $rfq->save();
        }

        return redirect()
            ->route('buyer.rfqs.index')
            ->with('success', 'RFQ создан');
    }



    /**
 * Форма редактирования RFQ
 */
public function edit(Rfq $rfq)
{
    $this->authorize('update', $rfq);

    if ($rfq->offers()->count() > 0) {
        return redirect()->route('buyer.rfqs.index')
            ->with('error', 'You cannot edit this RFQ because it already has offers.');
    }

    $categories = Category::orderBy('name')->get();

    return view('dashboard.buyer.rfqs.edit', compact('rfq', 'categories'));
}

/**
 * Обновление RFQ
 */
public function update(StoreRFQRequest $request, Rfq $rfq)
{
    $this->authorize('update', $rfq);

    if ($rfq->offers()->count() > 0) {
        return redirect()->route('buyer.rfqs.index')
            ->with('error', 'You cannot update this RFQ because it already has offers.');
    }

    $data = $request->validated();

    $rfq->update($data);

    // Обновление файла, если есть
    if ($request->hasFile('attachment')) {
    // удалить старый файл если нужно
    if($rfq->attachment_path){
        Storage::disk('public')->delete($rfq->attachment_path);
    }

    // сохраняем на публичном диске
    $rfq->attachment_path = $request->file('attachment')
        ->store('rfq_attachments', 'public');

    $rfq->save();
    }

    return redirect()->route('buyer.rfqs.show', $rfq->id)
        ->with('success', 'RFQ updated successfully.');
}




    /**
     * Просмотр RFQ + офферы
     */
    public function show(Rfq $rfq)
{



    $this->authorize('view', $rfq);

    $rfq->load(['offers.supplier']);

    // 🔒 Помечаем как прочитанные ТОЛЬКО для buyer
    if (
        auth()->user()->role === 'buyer' &&
        $rfq->buyer_id === auth()->id()
    ) {
        $rfq->offers()
            ->whereNull('buyer_viewed_at')
            ->update(['buyer_viewed_at' => now()]);
    }

    return view('dashboard.buyer.rfqs.show', compact('rfq'));
}


    /**
     * Выбор победителя
     */
    public function acceptOffer(Request $request, Rfq $rfq)
{



    // Находим оффер среди офферов текущего RFQ
    $offer = $rfq->offers()->findOrFail($request->input('offer_id'));

    $offer->load('rfq'); // 🔹 важно

    

    // Проверка через полиси
    $this->authorize('acceptOffer', $offer);

    // Закрываем RFQ
    $rfq->update(['status' => 'closed']);

    // Отклоняем все офферы
    $rfq->offers()->update(['status' => 'rejected']);

    // Принимаем выбранный оффер
    $offer->update(['status' => 'accepted']);


    $shippingTemplate = \App\Models\ShippingTemplate::find($offer->shipping_template->id);
    
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


      // 🔹 Создаём заказ
    $buyer = $rfq->buyer;

    // Сумма товаров
    $itemsTotal = $offer->price * $rfq->quantity;

    $order = Order::create([
        'user_id' => $buyer->id,
        'rfq_offer_id' => $offer->id,
        'type' => 'rfq',
        'status' => 'pending',
        'total' => $offer->price,
        'delivery_price' => $offer->shipping_template->price ?? 0,
        'delivery_method' => $offer->shipping_template?->title,
        'first_name' => $buyer->name ?? '',
        'last_name' => $buyer->last_name ?? '',
        'country' => $buyer->country ?? '',
        'city' => $buyer->city ?? '',
        'region' => $buyer->region ?? '',
        'street' => $buyer->street ?? '',
        'postal_code' => $buyer->postal_code ?? '',
        'phone' => $buyer->phone ?? '',
        'notes' => $offer->comment ?? '',
    ]);

    // 🔹 ОБЯЗАТЕЛЬНО фиксируем первый статус
    $order->statusHistory()->create([
            'status' => 'pending',
            'comment' => 'Заказ создан покупателем',
        ]);

    // 🔹 Добавляем позицию заказа с lead_time
    $orderItem = $order->items()->create([
        'product_id' => null,
        'product_name' => $rfq->title,
        'price' => $itemsTotal,
        'quantity' => $rfq->quantity ?? 1,
        'lead_time_days' => $offer->delivery_days ?? 0,
    ]);


    \App\Models\OrderItemShipment::create([
        'order_id'       => $order->id,
        'shippable_type' => \App\Models\OrderItem::class,
        'shippable_id'   => $orderItem->id,
                
        'provider_type'  => $providerType,
        'provider_id'    => $providerId,
  
        // админ заполнит позже
        'weight'         => null,
        'length'         => null,
        'width'          => null,
        'height'         => null,

        'shipping_price' => 0,
        'delivery_time'  => null,
        'status'         => 'pending',
    ]);


    return redirect()->route('buyer.orders.edit', $order->id)
                     ->with('success', 'Заказ успешно создан. Пожалуйста, завершите оформление заказа.');
}

}
