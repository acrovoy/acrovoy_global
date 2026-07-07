@extends('dashboard.layout')
@section('dashboard-content')

<a href="{{ route('buyer.orders.index') }}"
    class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to orders
</a>
 
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-semibold">
            Order #{{ $order->id }} 
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Review your order information, track status updates, manage disputes, and handle address or invoice details.
        </p>
       
    </div>

    <span class="px-3 py-1 rounded text-sm
        @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
        @elseif($order['status'] === 'confirmed') bg-green-100 text-blue-800
        @elseif($order['status'] === 'paid') bg-blue-100 text-blue-800
        @elseif($order['status'] === 'shipped') bg-green-100 text-green-800
        @else bg-gray-100 text-gray-800
        @endif
        ">
        {{ ucfirst($order['status']) }}
    </span>
</div>

{{-- Flash messages --}}
@foreach (['success' => 'green', 'error' => 'red', 'info' => 'yellow'] as $type => $color)
    @if(session($type))
        <div class="mb-4 px-4 py-3 rounded-lg bg-{{ $color }}-100 text-{{ $color }}-800 text-sm">
            {{ session($type) }}
        </div>
    @endif
@endforeach

@php
        
    $shipment = $order->items->first()?->shipment;
    
@endphp

        @if($shipment?->provider_type === \App\Models\LogisticCompany::class && ($order['delivery_price'] ?? 0) == 0)
            <div class="p-3 mb-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded">
            Awaiting for the delivery price and delivery time from the Shipping Provider. After that the supplier of the product will be able to confirm the order.
            </div>
        @elseif($shipment?->provider_type === \App\Models\Supplier::class)
            
            
        @else
           
        @endif





{{-- Products --}}
@include('dashboard.buyer.orders.partials.products', ['order' => $order, 'shipment' => $shipment])

{{-- Acrovoy delivery details --}}
@include('dashboard.buyer.orders.partials.acrovoy_delivery_details', ['order' => $order])

{{-- Disputes --}}
@include('dashboard.buyer.orders.partials.disputes', ['order' => $order])

{{-- Кнопки отзыв и спор (только если заказ завершён) --}}
@php
    // Проверяем, есть ли открытые споры (не resolved)
    $hasOpenDispute = $order->disputes->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])->count() > 0;

    // 🔹 Проверка отзывов на товары (только обычные товары)
    $hasReviewed = $order->items->filter(fn($item) => $item->product !== null)
        ->filter(fn($item) => $item->product->reviews()
            ->where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->exists()
        )
        ->count() > 0;

    // Проверяем, есть ли уже отзыв пользователя о продавце этого заказа
    $supplierId = null;

    $productWithSupplier = $order->items->firstWhere(fn($item) => $item->product?->supplier_id !== null);
    if ($productWithSupplier) {
        $supplierId = $productWithSupplier->product->supplier_id;
    } 
    elseif ($order->items->first()?->order?->rfqOffer?->supplier_id) {
        $supplierId = $order->items->first()->order->rfqOffer->supplier_id;
    }

    $hasReviewedSupplier = $supplierId
        ? \App\Models\SupplierReview::where('supplier_id', $supplierId)
            ->where('order_id', $order->id)
            ->where('user_id', auth()->id())
            ->exists()
        : false;
@endphp

@if($order->status === 'completed')
<div class="mt-4 flex flex-wrap gap-4">

    {{-- Отзыв о товаре --}}
    @if($order->items->whereNotNull('product')->count() > 0)
        <button
            onclick="openModal('reviewModal')"
            class="text-sm font-medium underline
                {{ $hasOpenDispute || $hasReviewed
                    ? 'text-gray-400 cursor-not-allowed no-underline'
                    : 'text-green-600 hover:text-green-800' }}"
            @if($hasOpenDispute || $hasReviewed) disabled @endif>
            Оставить отзыв
        </button>
    @endif

    {{-- Отзыв о продавце --}}
    <button
        onclick="openSupplierReviewModal()"
        class="text-sm font-medium underline
            {{ $hasOpenDispute || $hasReviewedSupplier
                ? 'text-gray-400 cursor-not-allowed no-underline'
                : 'text-blue-600 hover:text-blue-800' }}"
        @if($hasOpenDispute || $hasReviewedSupplier) disabled @endif>
        Оценить продавца
    </button>

    {{-- Спор --}}
    <button
        onclick="openModal('disputeModal')"
        class="text-sm font-medium underline
            {{ $hasOpenDispute
                ? 'text-gray-400 cursor-not-allowed no-underline'
                : 'text-red-600 hover:text-red-800' }}"
        @if($hasOpenDispute) disabled @endif>
        Жалоба / Возврат / Спор
    </button>

</div>
@endif


{{-- Status TimeLine --}}
@include('dashboard.buyer.orders.partials.status-timeline', ['order' => $order])

{{-- Delivery Address and Contact --}}
@include('dashboard.buyer.orders.partials.address_contact', ['order' => $order])

{{-- Actions --}}
<div class="flex flex-wrap gap-2 mb-6">

    {{-- Cancel Order --}}
    @if($canCancel)
        <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST">
            @csrf
            <button type="submit"
                    class="px-3 py-1.5 text-sm
                           border border-red-300 text-red-600
                           rounded-md
                           hover:bg-red-50 hover:border-red-400">
                Cancel Order
            </button>
        </form>
    @endif

    {{-- Edit Order --}}
    @if($order->status === 'pending')
        <a href="{{ route('buyer.orders.edit', $order->id) }}"
           class="px-3 py-1.5 text-sm
                  border border-green-300 text-green-700
                  rounded-md
                  hover:bg-green-50 hover:border-green-400">
            Edit Order
        </a>
    @else
        <!-- <button class="px-3 py-1.5 text-sm
                       border border-gray-300 text-gray-400
                       rounded-md cursor-not-allowed"
                disabled>
            Edit Order
        </button> -->
    @endif
   
</div>

<!-- {{-- Tracking Number --}}
@include('dashboard.buyer.orders.partials.tracking_number', ['order' => $order]) -->

{{-- Reviews modal --}}
@include('dashboard.buyer.orders.modals.review', ['order' => $order])

{{-- Dispute modal --}}
@include('dashboard.buyer.orders.modals.dispute', ['order' => $order])

{{-- Supplier review modal --}}
@include('dashboard.buyer.orders.modals.supplier_review', ['order' => $order])


{{-- Скрипт для открытия/закрытия модалок и работы звездочек --}}
<script>
/* Универсальная инициализация звездочек */
function initRatingStars(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.querySelectorAll('.rating-stars').forEach(group => {
        // Определяем привязанный input
        let input;
        const inputId = group.dataset.input; // для товаров
        if (inputId) {
            input = document.getElementById(inputId);
        } else {
            input = group.querySelector('input.rating-input'); // для продавца
        }

        const stars = group.querySelectorAll('.star');

        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                stars.forEach((s, i) => s.classList.toggle('text-orange-500', i < star.dataset.value));
            });

            star.addEventListener('mouseout', () => {
                const val = parseInt(input.value) || 0;
                stars.forEach((s, i) => s.classList.toggle('text-orange-500', i < val));
            });

            star.addEventListener('click', () => {
                input.value = star.dataset.value;
                stars.forEach((s, i) => s.classList.toggle('text-orange-500', i < input.value));
            });
        });
    });
}

/* Открыть любую модалку */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Инициализация звездочек только для открытой модалки
    initRatingStars(modalId);
}

/* Закрыть любую модалку */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

/* Специальная функция для открытия модалки оценки продавца */
function openSupplierReviewModal() {
    openModal('supplierReviewModal');
}
</script>

<style>
.star {
    color: #d1d5db; /* серый по умолчанию */
    cursor: pointer;
}

.star.hovered,
.star.selected,
.star.text-orange-500 {
    color: #F59E0B; /* оранжевый */
}
</style>

@endsection
