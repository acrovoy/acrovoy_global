@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-4">Заказ #{{ $order->id }}</h2>

 {{-- Flash messages --}}
@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

@if(session('info'))
    <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4">
        {{ session('info') }}
    </div>
@endif

<div class="bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold mb-2">Статус заказа: 
        <span class="font-bold {{ $order->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
            {{ ucfirst($order->status) }}
        </span>
    </h3>

    {{-- Товары --}}
    <div class="space-y-2">
        @foreach($order->items as $item)
        <div class="flex justify-between items-center border-b pb-2">
            <div class="flex items-center gap-2">
                <img src="{{ 
                $item->product && $item->product->mainImage 
                    ? asset('storage/' . $item->product->mainImage->image_path) 
                    : asset('images/no-photo.png') 
            }}"
                 alt="{{ $item->product_name }}"
                 class="w-12 h-12 object-contain rounded">
                <div>
                    <p class="font-semibold">{{ $item->product_name }}</p>
                    <p class="text-gray-500 text-sm">Количество: {{ $item->quantity }}</p>
                    <p class="text-gray-500 text-sm">Цена за единицу: {{ number_format($item->price, 2) }}₴</p>
                </div>
            </div>
            <div class="font-semibold">{{ number_format($item->price * $item->quantity, 2) }}₴</div>
        </div>
        @endforeach
    </div>

 {{-- Доставка --}}
    <div class="flex justify-between items-center border-t pt-2 mt-2 text-gray-700 font-medium">
        <span>Доставка ({{ $order->delivery_method ?? '-' }})</span>
        <span>{{ number_format($order->delivery_price ?? 0, 2) }}₴</span>
    </div>

    {{-- Итого --}}
    <div class="text-right mt-2 font-bold">Итого: {{ number_format($order->total, 2) }}₴</div>
</div>

{{-- Отображение споров --}}

@if($order->disputes->count())
    <div class="mt-6 border rounded-lg p-4 bg-red-50">
        <h4 class="font-semibold mb-3 text-red-700">
            Споры по заказу
        </h4>

        @foreach($order->disputes as $dispute)
            <div class="border rounded bg-white p-4 mb-3">

                {{-- Статус --}}
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium">
                        Статус:
                        <span class="
                            @if($dispute->status === 'pending') text-yellow-600
                            @elseif($dispute->status === 'supplier_offer') text-blue-600
                            @elseif($dispute->status === 'buyer_reject') text-red-600
                            @elseif($dispute->status === 'resolved') text-green-600
                            @else text-gray-600
                            @endif
                        ">
                            {{ __('dispute.status.' . $dispute->status) ?? ucfirst(str_replace('_', ' ', $dispute->status)) }}
                        </span>
                    </span>

                    <span class="text-xs text-gray-500">
                        {{ $dispute->created_at->format('d.m.Y H:i') }}
                    </span>
                </div>

                {{-- Причина --}}
                <p class="text-sm mb-1">
                    <strong>Причина:</strong> {{ $dispute->reason }}
                </p>

                {{-- Запрошенное действие --}}
                <p class="text-sm mb-1">
                    <strong>Запрос:</strong>
                    {{ __('dispute.action.' . $dispute->action) ?? ucfirst($dispute->action) }}
                </p>

                {{-- Ответ продавца --}}
                @if($dispute->supplier_comment)
                    <div class="mt-2 p-3 bg-gray-100 rounded text-sm">
                        <strong>Ответ продавца:</strong><br>
                        {{ $dispute->supplier_comment }}
                    </div>
                @endif

                {{-- Комментарий покупателя --}}
                @if($dispute->buyer_comment)
                    <div class="mt-2 p-3 bg-red-100 rounded text-sm">
                        <strong>Комментарий покупателя:</strong><br>
                        {{ $dispute->buyer_comment }}
                    </div>
                @endif

                {{-- Ответ администратора --}}
@if($dispute->admin_comment)
    <div class="mt-2 p-3 bg-yellow-100 border-l-4 border-yellow-500 rounded text-sm">
        <strong>Решение администратора:</strong><br>
        {{ $dispute->admin_comment }}
    </div>
@endif

                {{-- Файл --}}
                @if($dispute->attachment)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $dispute->attachment) }}"
                           target="_blank"
                           class="text-blue-600 hover:underline text-sm">
                            Посмотреть вложение
                        </a>
                    </div>
                @endif

                {{-- КНОПКИ ДЛЯ ПОКУПАТЕЛЯ --}}
                <div class="mt-3 flex gap-2 flex-wrap">

                    {{-- Спор ещё не решён — можно отменить --}}
                    @if($dispute->status === 'pending')
                        <form method="POST" action="{{ route('buyer.disputes.cancel', $dispute->id) }}">
                            @csrf
                            @method('PUT')
                            <button class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded hover:bg-gray-600">
                                Отменить спор
                            </button>
                        </form>

                        <a href="{{ route('buyer.support.chat', $dispute->id) }}"
                           class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            Связаться с поддержкой
                        </a>
                    @endif

                    {{-- Продавец предложил решение --}}
                    @if($dispute->status === 'supplier_offer')
                        <div class="mt-3 flex gap-2">
                            <form method="POST" action="{{ route('buyer.disputes.accept', $dispute->id) }}">
                                @csrf
                                @method('PUT')
                                <button class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                    Принять решение
                                </button>
                            </form>

                            <form method="POST" action="{{ route('buyer.disputes.reject', $dispute->id) }}" class="flex gap-2 flex-col">
                                @csrf
                                @method('PUT')

                                <textarea name="buyer_comment" rows="2" placeholder="Комментарий (необязательно)"
                                          class="border rounded px-2 py-1 text-sm w-full"></textarea>

                                <button type="submit"
                                        class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                    Отклонить решение
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Покупатель может подать апелляцию или закрыть спор если продавец отклонил --}}
@if($dispute->status === 'rejected')
    <div class="mt-3 flex gap-2 flex-wrap">

        {{-- Апелляция к администратору --}}
        <form method="POST" action="{{ route('buyer.disputes.appeal', $dispute->id) }}" class="flex flex-col gap-2 w-full md:w-auto">
            @csrf
            @method('PUT')

            <textarea name="buyer_comment" rows="2" placeholder="Комментарий к апелляции (необязательно)"
                      class="border rounded px-2 py-1 text-sm w-full"></textarea>

            <button type="submit"
                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                Подать апелляцию
            </button>
        </form>

        {{-- Закрыть спор --}}
        <form method="POST" action="{{ route('buyer.disputes.close', $dispute->id) }}">
            @csrf
            @method('PUT')
            <button class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                Закрыть спор
            </button>
        </form>

    </div>
@endif

                </div>

            </div>
        @endforeach
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

    // Пытаемся получить supplier_id из обычного товара
    $productWithSupplier = $order->items->firstWhere(fn($item) => $item->product?->supplier_id !== null);
    if ($productWithSupplier) {
        $supplierId = $productWithSupplier->product->supplier_id;
    } 
    // Если нет, пробуем получить из RFQ
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
                    <div class="mt-4 flex flex-wrap gap-3">

                        @if($order->items->whereNotNull('product')->count() > 0)
                            <button 
                                onclick="openModal('reviewModal')" 
                                class="px-4 py-2 rounded text-white
                                    {{ $hasOpenDispute || $hasReviewed 
                                        ? 'bg-gray-300 cursor-not-allowed hover:bg-gray-300' 
                                        : 'bg-green-500 hover:bg-green-600' }}"
                                @if($hasOpenDispute || $hasReviewed) disabled @endif>
                                Оставить отзыв
                            </button>
                        @endif

                        
                        <button
                            onclick="openSupplierReviewModal()"
                            class="px-4 py-2 rounded text-white
                                {{ $hasOpenDispute || $hasReviewedSupplier 
                                    ? 'bg-gray-300 cursor-not-allowed hover:bg-gray-300' 
                                    : 'bg-blue-500 hover:bg-blue-600' }}"
                            @if($hasOpenDispute || $hasReviewedSupplier) disabled @endif>
                            Оценить продавца
                        </button>


                        <button 
                            onclick="openModal('disputeModal')" 
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600
                                {{ $hasOpenDispute ? 'opacity-50 cursor-not-allowed hover:bg-red-500' : '' }}"
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
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold mb-2">Контактная информация</h3>
    @php
        $address = $order->user->addresses()->where('is_default', true)->first();
    @endphp
    @if($address)
        <p><strong>Имя:</strong> {{ $order->first_name }} {{ $address->last_name ?? '' }}</p>
        <p><strong>Страна:</strong> {{ $order->country }}</p>
        <p><strong>Город:</strong> {{ $order->city }}</p>
        <p><strong>Регион:</strong> {{ $order->region }}</p>
        <p><strong>Улица:</strong> {{ $order->street }}</p>
        <p><strong>Почтовый индекс:</strong> {{ $order->postal_code }}</p>
        <p><strong>Телефон:</strong> {{ $order->phone }}</p>
    @else
        <p>Адрес не указан.</p>
    @endif
</div>

{{-- Actions --}}
<div class="flex flex-wrap gap-3 mb-6">

    @if($canCancel)
        <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST">
            @csrf
            <button type="submit"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Cancel Order
            </button>
        </form>
    @endif

    @if($canEditAddress)
        <a href="{{ route('buyer.orders.edit-address', $order->id) }}"
           class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Edit Address
        </a>
    @endif

    {{-- Редактировать заказ --}}
    @if($order->status === 'pending')
        <a href="{{ route('buyer.orders.edit', $order->id) }}"
           class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Edit Order
        </a>
    @else
        <button class="px-4 py-2 bg-gray-300 text-white rounded cursor-not-allowed" disabled>
            Edit Order
        </button>
    @endif

    @if(!empty($order->invoice_file))
        <div>
            <a href="{{ route('buyer.orders.invoice', $order->id) }}"
               target="_blank"
               class="inline-block px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                Download Invoice
            </a>
        </div>
    @else
        <button class="inline-block px-3 py-1.5 bg-gray-300 text-white rounded text-sm cursor-not-allowed" disabled>
            Invoice not uploaded by the seller yet
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
