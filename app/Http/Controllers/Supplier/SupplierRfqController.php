<?php

namespace App\Http\Controllers\Supplier;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\RfqOffer;
use App\Models\ShippingTemplate;



class SupplierRfqController extends Controller
{
     /**
     * Список доступных RFQ для производителя
     */
    public function index()
{
    $user = auth()->user();
    $supplier = $user->supplier;
    $supplierId = $supplier->id ?? null;

    // 🔹 Дебаг
    info('User info', ['user_id' => $user->id, 'role' => $user->role]);
    info('Supplier info', ['supplier' => $supplier, 'supplier_id' => $supplierId]);

    $rfqs = Rfq::with(['category', 'offers'])
        ->latest()
        ->get();

    // 🔹 Посмотреть какие RFQ вообще грузятся
    info('Loaded RFQs', ['count' => $rfqs->count(), 'ids' => $rfqs->pluck('id')]);

    // Фильтруем RFQ
    $rfqs = Rfq::with([
        'category',
        'offers' => function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
    ])
    ->latest()
    ->get()
    ->filter(function ($rfq) use ($user, $supplierId) {
        if ($rfq->status === 'active') {
            return $user->can('view', $rfq);
        }

        if ($rfq->status === 'closed') {
            return $rfq->offers
                ->where('status', 'accepted')
                ->isNotEmpty();
        }

        return false;
    });

// 🔔 Добавляем счётчик непрочитанных статусов
$rfqs->each(function ($rfq) use ($supplierId) {
    // Берём оффер текущего supplier
    $offer = $rfq->offers
        ->where('supplier_id', $supplierId)
        ->first();

    if ($offer) {
        // Если оффер ещё не просмотрен
        if (in_array($offer->status, ['accepted', 'rejected']) && $offer->supplier_viewed_at === null) {
            $rfq->offer_status_badge = $offer->status; // 'accepted' или 'rejected'
        } else {
            $rfq->offer_status_badge = null; // бейдж не показываем
        }
    } else {
        $rfq->offer_status_badge = null;
    }
});

    return view('dashboard.manufacturer.rfqs.index', compact('rfqs'));
}



    
    /**
     * Просмотр конкретного RFQ
     */
    /**
 * Просмотр конкретного RFQ для производителя
 */
public function show(Rfq $rfq)
{
    // Проверка доступа через полиси
    $this->authorize('view', $rfq);

    // текущий supplier
    $supplierId = auth()->user()->supplier->id;

    // Помечаем офферы как просмотренные supplier'ом
    $rfq->offers()
        ->where('supplier_id', $supplierId)
        ->whereNull('supplier_viewed_at')
        ->whereIn('status', ['accepted', 'rejected'])
        ->update(['supplier_viewed_at' => now()]);

    // Подгружаем офферы, категорию и автора RFQ (покупателя)
    $rfq->load(['offers.supplier', 'category', 'buyer']);

    $shippingTemplates = ShippingTemplate::where(function ($query) {
        $query->where('manufacturer_id', auth()->id())
              ->orWhere('id', 1);
    })
    ->with('translations')
    ->get();

    return view('dashboard.manufacturer.rfqs.show', compact('rfq', 'shippingTemplates'));
}

    /**
     * Отправка предложения
     */
    public function storeOffer(Request $request, Rfq $rfq)
{
    // Полиси проверяет: может ли текущий пользователь сделать оффер
    $this->authorize('sendOffer', $rfq);

    $data = $request->validate([
        'price'         => 'required|numeric|min:0',
        'delivery_days' => 'nullable|integer|min:1',
        'comment'       => 'nullable|string|max:2000',
        'shipping_template_id' => 'nullable|exists:shipping_templates,id',
    ]);

    // Проверяем, чтобы производитель не сделал оффер дважды
    if ($rfq->offers()->where('supplier_id', auth()->id())->exists()) {
        return back()->with('error', 'You have already made an offer for this RFQ.');
    }

    $data['rfq_id'] = $rfq->id;
    $data['supplier_id'] = auth()->user()->supplier->id;
    $data['status'] = 'pending';

    RfqOffer::create($data);

    return back()->with('success', 'Your offer has been submitted.');
}
}
