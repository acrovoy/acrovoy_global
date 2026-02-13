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

// Проверка: доставка Acrovoy и цена 0
        $isAcrovoyPending = ($order['delivery_method'] === 'Delivery by Acrovoy' && ($order['delivery_price'] ?? 0) == 0);

@endphp


@if($isAcrovoyPending)
        <div class="p-3 mb-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded">
            Awaiting for the delivery price and delivery time from Acrovoy. After that supplier will be able to confirm the order.
        </div>
    @endif

{{-- ORDER CARD --}}
<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-medium">
            Products in the order:
        </h3>

        
    </div>

    {{-- Товары --}}
    <div class="">
        @foreach($order->items as $item)
            <div class="py-1 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img
                        src="{{ $item->product && $item->product->mainImage
                            ? asset('storage/' . $item->product->mainImage->image_path)
                            : asset('images/no-photo.png') }}"
                        class="w-12 h-12 rounded object-contain bg-gray-50 border"
                    >
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $item->product_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $item->quantity }} × {{ number_format($item->price, 2) }} $
                        </p>
                    </div>
                </div>

                <div class="font-semibold text-gray-900">
                    {{ number_format($item->price * $item->quantity, 2) }} $
                </div>
            </div>
        @endforeach
    </div>

 {{-- Доставка --}}
    <div class="flex justify-between text-sm text-gray-700 pt-3 mt-6 border-t">
        <span>
            DELIVERY: <span class="text-xs text-gray-400">({{ $order->delivery_method ?? '-' }})</span>
        </span>

        @if($order['delivery_method'] === 'Delivery by Acrovoy')
            <span class="font-semibold">0.00 $</span>
            @else
            {{ number_format($order->delivery_price ?? 0, 2) }} $
            @endif

       
    </div>


    

    {{-- Итого --}}
    <div class="text-right mt-3 text-lg font-semibold">
        Total: {{ number_format($order->total, 2) }} $
    </div>
</div>


@if($order['delivery_method'] === 'Delivery by Acrovoy' && $order['delivery_price'] > 0)
    <div class="mt-4 border border-gray-200 rounded-xl bg-white shadow-sm p-5">
        
        <h3 class="text-sm font-semibold text-gray-900 mb-4">
            Acrovoy Delivery Details
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm text-gray-700">
            
            {{-- Delivery Price --}}
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                    Delivery Cost
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ number_format($order['delivery_price'], 2) }} $
                </div>
            </div>

            {{-- Delivery Time --}}
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                    Delivery Time
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $order['delivery_time'] ?? '-' }} days
                </div>
            </div>

        </div>
    </div>
@endif



{{-- Отображение споров --}}
@if($order->disputes->count())
<div class="mt-6 border border-gray-200 rounded-lg bg-gray-50">

    <h3 class="pt-6 pr-6 pl-6 font-semibold text-lg">Disputes</h3>

    <div class="p-4 divide-y divide-gray-200">
    @foreach($order->disputes as $dispute)
        <div class="p-4 bg-white rounded-lg my-2 shadow-sm">

            {{-- Статус --}}
            <div class="flex justify-between items-center mb-3">
                <div class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    Статус:
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        @if($dispute->status === 'pending') bg-yellow-100 text-yellow-700
                        @elseif($dispute->status === 'supplier_offer') bg-blue-100 text-blue-700
                        @elseif($dispute->status === 'buyer_reject') bg-red-100 text-red-700
                        @elseif($dispute->status === 'rejected') bg-red-200 text-red-800
                        @elseif($dispute->status === 'resolved') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-600
                        @endif
                    ">
                        {{ __('dispute.status.' . $dispute->status) ?? ucfirst(str_replace('_', ' ', $dispute->status)) }}
                    </span>
                </div>

                <span class="text-xs text-gray-500">
                    {{ $dispute->created_at->format('d.M.y | H:i') }}
                </span>
            </div>

            {{-- Причина --}}
            <p class="text-sm text-gray-700 mb-1">
                <strong>Причина:</strong> {{ $dispute->reason }}
            </p>

            {{-- Запрос --}}
            <p class="text-sm text-gray-700 mb-2">
                <strong>Запрос:</strong>
                {{ __('dispute.action.' . $dispute->action) ?? ucfirst($dispute->action) }}
            </p>

            {{-- Ответ продавца --}}
            @if($dispute->supplier_comment)
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
                    <strong>Ответ продавца:</strong><br>
                    {{ $dispute->supplier_comment }}
                </div>
            @endif

            {{-- Комментарий покупателя --}}
            @if($dispute->buyer_comment)
                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded text-sm">
                    <strong>Комментарий покупателя:</strong><br>
                    {{ $dispute->buyer_comment }}
                </div>
            @endif

            {{-- Решение администратора --}}
            @if($dispute->admin_comment)
                <div class="mt-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded text-sm">
                    <strong>Решение администратора:</strong><br>
                    {{ $dispute->admin_comment }}
                </div>
            @endif

            {{-- Вложение --}}
            @if($dispute->attachment)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $dispute->attachment) }}"
                       target="_blank"
                       class="text-blue-600 hover:underline text-sm">
                        Посмотреть вложение
                    </a>
                </div>
            @endif

            {{-- КНОПКИ --}}
            <div class="mt-4 flex flex-wrap gap-3">

                {{-- pending --}}
                @if($dispute->status === 'pending')
                    <form method="POST" action="{{ route('buyer.disputes.cancel', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Отменить спор
                        </button>
                    </form>

                    <a href="{{ route('buyer.support.chat', $dispute->id) }}"
                       class="text-sm text-blue-600 hover:text-blue-800 underline">
                        Связаться с поддержкой
                    </a>
                @endif

                {{-- supplier_offer --}}
                @if($dispute->status === 'supplier_offer')
                    <form method="POST" action="{{ route('buyer.disputes.accept', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-green-600 hover:text-green-800 underline">
                            Принять решение
                        </button>
                    </form>

                    <form method="POST" action="{{ route('buyer.disputes.reject', $dispute->id) }}" class="flex flex-col gap-2 w-full md:w-auto">
                        @csrf
                        @method('PUT')

                        <textarea name="buyer_comment" rows="2"
                                  placeholder="Комментарий (необязательно)"
                                  class="border border-gray-300 rounded px-2 py-1 text-sm"></textarea>

                        <button type="submit"
                                class="text-sm text-red-600 hover:text-red-800 underline self-start">
                            Отклонить решение
                        </button>
                    </form>
                @endif

                {{-- rejected --}}
                @if($dispute->status === 'rejected')
                    <form method="POST" action="{{ route('buyer.disputes.appeal', $dispute->id) }}" class="flex flex-col gap-2 w-full md:w-auto">
                        @csrf
                        @method('PUT')

                        <textarea name="buyer_comment" rows="2"
                                  placeholder="Комментарий к апелляции (необязательно)"
                                  class="border border-gray-300 rounded px-2 py-1 text-sm"></textarea>

                        <button type="submit"
                                class="text-sm text-blue-600 hover:text-blue-800 underline self-start">
                            Подать апелляцию
                        </button>
                    </form>

                    <form method="POST" action="{{ route('buyer.disputes.close', $dispute->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="text-sm text-green-600 hover:text-green-800 underline">
                            Закрыть спор
                        </button>
                    </form>
                @endif

            </div>

        </div>
    @endforeach
    </div>

</div>
@endif








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




{{-- Таймлайн статусов заказа --}}
<div class="mt-4 ml-4">
    <h4 class="font-semibold mb-3">История заказа</h4>

    <ol class="relative border-l border-gray-300">
        @forelse($order->statusHistory as $history)
            <li class="mb-6 ml-6">
                {{-- Точка --}}
                <span class="absolute -left-3 flex items-center justify-center
                             w-6 h-6 rounded-full
                             @if($history->status === 'cancelled') bg-red-500
                             @elseif($history->status === 'completed') bg-green-600
                             @else bg-blue-600
                             @endif
                             text-white text-sm">
                    ✓
                </span>

                {{-- Статус --}}
                <h5 class="font-medium">
                    {{ __('order.status.' . $history->status) }}
                </h5>

                {{-- Дата --}}
                <time class="block text-sm text-gray-500">
                    {{ $history->created_at->format('d.m.Y H:i') }}
                </time>

                {{-- Комментарий --}}
                @if($history->comment)
                    <p class="mt-1 text-gray-600">
                        {{ $history->comment }}
                    </p>
                @endif
            </li>
        @empty
            <li class="ml-6 text-gray-500">
                История статусов пока отсутствует
            </li>
        @endforelse
    </ol>
</div>

{{-- Контакты и адрес --}}
<div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
    <h3 class="font-semibold text-gray-900 mb-3">
        Контактная информация
    </h3>

    

    @if($order->country && $order->city && $order->street)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div>
                <span class="text-gray-500">Имя</span>
                <div class="text-gray-900 font-medium">
                    {{ $order->first_name }} {{ $order->last_name ?? '' }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Телефон</span>
                <div class="text-gray-900 font-medium">
                    {{ $order->phone }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Страна</span>
                <div class="text-gray-900">
                    {{ $order->country }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Город</span>
                <div class="text-gray-900">
                    {{ $order->city }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Регион</span>
                <div class="text-gray-900">
                    {{ $order->region }}
                </div>
            </div>

            <div>
                <span class="text-gray-500">Почтовый индекс</span>
                <div class="text-gray-900">
                    {{ $order->postal_code }}
                </div>
            </div>

            <div class="sm:col-span-2">
                <span class="text-gray-500">Улица</span>
                <div class="text-gray-900">
                    {{ $order->street }}
                </div>
            </div>
        </div>
    @else
        <div class="text-sm text-gray-500">
            Адрес не указан.
        </div>
    @endif
</div>

{{-- Actions --}}
<div class="flex flex-wrap gap-2 mb-6">

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

    {{-- Кнопка открыть модалку --}}
    <div x-data="{ editAddressModalOpen: false }" class="inline-flex">

    {{-- Кнопка открыть модалку --}}
    @if($canEditAddress)
        <a href="#"
           @click.prevent="editAddressModalOpen = true"
           class="px-3 py-1.5 text-sm border border-yellow-300 text-yellow-700 rounded-md hover:bg-yellow-50 hover:border-yellow-400">
            Edit Address
        </a>
    @endif

    {{-- Подключаем модалку --}}
    @include('dashboard.buyer.orders.modals.edit_address_modal', ['order' => $order])

</div>
  

    {{-- Редактировать заказ --}}
    @if($order->status === 'pending')
        <a href="{{ route('buyer.orders.edit', $order->id) }}"
           class="px-3 py-1.5 text-sm
                  border border-green-300 text-green-700
                  rounded-md
                  hover:bg-green-50 hover:border-green-400">
            Edit Order
        </a>
    @else
        <button class="px-3 py-1.5 text-sm
                       border border-gray-300 text-gray-400
                       rounded-md cursor-not-allowed"
                disabled>
            Edit Order
        </button>
    @endif

    @if(!empty($order->invoice_file))
        <a href="{{ route('buyer.orders.invoice', $order->id) }}"
           target="_blank"
           class="px-3 py-1.5 text-sm
                  border border-blue-300 text-blue-700
                  rounded-md
                  hover:bg-blue-50 hover:border-blue-400">
            Download Invoice
        </a>
    @else
        <button class="px-3 py-1.5 text-sm
                       border border-gray-300 text-gray-400
                       rounded-md cursor-not-allowed"
                disabled>
            Invoice not uploaded by the supplier yet
        </button>
    @endif

</div>

{{-- Tracking Number --}}
@if(!empty($order->tracking_number))
    <div class="border rounded p-3 bg-gray-50 mt-4">
        <h3 class="font-semibold mb-1">Tracking Number</h3>
        <input type="text"
               readonly
               value="{{ $order->tracking_number }}"
               class="w-full border rounded px-3 py-2 text-sm bg-gray-100 cursor-text"
               onclick="this.select(); document.execCommand('copy');"
               title="Click to copy">
        <p class="text-gray-500 text-xs mt-1">Click on the field to copy the tracking number</p>
    </div>
@endif

{{-- Модальное окно "Оставить отзыв" --}}
<div id="reviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-lg w-full relative">
        <button onclick="closeModal('reviewModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Оставить отзыв</h3>

        <form action="{{ route('buyer.orders.review.store', $order->id) }}" method="POST">
            @csrf

            @foreach($order->items as $item)
            <div class="mb-6">
                <p class="font-semibold">{{ $item->product_name }}</p>

                {{-- Общая оценка --}}
                <label>Общая оценка:</label>
                <div class="flex space-x-1 rating-stars" data-input="rating-{{ $item->id }}">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star text-gray-300 cursor-pointer text-2xl" data-value="{{ $i }}">&#9733;</span>
                    @endfor
                    <input type="hidden" name="rating[{{ $item->id }}]" id="rating-{{ $item->id }}" value="0">
                </div>

                {{-- Соответствие карточке --}}
                <label class="mt-2">Соответствие карточке:</label>
                <div class="flex space-x-1 rating-stars" data-input="match_rating-{{ $item->id }}">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star text-gray-300 cursor-pointer text-2xl" data-value="{{ $i }}">&#9733;</span>
                    @endfor
                    <input type="hidden" name="match_rating[{{ $item->id }}]" id="match_rating-{{ $item->id }}" value="0">
                </div>

                {{-- Текстовый отзыв к товару --}}
                <textarea name="comment[{{ $item->id }}]" rows="3" placeholder="Ваш отзыв о товаре" class="w-full border rounded px-3 py-2 mt-2"></textarea>

                
            </div>
            @endforeach

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('reviewModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Отправить</button>
            </div>
        </form>
    </div>
</div>


{{-- Модальное окно "Жалоба / Возврат / Спор" --}}
<div id="disputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-lg w-full relative">
        <button onclick="closeModal('disputeModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Жалоба / Возврат / Спор</h3>
        <form action="{{ route('buyer.orders.dispute.store', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block mb-2">Причина спора</label>
            <textarea name="reason" rows="4" placeholder="Опишите проблему" class="w-full border rounded px-3 py-2 mb-4"></textarea>
            <label class="block mb-2">Желаемое действие</label>
            <select name="action" class="w-full border rounded px-3 py-2 mb-4">
                <option value="return">Возврат</option>
                <option value="compensation">Компенсация</option>
                <option value="exchange">Обмен</option>
            </select>
            <label class="block mb-2">Прикрепить файл (опционально)</label>
            <input type="file" name="attachment" class="w-full border rounded px-3 py-2 mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('disputeModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Отправить</button>
            </div>
        </form>
    </div>
</div>

{{-- Модальное окно "Оценить продавца" --}}
<div id="supplierReviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-md w-full relative">
        <button onclick="closeModal('supplierReviewModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-xl font-bold mb-4">Оценить продавца</h3>

        <form action="{{ route('buyer.orders.supplier.review.store', $order->id) }}" method="POST">
            @csrf

            <div class="flex space-x-1 rating-stars mb-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star text-gray-300 cursor-pointer text-3xl" data-value="{{ $i }}">&#9733;</span>
                @endfor
                <input type="hidden" name="rating" class="rating-input" value="0">
            </div>

            <label class="block mb-2">Комментарий:</label>
            <textarea name="comment" rows="3" class="border rounded w-full px-2 py-1 mb-4" placeholder="Ваш отзыв о продавце"></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('supplierReviewModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Отправить</button>
            </div>
        </form>
    </div>
</div>



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
